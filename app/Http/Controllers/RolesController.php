<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RolesController extends Controller
{

    public function index()
    {
        $roles = Role::where(function($query){
            if($statusid = request("filterstatus_id")){
                $query->where("status_id",$statusid);
            }
        })->paginate(5);
        $filterstatuses = Status::whereIn("id",[3,4])->get()->pluck('name',"id")->prepend("Choose Status","");
        // dd($filterstatuses);
        return view("roles.index",compact("roles"),compact("filterstatuses"));
    }
    // it doesn't need "LIKE" Sytax because status_id can be checked directly. 

    public function create()
    {    
         //$statuses = Status::all(); // get all statuses
         $statuses = Status::whereIn("id",[3,4])->get()->pluck('name',"id");
        //  dd($statuses);
         return view("roles.create",compact("statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:roles,name",
            "image" => "image|mimes:jpg,jpeg,png|max:1024",
            "status_id" => "required|in:3,4",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $role = new Role();
       $role->name = $request["name"];
       $role->slug = Str::slug($request["name"]);
       $role->status_id = $request["status_id"];
       $role->user_id = $user_id;

        // Single Image Upload
        if(file_exists($request["image"])){
            $file = $request["image"];
            $fname = $file->getClientOriginalName();
            $imagenewname = uniqid($user_id).$role['id'].$fname;
            // $file->move(public_path("roles/img"),$imagenewname);
            $file->move(public_path("assets/img/roles"),$imagenewname);
            
            $filepath = "assets/img/roles/".$imagenewname;
            $role->image = $filepath;
        }    

       $role->save();
       return redirect(route("roles.index"));
    }

    // public function show(string $id)
    // {
    //     $role = Role::findOrFail($id);
    //     return view("roles.show",["role"=>$role]);
    // }

    // note: we have to defined route binding into Providers > RouteServiceProvider.php
    public function show(Role $role)
    {
        return view("roles.show",["role"=>$role]);
    }


    public function edit(Role $role)
    {
        // $role = Role::findOrFail($id);
        $statuses = Status::whereIn("id",[3,4])->get();
        return view("roles.edit")->with("role",$role)->with("statuses",$statuses);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "name" => ["required","max:50","unique:roles,name,".$id],
            "image" => ["image","mimes:jpg,jpeg,png","max:1024"],
            "status_id" => ["required","in:3,4"],
        ]);

        $user = Auth::user();
        $user_id = $user->id;

        $role = Role::findOrFail($id);
        $role->name = $request["name"];
        $role->slug = Str::slug($request["name"]);
        $role->status_id = $request["status_id"];
        $role->user_id = $user_id;

        // Remove Old Image
        if($request->hasFile("image")){
            $path = $role->image;

            if(File::exists($path)){
                File::delete($path);
            }
        }

        // Single Image Upload
        if($request->hasFile("image")){
            $file = $request->file("image");
            $fname = $file->getClientOriginalName();
            $imagenewname = uniqid($user_id).$role['id'].$fname;
            $file->move(public_path("assets/img/roles"),$imagenewname);
            
            $filepath = "assets/img/roles/".$imagenewname;
            $role->image = $filepath;
        }    

        $role->save();
        return redirect(route("roles.index"));
    }


    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        
        // Remove Old Image
        $path = $role->image;
        if(File::exists($path)){
            File::delete($path);
        }
        
        $role->delete();
        return redirect()->back();
    }
}
