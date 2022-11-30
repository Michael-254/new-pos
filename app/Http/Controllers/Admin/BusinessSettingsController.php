<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class BusinessSettingsController extends Controller
{
    public function shop_index()
    {
        $businessSetting = BusinessSetting::where(['company_id' => auth('admin')->user()->company_id])->first();
        return view('admin-views.business-settings.shop-index', compact('businessSetting'));
    }
    public function shop_setup(Request $request)
    {
        if ($request->pagination_limit == 0) {
            Toastr::warning(translate('pagination_limit_is_required'));
            return back();
        }

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'shop_name' => $request['shop_name']
        ]);

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'shop_email' => $request['shop_email']
        ]);

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'shop_phone' => $request['shop_phone']
        ]);

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'shop_address' => $request['shop_address']
        ]);

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'pagination_limit' => $request['pagination_limit']
        ]);

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'stock_limit' => $request['stock_limit']
        ]);

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'currency' => $request['currency']
        ]);

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'country' => $request['country']
        ]);

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'footer_text' => $request['footer_text']
        ]);

        $curr_logo = BusinessSetting::where('company_id', auth('admin')->user()->company_id)->first();
        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'shop_logo' => $request->has('shop_logo') ? Helpers::update('shop/', $curr_logo->value, 'png', $request->file('shop_logo')) : $curr_logo->value
        ]);

        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'time_zone' => $request['time_zone'],
        ]);
        DB::table('business_settings')->where('company_id', auth('admin')->user()->company_id)->update([
            'vat_reg_no' => $request['vat_reg_no'],
        ]);
        Toastr::success(translate('Settings updated'));
        return back();
    }
    public function shortcut_key()
    {
        return view('admin-views.business-settings.shortcut-key-index');
    }
}
