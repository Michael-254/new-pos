<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\CPU\Helpers;
use App\Models\Order;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $coupons = Coupon::latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $coupons->total(),
            'limit' => $limit,
            'offset' => $offset,
            'coupons' => $coupons->items(),
        ];
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function poststore(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'coupon_type' => 'required',
            'code' => 'required|unique:coupons',
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        try {
            $coupon->title = $request->title;
            $coupon->code = $request->code;
            $coupon->user_limit = $request->coupon_type != 'default' ? 1 : $request->user_limit;
            $coupon->coupon_type = $request->coupon_type;
            $coupon->start_date = $request->start_date;
            $coupon->expire_date = $request->expire_date;
            $coupon->min_purchase = $request->min_purchase != null ? $request->min_purchase : 0;
            $coupon->max_discount = $request->max_discount != null ? $request->max_discount : $request->discount;
            $coupon->discount = $request->discount_type == 'amount' ? $request->discount : $request['discount'];
            $coupon->discount_type = $request->discount_type;
            $coupon->status = 1;
            $coupon->save();
            return response()->json([
                'success' => true,
                'message' => 'Coupon saved successfully',
            ], 200);
        } catch (\Throwable $th) {
            //dd($th);
            return response()->json([
                'success' => false,
                'message' => 'Coupon not saved',
            ], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function postUpdate(Request $request)
    {
        $coupon = Coupon::findOrFail($request->id);
        $request->validate([
            'id' => 'required',
            'title' => 'required',
            'coupon_type' => 'required',
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'start_date' => 'required',
            'expire_date' => 'required',
            'discount' => 'required'
        ]);
        $coupon->title = $request->title;
        $coupon->code = $request->code;
        $coupon->user_limit = $request->coupon_type != 'default' ? 1 : $request->user_limit;
        $coupon->coupon_type = $request->coupon_type;
        $coupon->start_date = $request->start_date;
        $coupon->expire_date = $request->expire_date;
        $coupon->min_purchase = $request->min_purchase != null ? $request->min_purchase : 0;
        $coupon->max_discount = $request->max_discount != null ? $request->max_discount : $request->discount;
        $coupon->discount = $request->discount_type == 'amount' ? $request->discount : $request['discount'];
        $coupon->discount_type = $request->discount_type;
        $coupon->status = 1;
        $coupon->save();
        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully',
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        try {
            $coupon = Coupon::findOrFail($request->id);
            if (!is_null($coupon)) {
                $coupon->delete();
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Coupon deleted successfully',
                    ],
                    200
                );
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not deleted',
            ], 403);
        }
    }
    public function updateStatus(Request $request)
    {
        $coupon = Coupon::find($request->id);
        $coupon->status = !$coupon['status'];
        $coupon->save();
        return response()->json(['success' => 'Status updated successfully.'], 200);
    }
    public function checkCoupon(Request $request)
    {
        $coupon = Coupon::where(['code' => $request->code])
            ->where('expire_date', '>=', Carbon::now())
            ->where('start_date', '<=', Carbon::now())
            ->where('status', '!=', 0)
            ->first();
        if (empty($coupon)) {
            return response(['message' => 'Sorry, that coupon is not valid.'], 202);
        }
        $order_coupon_count = Order::with('coupon')->where('coupon_code', $coupon->code)->where('user_id', $request->user_id)->count();
        if ($order_coupon_count >= $coupon->user_limit) {
            return response(['message' => 'Opps Coupon availed by you!'], 202);
        } elseif ($request->order_amount < $coupon->min_purchase) {
            return response(['message' => 'Does not satisfy minmun purchase amount!'], 202);
        } else {
            $data =  ['coupon' => $coupon];
            return response()->json($data, 200);
        }
    }

    public function getSearch(Request $request)
    {

        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $search = $request->name;
        if (!empty($search)) {
            $result = Coupon::where('title', 'like', '%' . $search . '%')->orWhere('code', 'like', '%' . $search . '%')->latest()->paginate($limit, ['*'], 'page', $offset);
            $data = [
                'total' => $result->total(),
                'limit' => $limit,
                'offset' => $offset,
                'coupons' => $result->items(),
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'total' => 0,
                'limit' => $limit,
                'offset' => $offset,
                'coupons' => [],
            ];
            return response()->json($data, 200);
        }
    }
}
