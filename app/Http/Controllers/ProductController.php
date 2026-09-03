<?php

namespace App\Http\Controllers;

use App\Exceptions\ExcelImportValidationException;
use App\Exports\ProductsExport;
use App\Imports\ProductImport;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductEditLog;
use App\Models\ProductImage;
use App\Models\ProductSpecificationValue;
use App\Models\ProductWorkflow;
use App\Models\ProductWorkflowAction;
use App\Models\Specification;
use App\Models\Status;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Services\OnlineProductExportDataService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductController extends Controller
{
    private const ONLINE_REQUIRED_SPECIFICATIONS = ['Weight', 'Length', 'Width', 'Height', 'Size'];

    private const STAND_REQUIRED_SPECIFICATIONS = ['Weight'];

    private const DEFAULT_DESCRIPTION_MM = 'PRO 1 Global Home Center မှ အရည်အသွေးကောင်းမွန် သော ပစ္စည်းများကိုသာ ပစ္စည်းမှန်စျေးနှုန်းမှန်ကန်စွာ ရောင်းချသဖြင့် ယုံကြည်စိတ်ချစွာ ၀ယ်ယူနိုင်ပါသည်။';

    private const DEFAULT_DESCRIPTION_EN = 'Shop for variety of high quality products with reasonable price at PRO 1 Global, leading provider for construction and home improvement products.';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->productList($request);
    }

    /**
     * Display products assigned to one workflow on a dedicated page.
     */
    public function workflowProducts(Request $request, string $channel)
    {
        abort_unless(in_array($channel, ['stand', 'online'], true), 404);

        return $this->productList($request, $channel);
    }

    private function productList(Request $request, ?string $workflowChannel = null, ?OnlineProductExportDataService $exportDataService = null)
    {
        $exportDataService ??= app(OnlineProductExportDataService::class);

        $keyword = trim((string) $request->query('keyword', ''));
        $statusId = $request->query('status_id');
        $brand = trim((string) $request->query('brand', ''));
        $stage = trim((string) $request->query('stage', ''));
        $onlineMonth = trim((string) $request->query('online_month', ''));
        $createdDate = trim((string) $request->query('created_date', ''));
        $categoryId = $request->query('category_id');
        $allowedStages = ['ongoing', 'checked', 'exported', 'finished'];

        $hasSearchFilters = $keyword !== ''
            || filled($statusId)
            || $brand !== ''
            || in_array($stage, $allowedStages, true)
            || ($workflowChannel === 'online' && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $onlineMonth))
            || preg_match('/^\d{4}-\d{2}-\d{2}$/', $createdDate)
            || $categoryId;

        $currentBranchId = $request->user()->branch_id;
        $currentBranch = $request->user()->branch;
        $activeBranchCount = $workflowChannel === 'stand'
            ? Branch::where('status_id', 3)->count()
            : 0;
        $productListTitle = match ($workflowChannel) {
            'stand' => 'Stand Products',
            'online' => 'Online Products',
            default => 'All Products',
        };

        $productsQuery = Product::with(['country', 'status', 'user'])
            ->when($workflowChannel, function ($query) use ($workflowChannel) {
                $query->whereExists(function ($query) use ($workflowChannel) {
                    $query->selectRaw('1')
                        ->from('product_workflows')
                        ->join('workflows', 'workflows.id', '=', 'product_workflows.workflow_id')
                        ->whereColumn('product_workflows.product_id', 'products.id')
                        ->where('workflows.slug', 'like', '%'.$workflowChannel.'%');
                });
            })
            ->when($workflowChannel && ! $hasSearchFilters, function ($query) use ($workflowChannel, $request) {
                $userId = auth()->id();
                $isStandViewer = $workflowChannel === 'stand'
                    && $request->user()->hasRoles(['Viewer']);

                $query->where(function ($query) use ($userId, $isStandViewer) {
                    $query->where('user_id', $userId)
                        ->orWhere(function ($query) {
                            $query->canAction();
                        });

                    if ($isStandViewer) {
                        $query->orWhere(function ($query) {
                            $query->visibleToStandViewerAfterChecked();
                        });
                    }
                });
            })
            ->when($currentBranchId, function ($query) use ($currentBranchId) {
                $query->withMax([
                    'printRecords as current_branch_last_printed_at' => fn ($query) => $query
                        ->where('branch_id', $currentBranchId)
                        ->where('status', 'printed'),
                ], 'printed_at');
                $query->withMax([
                    'printRecords as current_branch_last_printed_version' => fn ($query) => $query
                        ->where('branch_id', $currentBranchId)
                        ->where('status', 'printed'),
                ], 'product_version');
            })
            ->when($workflowChannel === 'stand', function ($query) {
                $query->selectSub(
                    DB::table('product_print_records')
                        ->join('branches', 'branches.id', '=', 'product_print_records.branch_id')
                        ->selectRaw('COUNT(DISTINCT product_print_records.branch_id)')
                        ->whereColumn('product_print_records.product_id', 'products.id')
                        ->whereColumn('product_print_records.product_version', 'products.print_version')
                        ->where('product_print_records.status', 'printed')
                        ->whereNotNull('product_print_records.branch_id')
                        ->where('branches.status_id', 3),
                    'all_branch_printed_count'
                );
            })
            ->when($workflowChannel === 'online', function ($query) {
                $query->selectSub(
                    DB::table('product_workflow_actions')
                        ->selectRaw('MAX(created_at)')
                        ->whereColumn('product_workflow_actions.product_id', 'products.id')
                        ->whereIn('product_workflow_actions.action', ['export', 'exported']),
                    'exported_at'
                );
            })
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('product_code', 'like', '%'.$keyword.'%')
                        ->orWhere('name', 'like', '%'.$keyword.'%');
                });
            })
            ->when(filled($statusId), fn ($query) => $query->where('status_id', $statusId))
            ->when($brand !== '', fn ($query) => $query->where('brand', $brand))
            ->when(
                in_array($stage, $allowedStages, true),
                fn ($query) => $query->where('stage', $stage)
            )
            ->when(
                $workflowChannel === 'online' && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $onlineMonth),
                fn ($query) => $query
                    ->whereYear('online_date', (int) substr($onlineMonth, 0, 4))
                    ->whereMonth('online_date', (int) substr($onlineMonth, 5, 2)),
            )
            ->when(
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $createdDate),
                fn ($query) => $query->whereDate('created_at', $createdDate)
            )
            ->when(filled($categoryId), fn ($query) => $query->where('category_id', $categoryId))
            ->when(! auth()->user()->can('viewany', Product::class), fn ($query) => $query->where('status_id', 1))
            ->orderByDesc('id');

        $products = $productsQuery
            ->paginate(15)
            ->withQueryString();

        if ($request->document_search == 'Export') {
            $products = $productsQuery->where('stage', 'checked')
                ->get();
            $preproducts = $exportDataService->prepare($products)
                ->filter(fn ($preproduct) => filled($preproduct['onlinedata'] ?? null))
                ->values();
            $exportedProducts = $preproducts
                ->pluck('product')
                ->filter()
                ->values();

            $this->recordOnlineExportWorkflowActions($request, $exportedProducts);

            return Excel::download(
                new ProductsExport($preproducts),
                'online-products-'.now()->format('Y-m-d').'.xlsx'
            );
        }

        $statuses = Status::whereIn('id', [1, 2])
            ->orderBy('id')
            ->get(['id', 'name']);

        $categories = Category::where('status_id', 3)
            ->orderBy('id')
            ->get(['id', 'name']);

        if ($statuses->isEmpty()) {
            $statuses = Status::orderBy('id')->get(['id', 'name']);
        }

        if ($categories->isEmpty()) {
            $categories = Category::orderBy('id')->get(['id', 'name']);
        }

        $brands = Product::query()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        return view('products.index', compact(
            'products',
            'statuses',
            'brands',
            'currentBranch',
            'currentBranchId',
            'workflowChannel',
            'productListTitle',
            'categories',
            'activeBranchCount'
        ));
    }

    public function catalog(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $products = Product::with(['category', 'country'])
            ->where('status_id', 1)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('product_name', 'like', '%'.$search.'%')
                        ->orWhere('product_code', 'like', '%'.$search.'%')
                        ->orWhere('brand', 'like', '%'.$search.'%')
                        ->orWhere('model', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.catalog', compact('products', 'search'));
    }

    /**
     * Display the public product QR scanner.
     */
    public function scanner()
    {
        return view('products.scanner');
    }

    /**
     * Resolve either a product code or one of this application's product URLs.
     */
    public function scanLookup(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:2048'],
        ]);

        $scannedValue = trim($validated['code']);
        $productId = null;
        $path = parse_url($scannedValue, PHP_URL_PATH);

        if (is_string($path) && preg_match('#/products/(\d+)/?$#', $path, $matches)) {
            $productId = (int) $matches[1];
        }

        $product = Product::query()
            ->where('status_id', 1)
            ->when(
                $productId,
                fn ($query) => $query->whereKey($productId),
                fn ($query) => $query->where('product_code', $scannedValue),
            )
            ->first();

        if (! $product) {
            return response()->json([
                'message' => "This product '$scannedValue' is not available in the catalog.",
            ], 404);
        }

        return response()->json([
            'message' => 'Product found. Opening product details…',
            'product_code' => $product->product_code,
            'redirect_url' => route('products.show', $product),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Product::class);
        $defaultDescriptionMm = self::DEFAULT_DESCRIPTION_MM;
        $defaultDescriptionEn = self::DEFAULT_DESCRIPTION_EN;

        $categories = Category::where('status_id', 3)
            ->orderBy('name')
            ->get(['id', 'name']);

        $statuses = Status::whereIn('id', [1, 2])
            ->orderBy('id')
            ->get(['id', 'name']);

        $countries = Country::where('status_id', 3)
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($statuses->isEmpty()) {
            $statuses = Status::orderBy('id')->get(['id', 'name']);
        }

        $specifications = Specification::orderBy('name')
            ->where('status_id', 3)
            ->pluck('name')
            ->values();

        $brands = Product::query()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand')
            ->values();

        $workflows = Workflow::query()
            ->where(function ($query) {
                $query->where('status_id', 1)->orWhereNull('status_id');
            })
            ->orderBy('id')
            ->get(['id', 'name', 'slug']);

        return view('products.create', compact(
            'categories',
            'statuses',
            'countries',
            'specifications',
            'brands',
            'workflows',
            'defaultDescriptionMm',
            'defaultDescriptionEn',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Product::class);
        $defaultDescriptionMm = self::DEFAULT_DESCRIPTION_MM;
        $defaultDescriptionEn = self::DEFAULT_DESCRIPTION_EN;

        $selectedWorkflowSlug = Workflow::whereKey($request->input('workflow_id'))->value('slug');
        $requiresMainImage = Str::contains(strtolower((string) $selectedWorkflowSlug), 'stand');
        $requiresOnlineDate = Str::contains(strtolower((string) $selectedWorkflowSlug), 'online');
        $minimumOnlineDate = now()->startOfMonth()->toDateString();

        $validated = $request->validate([
            'product_code' => ['required', 'string', 'max:255', 'unique:products,product_code'],
            'status_id' => ['required', 'exists:statuses,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'country_of_origin' => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'main_image' => [$requiresMainImage ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'brand_icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'online_date' => [$requiresOnlineDate ? 'required' : 'nullable', 'date_format:Y-m-d', 'after_or_equal:'.$minimumOnlineDate],
            'specifications' => [
                'required',
                'array',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail) use ($request, $requiresMainImage, $requiresOnlineDate) {
                    $isStandOnly = Workflow::whereKey($request->input('workflow_id'))
                        ->where('slug', 'stand-only')
                        ->exists();

                    if ($isStandOnly && is_array($value) && count($value) > 10) {
                        $fail('Stand Only workflow allows a maximum of 10 specifications.');
                    }

                    if (($requiresMainImage || $requiresOnlineDate) && is_array($value)) {
                        $submittedNames = collect($value)
                            ->pluck('name')
                            ->map(fn ($name) => Str::lower(Str::squish((string) $name)));
                        $requiredSpecifications = collect($requiresOnlineDate ? self::ONLINE_REQUIRED_SPECIFICATIONS : [])
                            ->merge($requiresMainImage ? self::STAND_REQUIRED_SPECIFICATIONS : [])
                            ->unique();
                        $missing = $requiredSpecifications
                            ->reject(fn ($name) => $submittedNames->contains(Str::lower($name)));

                        if ($missing->isNotEmpty()) {
                            $fail('This workflow requires these specifications: '.$missing->implode(', ').'.');
                        }
                    }
                },
            ],
            'specifications.*.name' => ['required', 'string', 'max:255'],
            'specifications.*.value' => ['required', 'string', 'max:255'],
            'workflow_id' => [
                'required',
                'integer',
                'exists:workflows,id',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! WorkflowStep::where('workflow_id', $value)->exists()) {
                        $fail('The selected workflow does not have any steps.');
                    }
                },
            ],
        ]);

        $user = Auth::user();
        $user_id = $user->id;
        $firstWorkflowStep = WorkflowStep::where('workflow_id', $validated['workflow_id'])
            ->orderBy('step_no')
            ->orderBy('id')
            ->firstOrFail();
        $description = trim((string) $request->input('description', '')).$defaultDescriptionMm;
        $descriptionEn = trim((string) $request->input('description_en', '')).$defaultDescriptionEn;

        $specificationRows = collect($request->input('specifications', []))
            ->map(function ($row) {
                return [
                    'name' => trim($row['name'] ?? ''),
                    'value' => trim($row['value'] ?? ''),
                ];
            })
            ->filter(fn ($row) => $row['name'] !== '')
            ->values();

        // dd($request, $specificationRows);

        DB::beginTransaction();
        try {

            $product = Product::create([
                'product_code' => $request['product_code'],
                'brand' => $request['brand'],
                'name' => $request['name'],
                'model' => $request['model'] ?? '',
                'country_of_origin' => $request['country_of_origin'] ?? '',
                'website_url' => $request['website_url'] ?? '',
                'description' => filled($description) ? $description : self::DEFAULT_DESCRIPTION_MM,
                'description_en' => filled($descriptionEn) ? $descriptionEn : self::DEFAULT_DESCRIPTION_EN,
                'status_id' => $request['status_id'] ?? null,
                'category_id' => $request['category_id'] ?? null,
                'user_id' => $request->user()?->id,
                'product_name' => $request['product_name'],
                'online_date' => $requiresOnlineDate
                    ? Carbon::createFromFormat('Y-m-d', $validated['online_date'])->startOfMonth()->toDateString()
                    : null,
            ]);

            $productWorkflow = new ProductWorkflow;
            $productWorkflow->product_id = $product->id;
            $productWorkflow->workflow_id = $validated['workflow_id'];
            $productWorkflow->current_step_id = $firstWorkflowStep->id;
            $productWorkflow->status = 'ongoing';
            $productWorkflow->save();

            foreach ($specificationRows as $row) {
                $specificationName = Str::of($row['name'])->squish()->toString();
                $specificationSlug = Str::slug($specificationName);

                $specification = Specification::firstOrCreate(
                    ['slug' => $specificationSlug],
                    [
                        'name' => $specificationName,
                        'status_id' => 3,
                        'user_id' => $request->user()?->id,
                        'category_id' => $request['category_id'] ?? '',
                    ]
                );

                ProductSpecificationValue::create([
                    'product_id' => $product->id,
                    'specification_id' => $specification->id,
                    'value' => $row['value'],
                ]);
            }

            // Start Single Image Upload
            if ($request->hasFile('main_image')) {
                $file = $request->file('main_image');
                $fname = $file->getClientOriginalName();
                $imagenewname = uniqid($user_id).$product['id'].$fname;
                $file->move(public_path('assets/img/products'), $imagenewname);

                $filepath = 'assets/img/products/'.$imagenewname;
                $product->image = $filepath;
            }
            $product->save();

            if ($request->hasFile('thumbnail_image')) {
                $file = $request->file('thumbnail_image');
                $fname = $file->getClientOriginalName();
                $imagenewname = uniqid($user_id).$product['id'].$fname;
                $file->move(public_path('assets/img/products'), $imagenewname);

                $filepath = 'assets/img/products/'.$imagenewname;
                $product->thumbnail = $filepath;
            }
            $product->save();

            if ($request->hasFile('brand_icon')) {
                $file = $request->file('brand_icon');
                $imagenewname = uniqid($user_id).$product->id.$file->getClientOriginalName();
                $file->move(public_path('assets/img/products'), $imagenewname);
                $product->brand_icon = 'assets/img/products/'.$imagenewname;
                $product->save();
            }
            // End Single Image Upload

            // foreach (['main_image' => 'main', 'thumbnail_image' => 'thumbnail'] as $inputName => $type) {
            //     if (! $request->hasFile($inputName)) {
            //         continue;
            //     }

            //     ProductImage::create([
            //         'product_id' => $product->id,
            //         'image' => $request->file($inputName)->store('products', 'public'),
            //         'type' => $type,
            //     ]);
            // }

            // Start Generate QR
            $destinationUrl = route('products.show', $product->id);
            $qrData = $this->generateQR($destinationUrl, $product->product_code, 'svg');

            $product->qr = $qrData['path'];
            $product->qr_destination = $destinationUrl;
            $product->save();
            // End Generate QR

            DB::commit();

            return $this->sendRespond($product, 'New Product created successfully');

        } catch (Exception $e) {
            DB::rollBack();

            Log::info($e);
            Log::info($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'There is an error in saving product.',
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with([
            'category',
            'country',
            'specificationValues.specification',
        ])
            ->where('id', $id)
            ->when(! auth()->user()?->can('viewany', Product::class), fn ($query) => $query->where('status_id', 1))
            ->firstOrFail();

        if (! auth()->check() && $product->status_id !== 1) {
            abort(404);
        }

        $printedCount = $product->printRecords()->where('status', 'printed')->count();
        $latestPrintedRecord = $product->printRecords()
            ->where('status', 'printed')
            ->latest('printed_at')
            ->first();

        if (request()->ajax()) {
            return $this->sendRespond($product, 'Fetch Single Product Successfully!.');
        }

        return view('products.show', compact(
            'product',
            'printedCount',
            'latestPrintedRecord',
        ));
    }

    /**
     * Display selected products as two fixed sheets per A4 landscape page.
     */
    public function batchPrint(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1', 'max:50'],
            'product_ids.*' => ['required', 'integer', 'distinct', 'exists:products,id'],
        ]);

        $selectedIds = collect($validated['product_ids'])
            ->map(fn ($id) => (int) $id)
            ->values();

        $productsById = Product::with([
            'category',
            'country',
            'specificationValues.specification',
        ])->whereIn('id', $selectedIds)->get()->keyBy('id');

        $products = $selectedIds
            ->map(fn ($id) => $productsById->get($id))
            ->filter()
            ->values();

        return view('products.batch-print', compact('products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with([
            'specificationValues.specification',
            'user.department',
        ])->findOrFail($id);
        $this->authorize('edit', $product);
        $defaultDescriptionMm = self::DEFAULT_DESCRIPTION_MM;
        $defaultDescriptionEn = self::DEFAULT_DESCRIPTION_EN;

        $productWorkflow = ProductWorkflow::where('product_id', $product->id)
            ->latest('id')
            ->first();

        $selectedWorkflow = $productWorkflow
            ? Workflow::find($productWorkflow->workflow_id)
            : null;
        $workflows = Workflow::query()
            ->where(function ($query) use ($selectedWorkflow) {
                $query->where('status_id', 1)
                    ->orWhereNull('status_id')
                    ->when($selectedWorkflow, fn ($query) => $query->orWhere('id', $selectedWorkflow->id));
            })
            ->orderBy('id')
            ->get(['id', 'name', 'slug']);

        // Method 1 Can Action
        $currentWorkflowStep = $productWorkflow?->current_step_id
            ? WorkflowStep::find($productWorkflow->current_step_id)
            : null;

        $isAdmin = request()->user()->hasRoles(['Admin', 'Administrator']);
        $hasStepRole = $currentWorkflowStep
            && (
                ! $currentWorkflowStep->role_id
                || request()->user()->roles()->whereKey($currentWorkflowStep->role_id)->exists()
            );

        $canWorkflowAction = $currentWorkflowStep
            && $productWorkflow->status === 'ongoing'
            && ($isAdmin || $hasStepRole);

        $categories = Category::where('status_id', 3)
            ->orderBy('name')
            ->get(['id', 'name']);

        $statuses = Status::whereIn('id', [1, 2])
            ->orderBy('id')
            ->get(['id', 'name']);

        $countries = Country::where('status_id', 3)
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($statuses->isEmpty()) {
            $statuses = Status::orderBy('id')->get(['id', 'name']);
        }

        $specifications = Specification::orderBy('name')
            ->where('status_id', 3)
            ->pluck('name')
            ->values();

        $brands = Product::query()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand')
            ->values();

        $initialSpecifications = $product->specificationValues
            ->map(fn ($item) => [
                'name' => $item->specification?->name ?? '',
                'value' => $item->value,
            ])
            ->values()
            ->all();

        $workflowActionLogs = $productWorkflow
            ? ProductWorkflowAction::query()
                ->with(['user.department', 'workflowStep.role'])
                ->where('product_workflow_id', $productWorkflow->id)
                ->orderBy('created_at')
                ->get()
                ->keyBy('workflow_step_id')
            : collect();

        $workflowActionTimeline = collect([
            [
                'label' => 'Prepared By',
                'completed' => true,
                'user' => $product->user,
                'acted_at' => $product->created_at,
            ],
        ])->merge(
            $selectedWorkflow
                ? WorkflowStep::query()
                    ->with('role')
                    ->where('workflow_id', $selectedWorkflow->id)
                    ->orderBy('step_no')
                    ->orderBy('id')
                    ->get()
                    ->map(function ($step) use ($workflowActionLogs) {
                        $actionLog = $workflowActionLogs->get($step->id);
                        $roleName = $step->role?->name;

                        return [
                            'label' => trim($step->name.' By '.($roleName ?: '')),
                            'completed' => filled($actionLog),
                            'user' => $actionLog?->user,
                            'acted_at' => $actionLog?->created_at,
                        ];
                    })
                : collect(),
        );

        return view('products.edit', compact(
            'product',
            'categories',
            'statuses',
            'countries',
            'specifications',
            'brands',
            'initialSpecifications',
            'productWorkflow',
            'selectedWorkflow',
            'workflows',
            'currentWorkflowStep',
            'canWorkflowAction',
            'defaultDescriptionMm',
            'defaultDescriptionEn',
            'workflowActionTimeline',
        ));
    }

    /**
     * Record the current workflow action and move the product to its next step.
     */
    public function workflowAction(Request $request, Product $product)
    {
        $this->authorize('edit', $product);

        DB::transaction(function () use ($request, $product) {
            $productWorkflow = ProductWorkflow::query()
                ->where('product_id', $product->id)
                ->latest('id')
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($productWorkflow->status !== 'ongoing', 422, 'This workflow has already been completed.');

            $currentStep = WorkflowStep::findOrFail($productWorkflow->current_step_id);
            $isAdmin = $request->user()->hasRoles(['Admin', 'Administrator']);
            $hasStepRole = ! $currentStep->role_id
                || $request->user()->roles()->whereKey($currentStep->role_id)->exists();

            abort_unless($isAdmin || $hasStepRole, 403, 'You cannot perform this workflow action.');

            $actionLog = new ProductWorkflowAction;
            $actionLog->product_id = $product->id;
            $actionLog->product_workflow_id = $productWorkflow->id;
            $actionLog->workflow_step_id = $currentStep->id;
            $actionLog->user_id = $request->user()->id;
            $actionLog->action = $currentStep->action;
            $actionLog->comment = $request->input('comment');
            $actionLog->save();

            $nextStep = WorkflowStep::query()
                ->where('workflow_id', $productWorkflow->workflow_id)
                ->where('step_no', '>', $currentStep->step_no)
                ->orderBy('step_no')
                ->orderBy('id')
                ->first();

            $product->stage = $currentStep->action;
            $product->save();

            $productWorkflow->current_step_id = $nextStep?->id;
            $productWorkflow->status = $nextStep ? 'ongoing' : 'completed';
            $productWorkflow->save();
        });

        return redirect()
            ->route('products.edit', $product)
            ->with('success', 'Workflow action completed successfully.');
    }

    public function bulkFinishOnlineProducts(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1', 'max:100'],
            'product_ids.*' => ['required', 'integer', 'distinct', 'exists:products,id'],
        ]);

        $finishedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($request, $validated, &$finishedCount, &$skippedCount) {
            foreach ($validated['product_ids'] as $productId) {
                $product = Product::findOrFail($productId);
                // $this->authorize('edit', $product);

                $productWorkflow = ProductWorkflow::query()
                    ->where('product_id', $product->id)
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                if (! $productWorkflow || $productWorkflow->status !== 'ongoing') {
                    $skippedCount++;

                    continue;
                }

                $currentStep = WorkflowStep::find($productWorkflow->current_step_id);
                $isOnlineWorkflow = Workflow::query()
                    ->whereKey($productWorkflow->workflow_id)
                    ->where('slug', 'like', '%online%')
                    ->exists();
                $isFinishStep = in_array(Str::lower((string) $currentStep?->action), ['finish', 'finished'], true);
                $isAdmin = $request->user()->hasRoles(['Admin', 'Administrator']);
                $hasStepRole = $currentStep
                    && (
                        ! $currentStep->role_id
                        || $request->user()->roles()->whereKey($currentStep->role_id)->exists()
                    );

                if (! $isOnlineWorkflow || ! $isFinishStep || ! ($isAdmin || $hasStepRole)) {
                    $skippedCount++;

                    continue;
                }

                $actionLog = new ProductWorkflowAction;
                $actionLog->product_id = $product->id;
                $actionLog->product_workflow_id = $productWorkflow->id;
                $actionLog->workflow_step_id = $currentStep->id;
                $actionLog->user_id = $request->user()->id;
                $actionLog->action = $currentStep->action;
                $actionLog->comment = $request->input('comment');
                $actionLog->save();

                $nextStep = WorkflowStep::query()
                    ->where('workflow_id', $productWorkflow->workflow_id)
                    ->where('step_no', '>', $currentStep->step_no)
                    ->orderBy('step_no')
                    ->orderBy('id')
                    ->first();

                $product->stage = $currentStep->action;
                $product->save();

                $productWorkflow->current_step_id = $nextStep?->id;
                $productWorkflow->status = $nextStep ? 'ongoing' : 'completed';
                $productWorkflow->save();

                $finishedCount++;
            }
        });

        $message = "{$finishedCount} product(s) finished successfully.";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} product(s) were skipped.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'finished_count' => $finishedCount,
            'skipped_count' => $skippedCount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('edit', $product);

        $productWorkflow = ProductWorkflow::where('product_id', $product->id)
            ->latest('id')
            ->first();
        $selectedWorkflowSlug = $productWorkflow
            ? Workflow::whereKey($productWorkflow->workflow_id)->value('slug')
            : null;
        $requiresMainImage = Str::contains(strtolower((string) $selectedWorkflowSlug), 'stand');
        $requiresOnlineDate = Str::contains(strtolower((string) $selectedWorkflowSlug), 'online');
        $isStandOnly = $selectedWorkflowSlug === 'stand-only';
        $removesMainImage = $request->boolean('remove_main_image');
        $mainImageRule = $requiresMainImage && (blank($product->image) || $removesMainImage) ? 'required' : 'nullable';
        $minimumOnlineDate = now()->startOfMonth()->toDateString();
        $description = trim((string) $request->input('description', ''));
        $descriptionEn = trim((string) $request->input('description_en', ''));

        $request->validate([
            'product_code' => ['required', 'string', 'max:255', 'unique:products,product_code,'.$product->id],
            'status_id' => ['required', 'exists:statuses,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'country_of_origin' => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'main_image' => [$mainImageRule, 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'brand_icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'remove_main_image' => ['nullable', 'boolean'],
            'remove_thumbnail_image' => ['nullable', 'boolean'],
            'remove_brand_icon' => ['nullable', 'boolean'],
            'online_date' => [$requiresOnlineDate ? 'required' : 'nullable', 'date_format:Y-m-d', 'after_or_equal:'.$minimumOnlineDate],
            'specifications' => [
                'required',
                'array',
                'min:1',
                function (string $attribute, mixed $value, \Closure $fail) use ($isStandOnly, $requiresMainImage, $requiresOnlineDate) {
                    if ($isStandOnly && is_array($value) && count($value) > 10) {
                        $fail('Stand Only workflow allows a maximum of 10 specifications.');
                    }

                    if (($requiresMainImage || $requiresOnlineDate) && is_array($value)) {
                        $submittedNames = collect($value)
                            ->pluck('name')
                            ->map(fn ($name) => Str::lower(Str::squish((string) $name)));
                        $requiredSpecifications = collect($requiresOnlineDate ? self::ONLINE_REQUIRED_SPECIFICATIONS : [])
                            ->merge($requiresMainImage ? self::STAND_REQUIRED_SPECIFICATIONS : [])
                            ->unique();
                        $missing = $requiredSpecifications
                            ->reject(fn ($name) => $submittedNames->contains(Str::lower($name)));

                        if ($missing->isNotEmpty()) {
                            $fail('This workflow requires these specifications: '.$missing->implode(', ').'.');
                        }
                    }
                },
            ],
            'specifications.*.name' => ['required', 'string', 'max:255'],
            'specifications.*.value' => ['required', 'string', 'max:255'],
        ]);

        $specificationRows = collect($request->input('specifications', []))
            ->map(fn ($row) => [
                'name' => trim($row['name'] ?? ''),
                'value' => trim($row['value'] ?? ''),
            ])
            ->filter(fn ($row) => $row['name'] !== '')
            ->values();

        DB::beginTransaction();

        try {
            $oldProductCode = $product->product_code;
            $product->load('specificationValues.specification');
            $oldPrintSnapshot = $this->productPrintSnapshot($product);
            $fromVersion = (int) $product->print_version;

            $product->update([
                // 'product_code' => $request->product_code,
                'name' => $request->name,
                // 'product_name' => $request->product_name,
                // 'brand' => $request->brand,
                'model' => $request->model,
                'country_of_origin' => $request->country_of_origin,
                'website_url' => $request->website_url ?? '',
                'description' => filled($description) ? $description : self::DEFAULT_DESCRIPTION_MM,
                'description_en' => filled($descriptionEn) ? $descriptionEn : self::DEFAULT_DESCRIPTION_EN,
                'status_id' => $request->status_id,
                // 'category_id' => $request->category_id,
                'online_date' => $requiresOnlineDate
                    ? Carbon::createFromFormat('Y-m-d', $request->input('online_date'))->startOfMonth()->toDateString()
                    : null,
            ]);

            $product->specificationValues()->delete();

            foreach ($specificationRows as $row) {
                $specificationName = Str::of($row['name'])->squish()->toString();
                $specification = Specification::firstOrCreate(
                    ['slug' => Str::slug($specificationName)],
                    [
                        'name' => $specificationName,
                        'status_id' => 3,
                        'user_id' => $request->user()?->id,
                        'category_id' => $request->category_id,
                    ]
                );

                ProductSpecificationValue::create([
                    'product_id' => $product->id,
                    'specification_id' => $specification->id,
                    'value' => $row['value'],
                ]);
            }

            foreach ([
                'main_image' => ['column' => 'image', 'remove' => 'remove_main_image'],
                'thumbnail_image' => ['column' => 'thumbnail', 'remove' => 'remove_thumbnail_image'],
                'brand_icon' => ['column' => 'brand_icon', 'remove' => 'remove_brand_icon'],
            ] as $input => $config) {
                $column = $config['column'];

                if ($request->boolean($config['remove']) && ! $request->hasFile($input)) {
                    if ($product->{$column}) {
                        File::delete(public_path($product->{$column}));
                    }

                    $product->{$column} = null;

                    continue;
                }

                if (! $request->hasFile($input)) {
                    continue;
                }

                if ($product->{$column}) {
                    File::delete(public_path($product->{$column}));
                }

                $file = $request->file($input);
                $fileName = uniqid((string) $request->user()?->id).$product->id.$file->getClientOriginalName();
                $file->move(public_path('assets/img/products'), $fileName);
                $product->{$column} = 'assets/img/products/'.$fileName;
            }

            if ($oldProductCode !== $product->product_code || ! $product->qr) {
                if ($product->qr) {
                    File::delete(public_path($product->qr));
                }

                $destinationUrl = route('products.show', $product->id);
                $qrData = $this->generateQR($destinationUrl, $product->product_code, 'svg');
                $product->qr = $qrData['path'];
                $product->qr_destination = $destinationUrl;
            }

            $product->save();

            $product->refresh()->load('specificationValues.specification');
            $newPrintSnapshot = $this->productPrintSnapshot($product);

            if ($oldPrintSnapshot !== $newPrintSnapshot) {
                $product->increment('print_version');
                $product->refresh();

                ProductEditLog::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()?->id,
                    'branch_id' => $request->user()?->branch_id,
                    'from_version' => $fromVersion,
                    'to_version' => $product->print_version,
                    'old_values' => $oldPrintSnapshot,
                    'new_values' => $newPrintSnapshot,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            DB::commit();

            return $this->sendRespond($product, 'Product updated successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'success' => false,
                'message' => 'There is an error in updating product.',
            ], 500);
        }
    }

    private function productPrintSnapshot(Product $product): array
    {
        return [
            'product_code' => $product->product_code,
            'name' => $product->name,
            'product_name' => $product->product_name,
            'brand' => $product->brand,
            'model' => $product->model,
            'category_id' => $product->category_id,
            'country_of_origin' => $product->country_of_origin,
            'description' => $product->description,
            'description_en' => $product->description_en,
            'status_id' => $product->status_id,
            'main_image' => $product->image,
            'thumbnail_image' => $product->thumbnail,
            'brand_icon' => $product->brand_icon,
            'qr' => $product->qr,
            'qr_destination' => $product->qr_destination,
            'specifications' => $product->specificationValues
                ->map(fn ($value) => [
                    'name' => $value->specification?->name,
                    'value' => $value->value,
                ])
                ->sortBy(fn ($value) => ($value['name'] ?? '').'|'.($value['value'] ?? ''))
                ->values()
                ->all(),
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::with('images')->findOrFail($id);
        $files = collect([
            $product->image,
            $product->thumbnail,
            $product->brand_icon,
            $product->qr,
        ])->merge($product->images->pluck('image'))
            ->filter()
            ->unique()
            ->values();

        DB::beginTransaction();

        try {
            ProductWorkflowAction::where('product_id', $product->id)->delete();
            ProductWorkflow::where('product_id', $product->id)->delete();
            $product->printRecords()->delete();
            $product->editLogs()->delete();
            $product->specificationValues()->delete();
            $product->images()->delete();
            $product->delete();

            DB::commit();

            $files->each(fn (string $path) => File::delete(public_path($path)));

            return $this->sendRespond($id, 'Product deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'success' => false,
                'message' => 'There is an error in deleting the product.',
            ], 500);
        }
    }

    public function search_product(Request $request)
    {
        $conn = DB::connection('master_product');
        // $branch = Branch::whereId($branch_code)->first();
        $product_code = $request->product_code;

        $products = $conn->select("
            select product_grade_name as ProductType
                ,cat.remark as MainCategory
                --,coalesce(cat.remark,'N/A') as Main_Category
                ,coalesce(cat.product_category_code,'N/A') as Category,coalesce(cat.product_category_name,'-') as Category_Name
                ,coalesce(subcat.product_group_code,'N/A') as Group,coalesce(subcat.product_group_name,'-') as Group_Name
                ,coalesce(class.product_pattern_code,'N/A')as Pattern,coalesce(class.product_pattern_name,'-') as Pattern_Name
                ,coalesce(subclass.product_design_code,'N/A') as Design,coalesce(subclass.product_design_name,'-') as Design_Name
                ,barcode_code
                ,coalesce(regexp_replace(prod.product_name1, E'[\\n\\r]+',' ', 'g' ),'')as product_name
                ,product_unit_name as Unit
                ,product_brand_name as Brand
            from master_data.master_product prod 
                left join master_data.master_product_category cat on prod.product_category_id = cat.product_category_id
                left join master_data.master_product_group subcat on prod.product_group_id = subcat.product_group_id
                left join master_data.master_product_pattern class on prod.product_pattern_id = class.product_pattern_id -- class
                left join master_data.master_product_design subclass on prod.product_design_id = subclass.product_design_id -- sub-class
                left join master_data.master_product_multiunit mulunit on prod.product_id= mulunit.product_id and prod.product_code= mulunit.product_code
                left join master_data.master_product_unit unit on mulunit.product_unit_id= unit.product_unit_id
                left join master_data.master_product_brand bd on prod.product_brand_id= bd.product_brand_id
                left join  master_data.master_product_grade gd on prod.product_grade_id= gd.product_grade_id
                inner join master_data.master_product_barcode bar on prod.product_id= bar.product_id
                and mulunit.product_unit_id= bar.product_unit_id
            where prod.inactive = 'A'
            and prod.product_code='$product_code'
        ");
        // dd($products);

        $product = ($products) ? $products[0] : null;
        if ($product) {
            return response()->json([
                'data' => $product,
            ]);
        } else {
            return response()->json(['error' => "Product code doesn't exist."]);
        }
    }

    public function generateProductQR(Request $request, string $text, string $format = 'png')
    {
        abort_unless(in_array($format, ['png', 'svg'], true), 404);

        $product = Product::where('product_code', $text)->firstOrFail();
        $this->authorize('edit', $product);

        $product->load('specificationValues.specification');
        $oldPrintSnapshot = $this->productPrintSnapshot($product);
        $fromVersion = (int) $product->print_version;
        $destinationUrl = route('products.show', $product->id);
        $qrData = $this->generateQR($destinationUrl, $product->product_code, $format);
        $productWasUpdated = $product->qr !== $qrData['path']
            || $product->qr_destination !== $destinationUrl;

        if ($productWasUpdated) {
            DB::transaction(function () use ($request, $product, $qrData, $destinationUrl, $oldPrintSnapshot, $fromVersion) {
                $product->update([
                    'qr' => $qrData['path'],
                    'qr_destination' => $destinationUrl,
                    'print_version' => $fromVersion + 1,
                    'user_id' => $request->user()?->id,
                ]);

                $product->load('specificationValues.specification');
                $newPrintSnapshot = $this->productPrintSnapshot($product);
                $newPrintSnapshot['qr_regenerated_at'] = now()->toIso8601String();

                ProductEditLog::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()?->id,
                    'branch_id' => $request->user()?->branch_id,
                    'from_version' => $fromVersion,
                    'to_version' => $product->print_version,
                    'old_values' => $oldPrintSnapshot,
                    'new_values' => $newPrintSnapshot,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'QR code generated successfully.',
            'data' => [
                ...$qrData,
                'destination_url' => $destinationUrl,
                'print_version' => $product->print_version,
                'product_updated' => $productWasUpdated,
            ],
        ]);
    }

    // composer require simplesoftwareio/simple-qrcode
    public function generateQR(string $text, string $fileName, string $format = 'png'): array
    {
        if (! in_array($format, ['png', 'svg'], true)) {
            throw new \InvalidArgumentException('Unsupported QR code format.');
        }

        $qrCode = QrCode::format($format)->size(100)->generate($text);
        $safeFileName = basename($fileName).'.'.$format;
        $relativePath = 'assets/img/products/qrs/'.$safeFileName;
        $absolutePath = public_path($relativePath);

        if (! file_exists(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }
        // Delete existing file if it exists
        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }

        file_put_contents($absolutePath, $qrCode);

        return [
            'format' => $format,
            'path' => $relativePath,
            'url' => asset($relativePath),
        ];
    }

    public function changestatus(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer', 'exists:products,id'],
            'status_id' => ['required', 'integer', 'exists:statuses,id'],
        ]);

        $product = Product::findOrFail($request['id']);
        $this->authorize('edit', $product);

        if ((int) $product->status_id === (int) $request['status_id']) {
            return response()->json(['success' => 'Status Change Successfully']);
        }

        $product->load('specificationValues.specification');
        $oldPrintSnapshot = $this->productPrintSnapshot($product);
        $fromVersion = (int) $product->print_version;

        $product->status_id = $request['status_id'];
        $product->print_version = $fromVersion + 1;
        $product->user_id = $request->user()?->id;
        $product->save();

        $product->load('specificationValues.specification');

        ProductEditLog::create([
            'product_id' => $product->id,
            'user_id' => $request->user()?->id,
            'branch_id' => $request->user()?->branch_id,
            'from_version' => $fromVersion,
            'to_version' => $product->print_version,
            'old_values' => $oldPrintSnapshot,
            'new_values' => $this->productPrintSnapshot($product),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['success' => 'Status Change Successfully']);
    }

    public function cleanDescriptionJson(Request $request)
    {
        $validated = $request->validate([
            'description' => ['required', 'string'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->parseProductDescription($validated['description']),
        ]);
    }

    public function import(Request $request)
    {
        $this->authorize('create', Product::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xls,xlsx', 'max:5120'],
        ]);

        ini_set('max_execution_time', '600');

        DB::beginTransaction();

        try {
            $import = new ProductImport($request->user()->id);
            Excel::import($import, $request->file('file'));

            DB::commit();

            return back()->with('success', $import->importedCount().' products imported successfully.');
        } catch (ExcelImportValidationException $e) {
            DB::rollBack();
            Log::info($e);

            return back()->with('validation_errors', [
                'row' => $e->rowNumber(),
                'errors' => $e->errors(),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return back()->with('error', 'System Error: '.$e->getMessage());
        }
    }

    private function parseProductDescription(string $rawDescription): array
    {
        $lines = collect(preg_split('/\R/u', str_replace(["\r\n", "\r"], "\n", $rawDescription)))
            ->map(fn ($line) => $this->cleanDescriptionLine($line))
            ->filter(fn ($line) => filled($line))
            ->values();

        $attributes = [];
        $descriptionLines = [];
        $isReadingDescription = false;

        foreach ($lines as $line) {
            $matchesAttribute = preg_match('/^([^:：]{1,80})[:：]\s*(.*)$/u', $line, $matches);

            if ($matchesAttribute && ! $isReadingDescription) {
                $label = Str::squish($matches[1]);
                $value = Str::squish($matches[2]);

                if (filled($label) && filled($value)) {
                    $attributes[Str::snake(Str::lower($label))] = [
                        'label' => $label,
                        'value' => $value,
                    ];

                    continue;
                }
            }

            $isReadingDescription = true;
            $descriptionLines[] = $line;
        }

        return [
            'attributes' => $attributes,
            'description' => collect($descriptionLines)
                ->map(fn ($line) => Str::squish($line))
                ->filter(fn ($line) => filled($line))
                ->implode("\n"),
            'description_lines' => $descriptionLines,
        ];
    }

    private function cleanDescriptionLine(string $line): string
    {
        $line = html_entity_decode($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $line = str_replace("\xc2\xa0", ' ', $line);
        $line = preg_replace('/^[\s\p{So}\p{Pd}•●▪▫■□◆◇♦◊]+/u', '', $line) ?? $line;

        return Str::squish($line);
    }

    private function recordOnlineExportWorkflowActions(Request $request, $products): void
    {
        abort_unless($request->user()?->hasRoles(['Ecommerce Admin']), 403, 'Only Ecommerce Admin can export online products.');

        DB::transaction(function () use ($request, $products) {
            foreach ($products as $product) {
                $productWorkflow = ProductWorkflow::query()
                    ->where('product_id', $product->id)
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();

                if (! $productWorkflow || $productWorkflow->status !== 'ongoing') {
                    continue;
                }

                $currentStep = WorkflowStep::find($productWorkflow->current_step_id);
                $isOnlineWorkflow = Workflow::query()
                    ->whereKey($productWorkflow->workflow_id)
                    ->where('slug', 'like', '%online%')
                    ->exists();
                $isExportStep = in_array(Str::lower((string) $currentStep?->action), ['export', 'exported'], true);
                $hasStepRole = $currentStep
                    && (
                        ! $currentStep->role_id
                        || $request->user()->roles()->whereKey($currentStep->role_id)->exists()
                    );

                if (! $isOnlineWorkflow || ! $isExportStep || ! $hasStepRole) {
                    continue;
                }

                $actionLog = new ProductWorkflowAction;
                $actionLog->product_id = $product->id;
                $actionLog->product_workflow_id = $productWorkflow->id;
                $actionLog->workflow_step_id = $currentStep->id;
                $actionLog->user_id = $request->user()->id;
                $actionLog->action = $currentStep->action;
                $actionLog->comment = 'Exported with Online Product Excel export.';
                $actionLog->save();

                $nextStep = WorkflowStep::query()
                    ->where('workflow_id', $productWorkflow->workflow_id)
                    ->where('step_no', '>', $currentStep->step_no)
                    ->orderBy('step_no')
                    ->orderBy('id')
                    ->first();

                $product->stage = $currentStep->action;
                $product->save();

                $productWorkflow->current_step_id = $nextStep?->id;
                $productWorkflow->status = $nextStep ? 'ongoing' : 'completed';
                $productWorkflow->save();
            }
        });
    }
}

//  sudo apt install php8.2-imagick -y

// 1. Ubuntu / Debian (Nginx or Apache)
// Run the following commands in your terminal:

// Bash
// # Update package list
// sudo apt update

// # Install the extension for your PHP version
// sudo apt install php-imagick -y
// (If you are running a specific PHP version like PHP 8.2 or 8.3, install php8.2-imagick or php8.3-imagick instead).

// Restart Your Web Server / PHP-FPM
// After installation, you must restart your web service so PHP loads the extension:

// For Nginx + PHP-FPM:

// Bash
// sudo systemctl restart php8.3-fpm   # Replace with your PHP version
// sudo systemctl restart nginx
// For Apache:

// Bash
// sudo systemctl restart apache2
// 2. Check if Imagick is Enabled
// Run this command in terminal to confirm it's loaded:

// Bash
// php -m | grep imagick
