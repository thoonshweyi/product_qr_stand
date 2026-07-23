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

        $users = $results->orderBy('id','desc')->with('branch')
                ->with('department')
                ->with('roles')
                ->paginate(15);
        // dd($users);

        if(request()->ajax()){
            return $this->sendRespond($users,"Fetch Users Successfully!.");
        }

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

    public function show($id){
        $user = User::with('branch')
                ->with('department')
                ->with('roles')
                ->with('branches')
                ->with('categories')
                ->findOrFail($id);

        return $this->sendRespond($user,"Fetch Single User Successfully!.");
    }

    public function update(Request $request, $id){
        $request->validate([
            "name"=>"required",
            "employee_id"=>"required",
            "status_id"=>"required",
            "branch_id"=>"required",
            "department_id"=>"required",
            // "password"=> "required"
        ]);

        $user = User::findOrFail($id);

        $updateData = $request->except('password');
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        $user->update($updateData);


        if(!empty($request["branch_ids"])){
            UserBranch::where('user_id',$user->id)->delete();

            foreach($request["branch_ids"] as $branch){
                $branchdatas = [
                    'user_id'=> $user["id"],
                    'branch_id'=> $branch
                ];
                UserBranch::insert($branchdatas);
            }
        }

        if(!empty($request["category_ids"])){
            $old_data = UserCategory::where('user_id', $user->id)->get();
            foreach ($old_data as $data) {
                $data->delete();
            }

            foreach ($request["category_ids"] as $category_id) {
                $user_category = new UserCategory();
                $user_category->user_id = $user->id;
                $user_category->category_id = $category_id;
                $user_category->save();
            }
        }

        $user->roles()->sync($request->role_id ?? []);

        return $this->sendRespond($user,"User Updated successfully");

    }

    public function destroy(string $id){
        $user = User::findOrFail($id);

        UserBranch::where('user_id', $user->id)->delete();

        UserCategory::where('user_id',$user->id)->delete();

        $user->roles()->detach();

        $user->delete();

        return $this->sendRespond($id,"User Deleted successfully");
    }   

}
