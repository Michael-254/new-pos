<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
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

        $accounts = Account::latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $data = [
            'total' => $accounts->total(),
            'limit' => $limit,
            'offset' => $offset,
            'accounts' => $accounts->items(),
        ];
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accountStore(Request $request, Account $account)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|unique:accounts,account',
            'balance' => 'required',
            'account_number' => 'required|unique:accounts',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        try {
            $account->account = $request->account;
            $account->description = $request->description;
            $account->company_id = auth()->user()->id;
            $account->balance = $request->balance;
            $account->account_number = $request->account_number;
            $account->save();
            return response()->json([
                'success' => true,
                'message' => 'Account saved successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Account not saved',
            ], 403);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function accountUpdate(Request $request)
    {

        $acc = Account::findOrFail($request->id);
        $request->validate([
            'account' => 'required|unique:accounts,account,' . $acc->id,
            'account_number' => 'required|unique:accounts,account_number,' . $acc->id,
        ]);
        $acc->account = $request->account;
        $acc->account_number = $request->account_number;
        $acc->description = $request->description;
        $acc->update();
        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully'
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        try {
            $account = Account::where('account', '!=', 'Receivable')->where('account', '!=', 'Cash')->where('account', '!=', 'Payable')->findOrFail($request->id);
            // dd($account);
            $account->delete();
            return response()->json(
                ['success' => true, 'message' => 'Account deleted successfully'],
                200
            );
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Account not deleted',
            ], 403);
        }
    }
    public function getSearch(Request $request)
    {

        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $search = $request->name;
        if (!empty($search)) {
            $result = Account::where('account', 'like', '%' . $search . '%')->orWhere('account_number', 'like', '%' . $search . '%')->latest()->paginate($limit, ['*'], 'page', $offset);
            $data = [
                'total' => $result->total(),
                'limit' => $limit,
                'offset' => $offset,
                'accounts' => $result->items(),
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'total' => 0,
                'limit' => $limit,
                'offset' => $offset,
                'accounts' => [],
            ];
            return response()->json($data, 200);
        }
    }
}
