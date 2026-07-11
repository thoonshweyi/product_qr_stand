<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Department;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Status;
use App\Models\User;
use App\Models\UserBranch;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $request->validate([
            "name"=>"required",
            "employee_id"=>"required",
            "status_id"=>"required",
            "branch_id"=>"required",
            "department_id"=>"required",
            "password"=> "required"
        ]);


        // dd("hay");

        $user = User::create([
            'name' => $request['name'],
            'email' => $request["email"],
            'password' => Hash::make($request["password"]),
            'employee_id' => $request["employee_id"],
            'branch_id' => $request["branch_id"],
            'status_id' => $request["status_id"],
            'department_id' => $request["department_id"],
        ]);

       
        if(!empty($request["branch_ids"])){
            foreach($request["branch_ids"] as $branch){
                $branchdatas = [
                    'user_id'=> $user["id"],
                    'branch_id'=> $branch
                ];
                UserBranch::insert($branchdatas);
            }
        }

        if(!empty($request["category_ids"])){
            foreach ($request["category_ids"] as $category_id) {
                $user_category = new UserCategory();
                $user_category->user_id = $user->id;
                $user_category->category_id = $category_id;
                $user_category->save();
            }
        }

        $roleuser = new RoleUser();
        $roleuser->role_id = $request["role_id"];
        $roleuser->user_id = $user["id"];
        $roleuser->save();


        return $this->sendRespond($user,"New User created successfully");
    }

}
