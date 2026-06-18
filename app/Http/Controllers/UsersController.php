<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request){
        $results = User::query();


        $users = $results->paginate(15);
        // dd($users);
        return view("users.index",compact("users"));
    }
}
