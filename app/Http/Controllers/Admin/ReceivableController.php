<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class ReceivableController extends Controller
{
    public function add(Request $request)
    {
        $accounts = Account::orderBy('id')->get();
        $search = $request['search'];
        $from = $request->from;
        $to = $request->to;
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Transaction::where('tran_type','Receivable')->
                    where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('description', 'like', "%{$value}%");
                        }
                });
            $query_param = ['search' => $request['search']];
        }else
         {
            $query = Transaction::where('tran_type','Receivable')
                                ->when($from!=null, function($q) use ($request){
                                     return $q->whereBetween('date', [$request['from'], $request['to']]);
            });

         }
        $receivables = $query->latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.account-receivable.add',compact('accounts','receivables','search','from','to'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
            'description'=> 'required',
            'amount' => 'required',
            'date' =>'required',
        ]);

        $account = Account::find($request->account_id);
        $transaction = new Transaction;
        $transaction->tran_type = 'Receivable';
        $transaction->account_id = $request->account_id;
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

        Toastr::success(translate('Receivable Balance Added successfully'));
        return back();
    }
    public function transfer(Request $request)
    {
        $receivable_account = Account::find($request->account_id);
        $receivable_transaction = Transaction::find($request->transaction_id);
        $balance = $receivable_transaction->amount - $request->amount;
        if($balance < 0){
            Toastr::warning(translate('You have not sufficient balance for this transaction'));
            return back();
        }
        $receivable_transaction->amount = $balance;
        $receivable_transaction->balance = $receivable_transaction->balance - $request->amount;
        $receivable_transaction->save();

        $receivable_account->total_out = $receivable_account->total_out + $request->amount;
        $receivable_account->balance = $receivable_account->balance - $request->amount;
        $receivable_account->save();

        $receive_account = Account::find($request->receive_account_id);
        $transaction = new Transaction;
        $transaction->tran_type = 'Income';
        $transaction->account_id = $request->receive_account_id;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->debit = 0;
        $transaction->credit = 1;
        $transaction->balance =  $receive_account->balance + $request->amount;
        $transaction->date = $request->date;
        $transaction->save();

        $receive_account->total_in = $receive_account->total_in + $request->amount;
        $receive_account->balance = $receive_account->balance + $request->amount;
        $receive_account->save();

        Toastr::success(translate('Payable Balance pay successfully'));
        return back();

    }
}
