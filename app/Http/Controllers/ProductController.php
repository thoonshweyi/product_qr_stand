<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecificationValue;
use App\Models\Specification;
use App\Models\Status;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = Product::query();

        $products = $results->orderBy('id', 'desc')->paginate(15);

        // dd($products);
        return view('products.index', compact(
            'products',
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
            ->pluck('name')
            ->values();

        $brands = Product::query()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand')
            ->values();

        return view('products.create', compact('categories', 'statuses', 'countries', 'specifications', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            'main_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'brand_icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'specifications' => ['required', 'array', 'min:1', 'max:8'],
            'specifications.*.name' => ['required', 'string', 'max:255'],
            'specifications.*.value' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $user_id = $user->id;

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
                // 'website_url' => $request['website_url'] ?? '',
                'description' => $request['description'] ?? '',
                'status_id' => $request['status_id'] ?? null,
                'category_id' => $request['category_id'] ?? null,
                'user_id' => $request->user()?->id,
                'product_name' => $request['product_name'],
            ]);

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
            if (file_exists($request['main_image'])) {
                $file = $request['main_image'];
                $fname = $file->getClientOriginalName();
                $imagenewname = uniqid($user_id).$product['id'].$fname;
                $file->move(public_path('assets/img/products'), $imagenewname);

                $filepath = 'assets/img/products/'.$imagenewname;
                $product->image = $filepath;
            }
            $product->save();

            if (file_exists($request['thumbnail_image'])) {
                $file = $request['thumbnail_image'];
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
            $qrData = $this->generateQR($destinationUrl, $product->product_code, 'png');

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
        ])->findOrFail($id);

        if (! auth()->check() && $product->status_id !== 1) {
            abort(404);
        }

        $printedCount = $product->printRecords()->where('status', 'printed')->count();
        $latestPrintedRecord = $product->printRecords()
            ->where('status', 'printed')
            ->latest('printed_at')
            ->first();

        return view('products.show', compact(
            'product',
            'printedCount',
            'latestPrintedRecord',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with('specificationValues.specification')->findOrFail($id);

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

        return view('products.edit', compact(
            'product',
            'categories',
            'statuses',
            'countries',
            'specifications',
            'brands',
            'initialSpecifications',
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'product_code' => ['required', 'string', 'max:255', 'unique:products,product_code,'.$product->id],
            'status_id' => ['required', 'exists:statuses,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'country_of_origin' => ['required', 'string', 'max:255'],
            // 'website_url' => ['nullable', 'url', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'main_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'brand_icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'specifications' => ['required', 'array', 'min:1', 'max:8'],
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

            $product->update([
                'product_code' => $request->product_code,
                'name' => $request->name,
                'product_name' => $request->product_name,
                'brand' => $request->brand,
                'model' => $request->model,
                'country_of_origin' => $request->country_of_origin,
                'website_url' => $request->website_url ?? '',
                'description' => $request->description ?? '',
                'status_id' => $request->status_id,
                'category_id' => $request->category_id,
                'user_id' => $request->user()?->id,
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

            foreach (['main_image' => 'image', 'thumbnail_image' => 'thumbnail', 'brand_icon' => 'brand_icon'] as $input => $column) {
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
                $qrData = $this->generateQR($destinationUrl, $product->product_code, 'png');
                $product->qr = $qrData['path'];
                $product->qr_destination = $destinationUrl;
            }

            $product->save();
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

    public function generateProductQR(string $text, string $format = 'png')
    {
        abort_unless(in_array($format, ['png', 'svg'], true), 404);

        $product = Product::where('product_code', $text)->firstOrFail();
        $destinationUrl = route('products.show', $product->id);
        $qrData = $this->generateQR($destinationUrl, $product->product_code, $format);

        $product->update([
            'qr' => $qrData['path'],
            'qr_destination' => $destinationUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'QR code generated successfully.',
            'data' => [
                ...$qrData,
                'destination_url' => $destinationUrl,
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
        file_put_contents($absolutePath, $qrCode);

        return [
            'format' => $format,
            'path' => $relativePath,
            'url' => asset($relativePath),
        ];
    }
}
