<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use App\Models\MpesaCredential;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MpesaCredentialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $mpesa= new \Vancha\Mpesa\Mpesa();

        $userid = auth()->guard('admin')->user()->id;
        $mpesaapi = MpesaCredential::whereAdminId($userid)->first();

        $amount = $request->amount;
        $accountid = Carbon::now()->timestamp;
        $phone = $request->phone;
        $userphonenumber = ltrim($phone, '+');

        $BusinessShortCode = $mpesaapi->shortcode;
        $LipaNaMpesaPasskey = $mpesaapi->lipa_na_mpesa_passkey;
        $TransactionType = 'CustomerPayBillOnline';
        $Amount = floor($amount);
        $PartyA = $userphonenumber;
        $PartyB = $mpesaapi->shortcode;
        $PhoneNumber = $userphonenumber;
        $CallBackURL = env('APP_URL').'/api/payments/callbackurl';
        $AccountReference = $accountid;
        $TransactionDesc = 'Product purchase';
        $Remarks = 'Product purchase';

        $stkPushSimulation = $mpesa->STKPushSimulation($BusinessShortCode, $LipaNaMpesaPasskey, $TransactionType, $Amount, $PartyA, $PartyB, $PhoneNumber, $CallBackURL, $AccountReference, $TransactionDesc, $Remarks);
        $result = json_decode($stkPushSimulation, true);
        dd($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MpesaCredential  $mpesaCredential
     * @return \Illuminate\Http\Response
     */
    public function show(MpesaCredential $mpesaCredential)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MpesaCredential  $mpesaCredential
     * @return \Illuminate\Http\Response
     */
    public function edit(MpesaCredential $mpesaCredential)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MpesaCredential  $mpesaCredential
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MpesaCredential $mpesaCredential)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MpesaCredential  $mpesaCredential
     * @return \Illuminate\Http\Response
     */
    public function destroy(MpesaCredential $mpesaCredential)
    {
        //
    }
}