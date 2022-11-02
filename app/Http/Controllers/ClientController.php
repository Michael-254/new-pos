<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct() 
    {
     $this->middleware('auth');
    }

    public function index(){
        $customer = Customer::findOrFail(5);

        $customer->load('orderDetails.product');

        return view('admin-views.client',compact('customer'));
    }
}
