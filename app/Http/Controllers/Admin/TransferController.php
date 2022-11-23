<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;

class TransferController extends Controller
{
    public function add(Request $request)
    {
        $accounts = Account::orderBy('id','desc')->get();
        $search = $request['search'];
        $from = $request->from;
        $to = $request->to;
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Transaction::where('tran_type','Transfer')->
                    where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('description', 'like', "%{$value}%");
                        }
                });
            $query_param = ['search' => $request['search']];
        }else
         {
            $query = Transaction::where('tran_type','Transfer')
                                ->when($from!=null, function($q) use ($request){
                                     return $q->whereBetween('date', [$request['from'], $request['to']]);
            });

         }
        $transfers = $query->latest()->paginate(Helpers::pagination_limit())->appends(['search' => $request['search'],'from'=>$request['from'],'to'=>$request['to']]);
        return view('admin-views.transfer.add',compact('accounts','transfers','search','from','to'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'account_from_id' => 'required',
            'account_to_id' => 'required',
            'description'=> 'required',
            'amount' => 'required|min:1',
        ]);

        $acc_from = Account::find($request->account_from_id);
        if($acc_from->balance < $request->amount)
        {
             Toastr::warning(\App\CPU\translate('you_have_not_sufficient_balance'));
            return back();
        }
        $transaction = new Transaction;
        $transaction->tran_type = 'Transfer';
        $transaction->account_id = $request->account_from_id;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->debit = 1;
        $transaction->credit = 0;
        $transaction->balance =  $acc_from->balance - $request->amount;
        $transaction->date = $request->date;
        $transaction->save();


        $acc_from->total_out = $acc_from->total_out + $request->amount;
        $acc_from->balance = $acc_from->balance - $request->amount;
        $acc_from->save();

        $acc_to = Account::find($request->account_to_id);
        $transaction = new Transaction;
        $transaction->tran_type = 'Transfer';
        $transaction->account_id = $request->account_to_id;
        $transaction->amount = $request->amount;
        $transaction->description = $request->description;
        $transaction->debit = 0;
        $transaction->credit = 1;
        $transaction->balance =  $acc_to->balance + $request->amount;
        $transaction->date = $request->date;
        $transaction->save();
        
        $acc_to->total_in = $acc_to->total_in + $request->amount;
        $acc_to->balance = $acc_to->balance + $request->amount;
        $acc_to->save();
        Toastr::success(translate('New Deposit Added successfully'));
        return back();
    }
}