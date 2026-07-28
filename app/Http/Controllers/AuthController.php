<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\StoreBusinessRequest;


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

        $token = $usuario->createToken('Postman')->plainTextToken;

        return response()->json(['status'=>'ok','token'=> $token,'rol'=>$usuario->role,'business'=>$usuario->business_id], 200);

        } catch(\PDOException $e){

            return response()->json(['status'=>'error', ], 500);
        }
    }

    public function registerCustomer(StoreUserRequest $request){

        try{

            $user_data = $request->validated();

            $user_data['password'] = Hash::make($user_data['password']);
            $user_data['role'] = 'customer';
            $user_data['business_id'] = null;

            $user = User::create($user_data);

            $token = $user->createToken('Postman')->plainTextToken;
            return response()->json(['status'=>'ok', 'Token'=>$token], 200);

        } catch(Exception $e){
            return response()->json(['status'=>'error'],500);
        }
    }

    public function registerBusiness(StoreBusinessRequest $request){

        try{
           $validation = $request->validated(); 

           $result = DB::transaction(function () use ($validation) {

            $business = Business::create([
                'name' => $validation['business_name'],
                'slug' => Str::slug($validation['business_name']),
            ]);

            $userData = [
                'name' => $validation['name'],
                'email' => $validation['email'],
                'password' => Hash::make($validation['password']),
                'role' => 'owner', // O 'business', el que prefieras usar
                'business_id' => $business->id,
            ];

           $user = User::Create($userData);

           $token = $user->reateToken('Postaman')->plainTextToken;

           return $token;

        });

           return response()->json(['status'=>'ok', 'Token'=>$result], 200);


        }catch(Exception $e){
            return response()->json(['status'=>'error'], 500);
        }
        

    }
}
