<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //Admin Login
    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        //Get authenticated admin
        $admin = Admin::where('email', $request->email)->first();
        //Check Above Admin
        if ($admin) {
            if (Hash::check($request->password, $admin->password)) {
                $token = $admin->createToken('LaravelPassportClient')->accessToken;
                return response()->json(
                    ['message' => 'You are logged in', 'token' => $token],
                    200
                );
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => 'Wrong credentials! please input correct email and password'];
            return response($response, 422);
        }
    }
    //Password Change
    public function passwordChange(Request $request)
    {
        $adminId = Auth::guard('admin-api')->user()->id;
        $validator = Validator::make($request->all(), [
            'password' => 'required|same:confirm_password|min:8',
            'confirm_password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        if (isset($adminId)) {
            DB::table('admins')->where(['id' => $adminId])->update([
                'password' => bcrypt($request['confirm_password'])
            ]);
            return response()->json(['message' => 'Password changed successfully.'], 200);
        }
    }
    //Password Change
    public function profile()
    {
        $profile = Auth::guard('admin-api')->user();
        return response()->json($profile, 200);
    }
    //log out user
    public function logOut(Request $request)
    {
        try {
            $request->admin()->token()->revoke();
            return response()->json([
                'message' => 'Successfully logged out',
                "success" => true
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something wrong',
                "success" => false
            ], 403);
        }
    }
}
