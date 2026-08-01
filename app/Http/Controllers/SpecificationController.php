<?php

namespace App\Http\Controllers;

use App\Http\Resources\SpecificationsResource;
use App\Models\Category;
use App\Models\Specification;
use App\Models\Status;
use Illuminate\Http\Request;

class SpecificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = Specification::query();

        $statuses = Status::whereIn("id",[1,2])->orderBy('id','asc')->get();
        $categories = Category::where('status_id',3)->orderBy('id','asc')->get();

        $specifications = $results->orderBy('id','desc')->with('status')
                ->with('user')
                ->with('category')
                ->paginate(15);
        // dd($users);

        if(request()->ajax()){
            return  SpecificationsResource::collection($specifications);
            // return $this->sendRespond(SpecificationsResource::collection($specifications),"Fetch Specifications Successfully!.");
        }

        return view("specifications.index",compact(
            "specifications",
            "statuses",
            "categories",
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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


    public function changestatus(Request $request){
        $product = Specification::findOrFail($request["id"]);
        $product->status_id = $request["status_id"];
        $product->save();

        return response()->json(["success"=>"Status Change Successfully"]);
    }
}
