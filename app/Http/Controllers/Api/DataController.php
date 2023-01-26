<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index()
    {
        $orders = Order::withoutGlobalScopes()->with('details', 'customer', 'account')->latest()->get();

        return response()->json([
            'success' => true,
            'all_orders' => $orders,
        ], 200);
    }
}
