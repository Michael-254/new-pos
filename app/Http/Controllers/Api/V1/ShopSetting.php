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
    // public function getShopData(Request $request)
    // {
    //     $data['shopName'] = DB::table('business_settings')->updateOrInsert(['key' => 'shop_name'], [
    //         'value' => $request['shop_name']
    //     ]);

    //     $data['shop_email'] = DB::table('business_settings')->updateOrInsert(['key' => 'shop_email'], [
    //         'value' => $request['shop_email']
    //     ]);

    //     $data['shop_phone'] = DB::table('business_settings')->updateOrInsert(['key' => 'shop_phone'], [
    //         'value' => $request['shop_phone']
    //     ]);

    //     $data['shop_address'] = DB::table('business_settings')->updateOrInsert(['key' => 'shop_address'], [
    //         'value' => $request['shop_address']
    //     ]);

    //     $data['pagination_limit'] = DB::table('business_settings')->updateOrInsert(['key' => 'pagination_limit'], [
    //         'value' => $request['pagination_limit']
    //     ]);

    //     $data['currency'] = DB::table('business_settings')->updateOrInsert(['key' => 'currency'], [
    //         'value' => $request['currency']
    //     ]);

    //     $data['country'] = DB::table('business_settings')->updateOrInsert(['key' => 'country'], [
    //         'value' => $request['country']
    //     ]);

    //     $data['footer_text'] = DB::table('business_settings')->updateOrInsert(['key' => 'footer_text'], [
    //         'value' => $request['footer_text']
    //     ]);

    //     $curr_logo = BusinessSetting::where(['key' => 'shop_logo'])->first();
    //     $data['curr_logo'] = DB::table('business_settings')->updateOrInsert(['key' => 'shop_logo'], [
    //         'value' => $request->has('shop_logo') ? Helpers::update('shop/', $curr_logo->value, 'png', $request->file('shop_logo')) : $curr_logo->value
    //     ]);
    //     $data['time_zone'] = DB::table('business_settings')->updateOrInsert(['key' => 'time_zone'], [
    //         'value' => $request['time_zone'],
    //     ]);
    //     return response()->json([
    //         'shopInfo' => $data, 'shopLogo' => $curr_logo
    //     ]);
    // }

    public function getShopData(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $curr_logo = BusinessSetting::where(['company_id' => $company_id])->first();

        if ($request->pagination_limit == 0) {
            Toastr::warning(translate('pagination_limit_is_required'));
            return back();
        }

        $data = DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
            'shop_name' => $request['shop_name'],
            'shop_email' => $request['shop_email'],
            'shop_phone' => $request['shop_phone'],
            'shop_address' => $request['shop_address'],
            'pagination_limit' => $request['pagination_limit'],
            'stock_limit' => $request['stock_limit'],
            'currency' => $request['currency'],
            'country' => $request['country'],
            'footer_text' => $request['footer_text'],
            'shop_logo' => $request->has('shop_logo') ? Helpers::update('shop/', $curr_logo->shop_logo, 'png', $request->file('shop_logo')) : $curr_logo->shop_logo,
            'time_zone' => $request['time_zone'],
            'vat_reg_no' => $request['vat_reg_no'],
        ]);

        // DB::table('mpesa_credentials')->updateOrInsert(['company_id' => $company_id], [
        //     'consumer_key' => $request['consumer_key'],
        //     'consumer_secret' => $request['consumer_secret'],
        //     'test_consumer_key' => $request['test_consumer_key'],
        //     'test_consumer_secret' => $request['test_consumer_secret'],
        //     'environment' => $request['environment'],
        //     'shortcode' => $request['shortcode'],
        //     'security_credential' => $request['security_credential'],
        //     'lipa_na_mpesa_passkey' => $request['lipa_na_mpesa_passkey'],
        // ]);

        return response()->json([
            'shopInfo' => $data, 'shopLogo' => $curr_logo
        ]);
    }
}
