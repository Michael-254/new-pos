<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class ExpenseController extends Controller
{
    public function add(Request $request)
    {
        $accounts = Account::orderBy('id','desc')->get();
        $search = $request['search'];
        $from = $request->from;
        $to = $request->to;

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Transaction::where('tran_type','Expense')->
                    where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('description', 'like', "%{$value}%");
                        }
                });
            $query_param = ['search' => $request['search']];
         }else
         {
            $query = Transaction::where('tran_type','Expense')
                                ->when($from!=null, function($q) use ($request){
                                     return $q->whereBetween('date', [$request['from'], $request['to']]);
            });

         }
        $expenses = $query->orderBy('id','desc')->paginate(Helpers::pagination_limit())->appends(['search' => $request['search'],'from'=>$request['from'],'to'=>$request['to']]);
        return view('admin-views.expense.add',compact('accounts','expenses','search','from','to'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
            'description'=> 'required',
            'amount' => 'required|min:1',
        ]);

        $account = Account::find($request->account_id);
        if($account->balance < $request->amount)
        {
            Toastr::warning(\App\CPU\translate('you_do_not_have_sufficent_balance'));
            return back();
        }
        $transaction = new Transaction;
        $transaction->tran_type = 'Expense';
        $transaction->account_id= $request->account_id;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->debit = 1;
        $transaction->credit = 0;
        $transaction->balance = $account->balance - $request->amount;
        $transaction->date = $request->date;
        $transaction->save();


        $account->total_out = $account->total_out + $request->amount;
        $account->balance = $account->balance - $request->amount;
        $account->save();

        Toastr::success(translate('New Expense Added successfully'));
        return back();
    }
}
