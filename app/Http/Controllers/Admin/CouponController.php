<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use function App\CPU\translate;

class CouponController extends Controller
{
    public function add_new(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $coupons = Coupon::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%")
                        ->orWhere('code', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $coupons = new Coupon();
        }

        $coupons = $coupons->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.coupon.index', compact('coupons', 'search'));
    }
    public function store(Request $request)
    {
        //return $request->all();
        $request->validate([
            'title' => 'required',
            'coupon_type'=>'required',
            'code' => 'required|unique:coupons',
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required'
        ]);

        DB::table('coupons')->insert([
            'title' => $request->title,
            'code' => $request->code,
            'user_limit' => $request->coupon_type !='default'? 1 : $request->user_limit,
            'coupon_type' => $request->coupon_type,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : $request->discount,
            'discount' => $request->discount_type == 'amount' ? $request->discount : $request['discount'],
            'discount_type' => $request->discount_type,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Toastr::success(translate('Coupon added successfully'));
        return back();
    }
    public function status(Request $request)
    {
        $coupon = Coupon::find($request->id);
        $coupon->status = $request->status;
        $coupon->save();

        Toastr::success(translate('Coupon status updated'));
        return back();
    }
    public function edit($id)
    {
        $coupon = Coupon::where(['id' => $id])->first();
        return view('admin-views.coupon.edit', compact('coupon'));
    }
    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);
        $request->validate([
            'title' => 'required',
            'coupon_type'=>'required',
            'code' => 'required|unique:coupons,code,'.$coupon->id,
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required'
        ]);

        DB::table('coupons')->where(['id' => $id])->update([
            'title' => $request->title,
            'code' => $request->code,
            'user_limit' => $request->coupon_type !='default'? 1 : $request->user_limit,
            'coupon_type' => $request->coupon_type,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
            'max_discount' => $request->max_discount != null ? $request->max_discount : $request->discount,
            'discount' => $request->discount_type == 'amount' ? $request->discount : $request['discount'],
            'discount_type' => $request->discount_type,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Toastr::success(translate('Coupon updated successfully'));
        return back();
    }
    public function delete(Request $request)
    {
        $coupon = Coupon::find($request->id);
        $coupon->delete();
        Toastr::success(translate('Coupon removed'));
        return back();
    }
}
