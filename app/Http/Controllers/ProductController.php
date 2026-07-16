<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecificationValue;
use App\Models\Specification;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        return view('products.create', compact('categories', 'statuses', 'specifications', 'brands'));
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
}
