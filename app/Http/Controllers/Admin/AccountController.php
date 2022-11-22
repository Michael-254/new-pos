<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Account;
use function App\CPU\translate;

class AccountController extends Controller
{
    public function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Account::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('account', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $query = new Account;
        }

        $accounts = $query->orderBy('id','desc')->paginate(Helpers::pagination_limit());
        return view('admin-views.account.list', compact('accounts','search'));
    }

    public function add()
    {
        return view('admin-views.account.add');
    }
    public function store(Request $request)
    {
        $request->validate([
            'account' => 'required|unique:accounts,account',
            'balance'=> 'required',
            'account_number' => 'required|unique:accounts',
        ]);

        $account = new Account();
        $account->account = $request->account;
        $account->description = $request->description;
        $account->balance = $request->balance;
        $account->account_number = $request->account_number;
        $account->save();

        Toastr::success(translate('New Account Added successfully'));
        //return back();
        return redirect()->route('admin.account.list');
    }
    public function edit($id)
    {
        $account = Account::find($id);
        return view('admin-views.account.edit', compact('account'));
    }
    public function update(Request $request, $id)
    {
        $account = Account::find($id);
        $request->validate([
            'account' => 'required|unique:accounts,account,'.$account->id,
            'account_number' => 'required|unique:accounts,account_number,'.$account->id,
        ]);

        $account->account = $request->account;
        $account->account_number = $request->account_number;
        $account->description = $request->description;
        $account->save();
        Toastr::success(translate('Account updated successfully'));
        return back();
    }
    public function delete($id)
    {
        $account = Account::find($id);
        $account->delete();

        Toastr::success(translate('Account deleted successfully'));
        return back();
    }

}
