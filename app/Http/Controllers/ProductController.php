<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $results = Product::query();


        // $products = $results->paginate(15);
        return view("products.index",compact(
            // "products",
            []
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Static sample data for the product form prototype. No database records are used here.
        $sampleCategories = [
            'water-pump' => ['name' => 'Water Pump', 'group' => 'Garden'],
            'bathtub' => ['name' => 'Bathtub', 'group' => 'Sanitary'],
            'ceiling-board' => ['name' => 'Ceiling Board', 'group' => 'Roofing & Ceiling'],
        ];

        $sampleAttributes = ['Power', 'Maximum Head', 'Flow Rate', 'Inlet Size', 'Outlet Size', 'Weight', 'Material', 'Color'];
        $sampleBrands = ['IM Dayuan', 'Cotto', 'DECO', 'Ispa', 'TOTO', 'Zhangshi'];
        $sampleStatuses = ['Draft', 'Active'];


        // Start Store Attribute
        // $normalizedName = Str::slug($attributeName);

        // $attribute = Attribute::firstOrCreate(
        //     ['normalized_name' => $normalizedName],
        //     [
        //         'name' => trim($attributeName),
        //         'created_by' => auth()->id(),
        //     ]
        // );

        // $product->attributeValues()->updateOrCreate(
        //     ['attribute_id' => $attribute->id],
        //     [
        //         'value' => $attributeValue,
        //         'sort_order' => $index,
        //     ]
        // );
        // End Store Attribute

        return view('products.create', compact('sampleCategories', 'sampleAttributes', 'sampleBrands', 'sampleStatuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
