<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Admin;
use App\Models\Company;
use App\Models\BusinessSetting;
use App\Models\CustomerLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //Customer Register
    public function customerRegister(Request $request, Company $company, BusinessSetting $busSettings)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'password' => 'required',
            'usertype' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

		if($request->usertype == 'admin') {
			$company->company_name = $request->f_name;
            $company->save();

			$busSettings->company_id = $company->id;
            $busSettings->save();
			
			$admin = Admin::create([
				'f_name' => $request->f_name,
				'l_name' => $request->l_name,
				'email' => $request->email,
				'phone' => $request->phone,
				'password' => Hash::make($request->password),
				'company_id' => $company->id
			]);

            $admin->givePermissionTo('can_make_sales');
            $admin->givePermissionTo('can_give_discounts');
            $admin->givePermissionTo('can_add_stock-in');
            $admin->givePermissionTo('can_add_new_products');
            $admin->givePermissionTo('can_add_expenses');
            $admin->givePermissionTo('can_view & manage_customers');
            $admin->givePermissionTo('can_view & manage_suppliers');
            $admin->givePermissionTo('can_view & manage_users');
            $admin->givePermissionTo('can_view_stock_balance');
            $admin->givePermissionTo('can_view_other_shops_stock_balance');
            $admin->givePermissionTo('can_count_and_update_stock_balance');
            $admin->givePermissionTo('can_edit_daily_entries');
            $admin->givePermissionTo('can_delete_daily_entries');
            $admin->givePermissionTo('can_back_date_entries');

			$token = $admin->createToken('LaravelPassportClient')->accessToken;

			return response()->json(
				['message' => 'You are logged in', 'token' => $token, 'user_id' => $admin->id, 'user_type' => $request->usertype, 'fname' => $admin->f_name, 'lname' => $admin->l_name, 'phone' => $admin->phone],
				200
			);
		}
		else {
			$customer = CustomerLogin::create([
				'f_name' => $request->f_name,
				'l_name' => $request->l_name,
				'email' => $request->email,
				'phone' => $request->phone,
				'password' => Hash::make($request->password)
			]);

			$token = $customer->createToken('LaravelPassportClient')->accessToken;

			return response()->json(
				['message' => 'You are logged in', 'token' => $token, 'user_id' => $customer->id, 'user_type' => $request->usertype, 'fname' => $customer->f_name, 'lname' => $customer->l_name, 'phone' => $customer->phone, 'permissions' => $admin->permissions],
				200
			);
		}
    }

    //Admin Login
    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'usertype' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->usertype == 'admin') {
            //Get authenticated admin
            $admin = Admin::where('email', $request->email)->with('permissions')->first();
			$permissions = $admin->getPermissionNames();

            //Check Above Admin
            if ($admin) {
                if (Hash::check($request->password, $admin->password)) {
                    $token = $admin->createToken('LaravelPassportClient')->accessToken;
                    return response()->json(
                        ['message' => 'You are logged in', 'token' => $token, 'user_id' => $admin->id, 'user_type' => $request->usertype, 'fname' => $admin->f_name, 'lname' => $admin->l_name, 'phone' => $admin->phone, 'permissions' => $permissions],
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
        } else {
            //Get authenticated customer
            $customer = CustomerLogin::where('email', $request->email)->OrWhere('phone', $request->email)->first();

            //Check Above Customer
            if ($customer) {
                if (Hash::check($request->password, $customer->password)) {
                    $token = $customer->createToken('LaravelPassportClient')->accessToken;
                    return response()->json(
                        ['message' => 'You are logged in', 'token' => $token, 'user_id' => $customer->id, 'user_type' => $request->usertype, 'fname' => $customer->f_name, 'lname' => $customer->l_name, 'phone' => $customer->phone],
                        200
                    );
                } else {
                    $response = ["message" => "Password mismatch"];
                    return response($response, 422);
                }
            } else {
                $response = ["message" => 'Wrong credentials! please input correct email/phone number and password'];
                return response($response, 422);
            }
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
