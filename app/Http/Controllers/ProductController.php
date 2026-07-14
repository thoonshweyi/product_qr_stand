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
            'water-pump' => [
                'name' => 'Water Pump',
                'group' => 'Garden',
                'attributes' => [
                    ['key' => 'power', 'label' => 'Power', 'type' => 'number', 'value' => '370', 'unit' => 'W', 'placeholder' => 'e.g. 370'],
                    ['key' => 'high_max', 'label' => 'Maximum Head', 'type' => 'number', 'value' => '45', 'unit' => 'm', 'placeholder' => 'e.g. 45'],
                    ['key' => 'suction_max', 'label' => 'Maximum Suction', 'type' => 'number', 'value' => '9', 'unit' => 'm', 'placeholder' => 'e.g. 9'],
                    ['key' => 'flow_rate', 'label' => 'Flow Rate', 'type' => 'number', 'value' => '35', 'unit' => 'L/min', 'placeholder' => 'e.g. 35'],
                    ['key' => 'inlet_size', 'label' => 'Inlet Size', 'type' => 'number', 'value' => '25', 'unit' => 'mm', 'placeholder' => 'e.g. 25'],
                    ['key' => 'outlet_size', 'label' => 'Outlet Size', 'type' => 'number', 'value' => '25', 'unit' => 'mm', 'placeholder' => 'e.g. 25'],
                ],
            ],
            'bathtub' => [
                'name' => 'Bathtub',
                'group' => 'Sanitary',
                'attributes' => [
                    ['key' => 'series', 'label' => 'Series', 'type' => 'text', 'value' => 'DONNA Series', 'unit' => '', 'placeholder' => 'e.g. DONNA Series'],
                    ['key' => 'type', 'label' => 'Type', 'type' => 'select', 'value' => 'Plain Bathtub', 'unit' => '', 'options' => ['Plain Bathtub', 'Free Standing Bathtub', 'Whirlpool Bathtub']],
                    ['key' => 'material', 'label' => 'Material', 'type' => 'select', 'value' => 'Acrylic Fiber', 'unit' => '', 'options' => ['Acrylic Fiber', 'Virgin Acrylic', 'Ceramic']],
                    ['key' => 'length', 'label' => 'Length', 'type' => 'number', 'value' => '1700', 'unit' => 'mm', 'placeholder' => 'e.g. 1700'],
                    ['key' => 'width', 'label' => 'Width', 'type' => 'number', 'value' => '730', 'unit' => 'mm', 'placeholder' => 'e.g. 730'],
                    ['key' => 'weight', 'label' => 'Weight', 'type' => 'number', 'value' => '24', 'unit' => 'kg', 'placeholder' => 'e.g. 24'],
                ],
            ],
            'ceiling-board' => [
                'name' => 'Ceiling Board',
                'group' => 'Roofing & Ceiling',
                'attributes' => [
                    ['key' => 'thickness', 'label' => 'Thickness', 'type' => 'number', 'value' => '7.5', 'unit' => 'mm', 'placeholder' => 'e.g. 7.5'],
                    ['key' => 'width', 'label' => 'Width', 'type' => 'number', 'value' => '603', 'unit' => 'mm', 'placeholder' => 'e.g. 603'],
                    ['key' => 'length', 'label' => 'Length', 'type' => 'number', 'value' => '603', 'unit' => 'mm', 'placeholder' => 'e.g. 603'],
                    ['key' => 'packing_quantity', 'label' => 'Packing Quantity', 'type' => 'number', 'value' => '10', 'unit' => 'pcs', 'placeholder' => 'e.g. 10'],
                    ['key' => 'usage_location', 'label' => 'Usage Location', 'type' => 'select', 'value' => 'Indoor', 'unit' => '', 'options' => ['Indoor', 'Outdoor', 'Indoor & Outdoor']],
                    ['key' => 'color', 'label' => 'Color', 'type' => 'select', 'value' => 'Gold', 'unit' => '', 'options' => ['Gold', 'White', 'Grey']],
                ],
            ],
        ];

        $sampleBrands = ['IM Dayuan', 'Cotto', 'DECO', 'Ispa', 'TOTO', 'Zhangshi'];
        $sampleStatuses = ['Draft', 'Active'];

        return view('products.create', compact('sampleCategories', 'sampleBrands', 'sampleStatuses'));
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
