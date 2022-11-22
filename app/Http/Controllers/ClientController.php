<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    public function index(){
        $customer = Customer::findOrFail(5);

        $orders = $customer->orderDetails()->paginate(10);


        return view('admin-views.client',compact('customer','orders'));
    }
}