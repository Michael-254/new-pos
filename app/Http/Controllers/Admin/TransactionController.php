<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\CPU\Helpers;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function list(Request $request)
    {
        $accounts = Account::orderBy('id','desc')->get();
        $acc_id = $request['account_id'];
        $tran_type = $request['tran_type'];
        $from = $request['from'];
        $to = $request['to'];

        $query = Transaction::when($acc_id!=null, function($q) use ($request){
                                    return $q->where('account_id',$request['account_id']);
                                })
                                ->when($tran_type!=null, function($q) use ($request){
                                    return $q->where('tran_type',$request['tran_type']);
                                })
                                ->when($from!=null, function($q) use ($request){
                                    return $q->whereBetween('date', [$request['from'], $request['to']]);
                                });

        $transactions = $query->orderBy('id','desc')->paginate(Helpers::pagination_limit())->appends(['account_id' => $request['account_id'],'tran_type'=>$request['tran_type'],'from'=>$request['from'],'to'=>$request['to']]);
        return view('admin-views.transaction.list',compact('accounts','transactions','acc_id','tran_type','from','to'));
    }
    public function export(Request $request)
    {
        //return $request;
        $acc_id = $request['account_id'];
        $tran_type = $request['tran_type'];
        $from = $request['from'];
        $to = $request['to'];
        if($acc_id==null && $tran_type==null && $to==null && $from !=null)
        {
            $transactions = Transaction::whereMonth('date',Carbon::now()->month)->get();

        }else{
            $transactions = Transaction::when($acc_id!=null, function($q) use ($request){
                                    return $q->where('account_id',$request['account_id']);
                                })
                                ->when($tran_type!=null, function($q) use ($request){
                                    return $q->where('tran_type',$request['tran_type']);
                                })
                                ->when($from!=null, function($q) use ($request){
                                    return $q->whereBetween('date', [$request['from'], $request['to']]);
                                })->get();
        }

        $storage = [];
        foreach($transactions as $transaction)
        {
            array_push($storage,[
                'transaction_type'=> $transaction->tran_type,
                'account' => $transaction->account->account,
                'amount'=> $transaction->amount,
                'description'=> $transaction->description,
                'debit'=> $transaction->debit==1?$transaction->amount:0,
                'credit'=>$transaction->credit==1?$transaction->amount:0,
                'balance'=>$transaction->balance,
                'date'=>$transaction->date,
            ]);
        }
        return (new FastExcel($storage))->download('transaction_history.xlsx');
    }
}