<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function getExpense(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $expenses = Transaction::with('account')->where('tran_type', '=', 'Expense')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $expenses->total(),
            'limit' => $limit,
            'offset' => $offset,
            'expenses' => $expenses->items(),
        ];
        return response()->json($data, 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeExpenses(Request $request, Transaction $expense)
    {
        $request->validate([
            'account_id' => 'required',
            'description' => 'required',
            'amount' => 'required|min:1',
        ]);
        try {
            $account = Account::find($request->account_id);
            if ($account->balance < $request->amount) {
                return response()->json(['success' => false, 'message' => 'You do not have sufficent balance'], 400);
            }
            $expense->tran_type = "Expense";
            $expense->account_id = $request->account_id;
            $expense->amount = $request->amount;
            $expense->description = $request->description;
            $expense->debit = 1;
            $expense->credit = 0;
            $expense->date = $request->date;
            $expense->save();
            //Now reduce amount form account after saving expense
            $account->total_out = $account->total_out + $request->amount;
            $account->balance = $account->balance - $request->amount;
            $account->save();
            return response()->json([
                'success' => true,
                'message' => 'Expenses saved successfully',
            ], 200);
        } catch (\Throwable $th) {
            //dd($th);
            return response()->json([
                'success' => false,
                'message' => 'Expenses not saved',
            ], 403);
        }
    }
    public function storeTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required',
            'description' => 'required',
            'amount' => 'required|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $accTransfaree = Account::findOrFail($request->account_id);
        if ($accTransfaree->balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance',
            ], 400);
        } elseif ($request->amount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'You are not able to transfer this amount',
            ], 400);
        }
        try {
            $transfer = new Transaction();
            $transfer->tran_type = 'Transfer';
            $transfer->account_id = $request->account_id;
            $transfer->amount = $request->amount;
            $transfer->description = $request->description;
            $transfer->debit = 1;
            $transfer->credit = 0;
            $transfer->balance = $accTransfaree->balance - $request->amount;
            $transfer->date = $request->date;
            $transfer->save();

            $accTransfaree->total_out = $accTransfaree->total_out + $request->amount;
            $accTransfaree->balance = $accTransfaree->balance - $request->amount;
            $accTransfaree->save();
            return response()->json([
                'success' => false,
                'message' => 'Transfer successfull',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer unsuccessfull',
            ], 403);
        }
    }

    public function getSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        if (!empty($request->from && $request->to)) {
            $result = Transaction::when(($request->from && $request->to), function ($query) use ($request) {
                $query->whereBetween('date', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
            })->where('tran_type', '=', 'Expense')->latest()->paginate($limit, ['*'], 'page', $offset);
            $data = [
                'total' => $result->total(),
                'limit' => $limit,
                'offset' => $offset,
                'expenses' => $result->items(),
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'total' => 0,
                'limit' => $limit,
                'offset' => $offset,
                'expenses' => [],
            ];
            return response()->json($data, 200);
        }
    }

    public function transferList(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $transfers = Transaction::with('account')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $transfers->total(),
            'limit' => $limit,
            'offset' => $offset,
            'transfers' => $transfers->items(),
        ];
        return response()->json($data, 200);
    }
}