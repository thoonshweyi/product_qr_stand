<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request){
        $results = User::query();

        $statuses = Status::whereIn("id",[1,2])->orderBy('id','asc')->get();
        $branches = Branch::where('status_id',3)->orderBy('id','asc')->get();
        $departments = Department::where('status_id',3)->orderBy('id','asc')->get();
        $categories = Category::where('status_id',3)->orderBy('id','asc')->get();
        $roles = Role::where('status_id',3)->orderBy('id','asc')->get();

        $users = $results->paginate(15);
        // dd($users);
        return view("users.index",compact(
            "users",
            "statuses",
            "branches",
            "departments",
            "categories",
            "roles"
        ));
    }


    public function store(Request $request)
    {
       

        //    $roleuser = new RoleUser();
        //    $roleuser->role_id = $request["role_id"];
        //    $roleuser->user_id = $request["user_id"];
        //    $roleuser->save();


       return redirect(route("roleusers.index"));
    }

}
