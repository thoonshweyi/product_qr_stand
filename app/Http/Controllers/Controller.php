<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function sendRespond($result,$message=""){
        $response = [
            'success'=>true,
            'data'=>$result,
            'message'=>$message
        ];
        return response()->json($response,200);
    }

    public function sendError($message=[],$errors=[],$code=404){
        $response = [
            'success'=>false,
            'message'=>$message
        ];
        // dd($errors);
        if(!empty($errors) || count($errors) > 0){
            $response['data'] = $errors;
        }

        return response()->json($response,$code);
    }
}
