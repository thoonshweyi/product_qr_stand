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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $results = Product::query();

        // $products = $results->paginate(15);
        return view('products.index', compact(
            // "products",
            []
        ));
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
        dd("hay");
        $validated = $request->validate([
            'product_code' => ['required', 'string', 'max:255', 'unique:products,product_code'],
            'status_id' => ['required', 'exists:statuses,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'country_of_origin' => ['required', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:2000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'main_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'specifications' => ['required', 'array', 'min:1', 'max:8'],
            'specifications.*.name' => ['required', 'string', 'max:255'],
            'specifications.*.value' => ['required', 'string', 'max:255'],
        ]);

        $specificationRows = collect($request->input('specifications', []))
            ->map(function ($row) {
                return [
                    'name' => trim($row['name'] ?? ''),
                    'value' => trim($row['value'] ?? ''),
                ];
            })
            ->filter(fn ($row) => $row['name'] !== '')
            ->values();

        $duplicateSpecification = $specificationRows
            ->map(fn ($row) => Str::of($row['name'])->squish()->lower()->toString())
            ->duplicates()
            ->isNotEmpty();

        if ($duplicateSpecification) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => ['specifications' => ['The same specification cannot be added twice.']],
            ], 422);
        }

        DB::transaction(function () use ($request, $validated, $specificationRows) {
            $product = Product::create([
                'product_code' => $validated['product_code'],
                'brand' => $validated['brand'],
                'name' => $validated['name'],
                'model' => $validated['model'] ?? '',
                'country_of_origin' => $validated['country_of_origin'] ?? '',
                'website_url' => $validated['website_url'] ?? '',
                'description' => $validated['description'] ?? '',
                'status_id' => $validated['status_id'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'user_id' => $request->user()?->id,
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
                    ]
                );

                ProductSpecificationValue::create([
                    'product_id' => $product->id,
                    'specification_id' => $specification->id,
                    'value' => $row['value'],
                ]);
            }

            foreach (['main_image' => 'main', 'thumbnail_image' => 'thumbnail'] as $inputName => $type) {
                if (! $request->hasFile($inputName)) {
                    continue;
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $request->file($inputName)->store('products', 'public'),
                    'type' => $type,
                ]);
            }
        });

        return response()->json([
            'message' => 'Product created successfully.',
            'redirect' => route('products.index'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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

        $product = ($products) ? $products[0]: null;
        if ($product) {
            return response()->json([
                'data'=>$product
            ]);
        } else {
            return response()->json(["error"=>"Product code doesn't exist."]);
        }
    }

}
