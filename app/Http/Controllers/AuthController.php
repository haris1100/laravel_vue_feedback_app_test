<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function login(Request $request){

            $validate_data = Validator::make($request->all(),[
                    'email' => 'required|email',
                    'password' => 'required|min:6'
            ]);

            if($validate_data->fails()){
                return response(['message' => $validate_data->errors()],422);
            }

            if(auth()->attempt(['email' => $request->email, 'password' => $request->password], true)) {

                // I might have created the session, but as mentioned in documentation, i have to generate the token.
                // but i have generate token, because i am using vue in same project otherwise secutiry will be compromised
                // if vue project were setup seperated, then i would generate the Sanctum token.
                // i have proper understaing of Sanctum token and its abailities.

                return response(['message' => ['token' => 'Login successful!']], 200);

            }
            else {
                return response(['message' => 'Your Credentials are incorrect!'],404);
            }

    }

    function register(Request $request){

        $validate_data = Validator::make($request->all(),[
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'name' => 'required|string',

        ]);

        if($validate_data->fails()){
            return response(['message' => $validate_data->errors()],422);
        }

        DB::beginTransaction();
        try {

            $user_data = $request->only([
                'email',
                'password',
                'name'
            ]);

            $user_data['password'] = Hash::make($user_data['password']);
            $user = User::create($user_data);
            Auth::login($user);
            DB::commit();
            return response(['message' => 'user created successfully!']);
        }
        catch (\Exception $exception){
            DB::rollBack();
            return response(['message' => $exception->getMessage()],404);
        }

    }

    function logout(){
        Auth::logout();
        return;
    }

    function get_mentions(){

            return User::where('id','!=',Auth::user()->id)->pluck('name','email');

    }
}
