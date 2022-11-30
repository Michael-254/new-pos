<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function updateShop(Request $request)
    {
        $company_id = auth()->user()->company_id;
        $curr_logo = BusinessSetting::where(['company_id' => $company_id])->first();

        DB::table('business_settings')->updateOrInsert(['company_id' => $company_id], [
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

        DB::table('mpesa_credentials')->updateOrInsert(['company_id' => $company_id], [
            'consumer_key' => $request['consumer_key'],
            'consumer_secret' => $request['consumer_secret'],
            'test_consumer_key' => $request['test_consumer_key'],
            'test_consumer_secret' => $request['test_consumer_secret'],
            'environment' => $request['environment'],
            'shortcode' => $request['shortcode'],
            'security_credential' => $request['security_credential'],
            'lipa_na_mpesa_passkey' => $request['lipa_na_mpesa_passkey'],
        ]);

        if ($request->pagination_limit == 0) {
            Toastr::warning(translate('pagination_limit_is_required'));
            return back();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Shop updated succefully',
        ], 200);
    }
    public function configuration()
    {
        $company_id = 1; //auth()->user()->company_id;
        // $key = [
        //     'shop_logo',
        //     'pagination_limit',
        //     'currency',
        //     'shop_name',
        //     'shop_address',
        //     'shop_phone',
        //     'shop_email',
        //     'footer_text',
        //     'app_minimum_version_ios',
        //     'country',
        //     'stock_limit',
        //     'time_zone',
        //     'vat_reg_no'
        // ];
        $config_key_value_array =  array_column(BusinessSetting::select('shop_logo', 'pagination_limit', 'currency', 'shop_name', 'shop_address', 'shop_phone', 'shop_email', 'footer_text', 'app_minimum_version_ios', 'country', 'stock_limit', 'time_zone', 'vat_reg_no')->where('company_id', $company_id)->get()->toArray(), 'value', 'key');
        return response()->json([
            'business_info' => $config_key_value_array,
            'currency_symbol' => Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol,
            'base_urls' => [
                'category_image_url' => asset('storage/category'),
                'brand_image_url' => asset('storage/brand'),
                'product_image_url' => asset('storage/product'),
                'supplier_image_url' => asset('storage/supplier'),
                'shop_image_url' => asset('storage/shop'),
            ]
        ], 200);
    }
}
