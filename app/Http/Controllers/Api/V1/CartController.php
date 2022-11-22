<?php

namespace App\Http\Controllers\Api\V1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CartController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart($id)
    {
        //Taking the product
        $product = DB::table('products')->where('id', $id)->first();
        $order_details = DB::table('order_details')->where('product_id', $id)->first();
        return response()->json([
            'success' => true, 'message' => "You Product", 'product' => $product, 'order_details' => $order_details
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function removeCart(Request $request, $id)
    {
        DB::table('poss')->where('id', $id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Cart item removed successfully',
        ], 200);
    }
}
