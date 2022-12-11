<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Income;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\CPU\Helpers;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $incomes = Transaction::with('account')->where('tran_type', '=', 'Income')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $incomes->total(),
            'limit' => $limit,
            'offset' => $offset,
            'incomes' => $incomes->items(),
        ];
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFilter(Request $request)
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
            $result = Transaction::with('account')->when(($request->from && $request->to), function ($query) use ($request) {
                $query->whereBetween('date', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
            })->where('tran_type', '=', 'Income')->latest()->paginate($limit, ['*'], 'page', $offset);
            $data = [
                'total' => $result->total(),
                'limit' => $limit,
                'offset' => $offset,
                'incomes' => $result->items(),
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'total' => 0,
                'limit' => $limit,
                'offset' => $offset,
                'transfers' => [],
            ];
            return response()->json($data, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function show(Income $income)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Income $income)
    {
        //
    }

    public function newIncome(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
            'description' => 'required',
            'amount' => 'required|min:1',
            'date' => 'required',
        ]);

        $account = Account::find($request->account_id);
        $transaction = new Transaction();
        $transaction->tran_type = 'Income';
        $transaction->account_id = $request->account_id;
        $transaction->company_id = auth()->user()->company_id;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->debit = 0;
        $transaction->credit = 1;
        $transaction->balance =  $account->balance + $request->amount;
        $transaction->date = $request->date;
        $transaction->save();

        $account->total_in = $account->total_in + $request->amount;
        $account->balance = $account->balance + $request->amount;
        $account->save();
        return response()->json(
            ['success' => true, 'message' => 'New Income Added successfully'],
            200
        );
    }
}
