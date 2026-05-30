<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){

        try{

        $usuario = User::where('email', $request->email)->first();

        if(empty($usuario)){
            return response()->json(['status'=>'error'], 401);
        }

        if(!Hash::check($request->password, $usuario->password)){
            return response()->json(['status'=>'error'], 401);  
        }

        $token = $usuario->createToken('token_largo_para_que_cumpla_los_32_digitos')->plainTextToken;

        return response()->json(['status'=>'ok','token'=> $token], 200);

        } catch(\PDOException $e){

            return response()->json(['status'=>'error'], 500);
        }
    }
}
