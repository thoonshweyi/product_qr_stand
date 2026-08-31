<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

    public function dashboard()
    {
        $products = Product::query();

        $workflowExists = function ($query, $slug) {
            $query->selectRaw('1')
                ->from('product_workflows')
                ->join('workflows', 'workflows.id', '=', 'product_workflows.workflow_id')
                ->whereColumn('product_workflows.product_id', 'products.id')
                ->where('workflows.slug', 'like', "%{$slug}%");
        };

        $stand = (clone $products)
            ->whereExists(fn ($q) => $workflowExists($q, 'stand'))
            ->count();

        $online = (clone $products)
            ->whereExists(fn ($q) => $workflowExists($q, 'online'))
            ->count();

        $standAndOnline = (clone $products)
            ->whereExists(fn ($q) => $workflowExists($q, 'stand'))
            ->whereExists(fn ($q) => $workflowExists($q, 'online'))
            ->count();

        $datas = [
            "totalproducts" => (clone $products)->count(),
            "stand" => $stand,
            "online" => $online,
            "standandonline" => $standAndOnline,
        ];

        return response()->json($datas);
    }

    // public function dashboard(){
    //     $leaves = Leave::all();

    //     $datas = [
    //         "totalleaves" => $leaves->count(),
    //         "approved" => $leaves->where("stage_id",1)->count(),
    //         "pending" => $leaves->where("stage_id",2)->count(),
    //         "rejeted" => $leaves->where("stage_id",3)->count(),
    //     ];

    //     return response()->json($datas);
    // }
}
