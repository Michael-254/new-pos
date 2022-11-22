<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Account;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\OrderDetail;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use function App\CPU\translate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;

class ShopSetting extends Controller
{
    public function getShopData(Request $request)
    {
        $company_id = auth()->guard('admin')->user()->company_id;

        $data['shopName'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'shop_name' => $request['shop_name']
        ]);

        $data['shop_email'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'shop_email' => $request['shop_email']
        ]);

        $data['shop_phone'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'shop_phone' => $request['shop_phone']
        ]);

        $data['shop_address'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'shop_address' => $request['shop_address']
        ]);

        $data['pagination_limit'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'pagination_limit' => $request['pagination_limit']
        ]);

        $data['currency'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'currency' => $request['currency']
        ]);

        $data['country'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'country' => $request['country']
        ]);

        $data['footer_text'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'footer_text' => $request['footer_text']
        ]);

        $curr_logo = BusinessSetting::where(['company_id' => $company_id])->first();
        $data['curr_logo'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'shop_logo' => $request->has('shop_logo') ? Helpers::update('shop/', $curr_logo->shop_logo, 'png', $request->file('shop_logo')) : $curr_logo->shop_logo
        ]);
        $data['time_zone'] = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'time_zone' => $request['time_zone'],
        ]);

        return response()->json([
            'shopInfo' => $data, 'shopLogo' => $curr_logo
        ]);
    }
}
