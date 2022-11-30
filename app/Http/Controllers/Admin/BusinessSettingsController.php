<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\MpesaCredential;
use Illuminate\Support\Facades\DB;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class BusinessSettingsController extends Controller
{
    public function shop_index()
    {
        return view('admin-views.business-settings.shop-index');
    }
    public function shop_setup(Request $request)
    {
        $company_id = auth()->guard('admin')->user()->company_id;
        $curr_logo = BusinessSetting::where(['company_id' => $company_id])->first();

        if ($request->pagination_limit == 0) {
            Toastr::warning(translate('pagination_limit_is_required'));
            return back();
        }

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

        Toastr::success(translate('Settings updated'));
        return back();
    }
    public function shortcut_key()
    {
        return view('admin-views.business-settings.shortcut-key-index');
    }
}
