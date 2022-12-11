<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class PayableController extends Controller
{
    public function add(Request $request)
    {
        $accounts = Account::orderBy('id')->get();
        $search = $request['search'];
        $from = $request->from;
        $to = $request->to;
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Transaction::where('tran_type', 'Payable')->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('description', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $query = Transaction::where('tran_type', 'Payable')
                ->when($from != null, function ($q) use ($request) {
                    return $q->whereBetween('date', [$request['from'], $request['to']]);
                });
        }
        $payables = $query->latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.account-payable.add', compact('accounts', 'payables', 'search', 'from', 'to'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
            'description' => 'required',
            'amount' => 'required',
        ]);

        $account = Account::find($request->account_id);
        $transaction = new Transaction;
        $transaction->tran_type = 'Payable';
        $transaction->account_id = $request->account_id;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->debit = 1;
        $transaction->credit = 0;
        $transaction->balance =  $account->balance + $request->amount;
        $transaction->date = $request->date;
        $transaction->save();

        $account->total_in = $account->total_in + $request->amount;
        $account->balance = $account->balance + $request->amount;
        $account->save();

        Toastr::success(translate('Payable Balance Added successfully'));
        return back();
    }
    public function transfer(Request $request)
    {
        $payment_account = Account::find($request->payment_account_id);
        $remain_balance = $payment_account->balance - $request->amount;
        if ($remain_balance < 0) {
            Toastr::warning(translate('Your payment account has not sufficent balance for this transaction'));
            return back();
        }
        $payable_account = Account::find($request->account_id);
        $payable_transaction = Transaction::find($request->transaction_id);
        $balance = $payable_transaction->amount - $request->amount;
        if ($balance < 0) {
            Toastr::warning(translate('You have not sufficient balance for this transaction'));
            return back();
        }
        $payable_transaction->amount = $balance;
        $payable_transaction->balance = $payable_transaction->balance - $request->amount;
        $payable_transaction->save();

        $payable_account->total_out = $payable_account->total_out + $request->amount;
        $payable_account->balance = $payable_account->balance - $request->amount;
        $payable_account->save();

        $transaction = new Transaction;
        $transaction->tran_type = 'Expense';
        $transaction->account_id = $request->payment_account_id;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->debit = 1;
        $transaction->credit = 0;
        $transaction->balance =  $payment_account->balance - $request->amount;
        $transaction->date = $request->date;
        $transaction->save();

        $payment_account->total_out = $payment_account->total_out + $request->amount;
        $payment_account->balance = $payment_account->balance - $request->amount;
        $payment_account->save();

        Toastr::success(translate('Payable Balance pay successfully'));
        return back();
    }
}
