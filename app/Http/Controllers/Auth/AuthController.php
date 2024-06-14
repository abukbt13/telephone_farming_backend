<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function createUser(Request $request)
    {
        $userAgent = $request->header('User-Agent');

        // Use a library like jenssegers/agent to parse the User-Agent string
        $agent = new \Jenssegers\Agent\Agent();

        // Detect the platform using the parsed User-Agent string
        $platform = $agent->platform();

        $rules = [
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'name' => 'required',
            'phone' => 'required',
        ];
        $data = request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())){
            return response([
                'status' => 'failed',
                'error' => $valid->errors()
            ]);
        }
        $user = new User();
        $user->email = $data['email'];
        $user->name = $data['name'];
        $user->phone = $data['phone'];
        $user->email = $data['email'];
        $user->role = "telephone_farmer";
        $user->password = Hash::make($request->password);
        $user->save();
        storelog('Created a new user in', $user,$platform);
        if (Auth::attempt(['email' => $data['email'], 'password' => $data ['password']])) {
            $token = $user->createToken('token')->plainTextToken;
            return response([
                'status'=>'success',
                'token'=>$token,
                'user'=>$user
            ]);
        }
    }
    public function login(Request $request)
    {
        $userAgent = $request->header('User-Agent');

        // Use a library like jenssegers/agent to parse the User-Agent string
        $agent = new \Jenssegers\Agent\Agent();

        // Detect the platform using the parsed User-Agent string
        $platform = $agent->platform();

        $data = request()->all();
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ],422);
        }
        $email = request('email');
        $password = request('password');
        $user = User::where('email', $email)->get()->first();

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            storelog('Sign in', $user,$platform);
            $token = $user->createToken('token')->plainTextToken;

            return response([
                'status' => 'success',
                'token' => $token,
                'user' => request()->user()
            ]);
        }
        else{
            return response([
                'status' => 'failed',
                'message' => 'Enter correct details',
            ]);
        }
    }
    public function auth(){
        if (Auth::check()) {
            return response()->json(['authenticated' => true]);
        } else {
            return response()->json(['authenticated' => false]);
        }
    }
}
