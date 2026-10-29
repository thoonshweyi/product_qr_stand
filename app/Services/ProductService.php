<?php

namespace App\Services;

use App\Exports\ProductsExport;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductService
{
    private const DEFAULT_DESCRIPTION_MM = "PRO 1 Global Home Center မှ အရည်အသွေးကောင်းမွန် သော ပစ္စည်းများကိုသာ ပစ္စည်းမှန်စျေးနှုန်းမှန်ကန်စွာ ရောင်းချသဖြင့် ယုံကြည်စိတ်ချစွာ ၀ယ်ယူနိုင်ပါသည်။";

    private const DEFAULT_DESCRIPTION_EN = "Shop for variety of high quality products with reasonable price at PRO 1 Global, leading provider for construction and home improvement products.";

    public function create(array $data, $user = null): Product
    {
        return DB::transaction(function () use ($data, $user,) {
            // Start Workflow Config
            $workflowConfig = $this->getWorkflowConfig($data['workflow_id'] ?? null);

            $requiresOnlineDate = $workflowConfig['requiresOnlineDate'];
            // End Workflow Config



            $defaultDescriptionMm = "\n".self::DEFAULT_DESCRIPTION_MM;
            $defaultDescriptionEn = "\n".self::DEFAULT_DESCRIPTION_EN;

            $user = Auth::user();
            $user_id = $user->id;
            $firstWorkflowStep = WorkflowStep::where('workflow_id', $data['workflow_id'])
                ->orderBy('step_no')
                ->orderBy('id')
                ->firstOrFail();
            $description = trim((string) $data['description']).$defaultDescriptionMm;
            $descriptionEn = trim((string) $data['description_en']).$defaultDescriptionEn;

            $specificationRows = collect($data['specifications'])
                ->map(function ($row) {
                    return [
                        'name' => trim($row['name'] ?? ''),
                        'value' => trim($row['value'] ?? ''),
                    ];
                })
                ->filter(fn ($row) => $row['name'] !== '')
                ->values();


            $product = Product::create([
                'product_code' => $data['product_code'],
                'brand' => $data['brand'],
                'name' => $data['name'],
                'model' => $data['model'] ?? '',
                'country_of_origin' => $data['country_of_origin'] ?? '',
                'website_url' => $data['website_url'] ?? '',
                'description' => filled($description) ? $description : self::DEFAULT_DESCRIPTION_MM,
                'description_en' => filled($descriptionEn) ? $descriptionEn : self::DEFAULT_DESCRIPTION_EN,
                'status_id' => $data['status_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'user_id' => $user?->id,
                'product_name' => $data['product_name'],
                'online_date' => $requiresOnlineDate
                    ? Carbon::createFromFormat('Y-m-d', $data['online_date'])->startOfMonth()->toDateString()
                    : null,
            ]);


            $productWorkflow = new ProductWorkflow;
            $productWorkflow->product_id = $product->id;
            $productWorkflow->workflow_id = $data['workflow_id'];
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
                        'user_id' => $user?->id,
                        'category_id' => $data['category_id'] ?? '',
                    ]
                );

                ProductSpecificationValue::create([
                    'product_id' => $product->id,
                    'specification_id' => $specification->id,
                    'value' => $row['value'],
                ]);
            }

            // Start Single Image Upload
            
            // End Single Image Upload

            // Start Generate QR
            $destinationUrl = route('products.show', $product->id);
            $qrData = $this->generateQR($destinationUrl, $product->product_code, 'svg');

            $product->qr = $qrData['path'];
            $product->qr_destination = $destinationUrl;
            $product->save();

            return $product;
        });
    }

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

    public function parseProductDescription(string $rawDescription): array
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

    public function getSpecifications($specificationsArr) {
        $result = [];
        foreach ($specificationsArr as $name => $spec) {
            if (is_array($spec) && isset($spec['value'])) {
                $result[] = [
                    'name' => $name,
                    'value' => $spec['value'],
                ];
            }
        }
        return $result;
    }

    public function getWorkflowConfig(?int $workflowId): array
    {
        $selectedWorkflowSlug = Workflow::whereKey($workflowId)->value('slug');
        $requiresMainImage = Str::contains(strtolower((string) $selectedWorkflowSlug), 'stand');
        $requiresOnlineDate = Str::contains(strtolower((string) $selectedWorkflowSlug), 'online');
        $minimumOnlineDate = now()->startOfMonth()->toDateString();

        return [
            'selectedWorkflowSlug' => $selectedWorkflowSlug,
            'requiresMainImage' => $requiresMainImage,
            'requiresOnlineDate' => $requiresOnlineDate,
            'minimumOnlineDate' => $minimumOnlineDate,
        ];
    }

    public function search_products($productCodes){
        $productCodesString = implode("','", $productCodes);

        $conn = DB::connection('master_product');
        // $branch = Branch::whereId($branch_code)->first();

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
            and prod.product_code in ('$productCodesString')
        ");
        // dd($products);

        return $products;
    }

    public function productList(Request $request, ?string $workflowChannel = null, ?OnlineProductExportDataService $exportDataService = null)
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

        $currentBranchId = $request->user()?->branch_id;
        $currentBranch = $request->user()?->branch;
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
            ->when(! auth()->user()?->can('viewany', Product::class), fn ($query) => $query->where('status_id', 1))
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

            // dd($exportedProducts);

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

        
        if(request()->ajax()){
            return response()->json([
                'success' => true,
                'data'=>$products,
                'message' => "Product List Fetched Successfully",
            ]);
        }

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

    private function recordOnlineExportWorkflowActions(Request $request, $products): void
    {
        abort_unless($request->user()?->hasRoles(['Ecommerce Admin', 'Administrator']), 403, 'Only Ecommerce Admin can export online products.');

        DB::transaction(function () use ($request, $products) {
            try {
                $isAdmin = $request->user()->hasRoles(['Admin', 'Administrator']);

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
                    $hasStepRole = $isAdmin
                        || $currentStep
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
            } catch (Exception $e) {
                Log::info('Export Workflow Exception: '.$e->getMessage());
            }
        });
    }
}
