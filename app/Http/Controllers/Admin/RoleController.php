<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use App\CPU\Helpers;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin-views.roles.index');
    }
    public function store(Request $request)
    {
        $company_id = auth('admin')->user()->company_id;
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
        ]);

        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('customer/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $split_name = explode(" ", $request->name);
        $dukapaq_member = Role::where(['phone' => $request->mobile])->first();

        if (!$dukapaq_member) {
            $dukapaq_member = Role::Create([
                'phone' => $request->mobile,
                'email' => $request->email ?? '',
                'f_name' => $split_name[0],
                'l_name' => $split_name[1] ? $split_name[1] : '',
                'password' => bcrypt(123456),
                'is_loyalty_enrolled' => $request->is_loyalty_enrolled,
            ]);
        }


        if ($request->is_loyalty_enrolled == 'Yes') {
            $dukapaq_member->update([
                'loyalty_points' => 100,
            ]);
        }

        $customer = new Role;
        $customer->member_id = $dukapaq_member->id;
        $customer->name = $request->name;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        $customer->image = $image_name;
        $customer->balance = $request->balance;
        $customer->company_id = auth('admin')->user()->company_id;
        $customer->save();


        Toastr::success(translate('Role Added successfully'));
        return back();
    }
    public function list(Request $request)
    {
        $accounts = Account::orderBy('id')->get();
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $customers = Role::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('mobile', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $customers = new Role;
        }
      
        $customers = $customers->with('member')->where('company_id', auth('admin')->user()->company_id ?? 1)->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.roles.list', compact('customers', 'accounts', 'search'));
    }

    public function listWithLoyalty(Request $request)
    {
        $accounts = Account::orderBy('id')->get();
        $query_param = [];
        $search = $request['search'];

        $key = explode(' ', $request['search']);

        $customers = Role::whereHas('member', function ($q) {
            $q->where('is_loyalty_enrolled', 'Yes');
        })
            ->when($request->has('search'), function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('mobile', 'like', "%{$value}%");
                }
            });
        $query_param = ['search' => $request['search'] ?? ''];
        //$walk_customer = $customers->where('type',0)->get();
        $customers = $customers->where('company_id', auth('admin')->user()->company_id)
            ->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.roles.list', compact('customers', 'accounts', 'search'));
    }

    public function view(Request $request, $id)
    {
        $customer = Role::where('id', $id)->first();

        if (isset($customer)) {
            $query_param = [];
            $search = $request['search'];
            if ($request->has('search')) {
                $key = explode(' ', $request['search']);
                $orders = Order::where(['user_id' => $id])
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->where('id', 'like', "%{$value}%");
                        }
                    });
                $query_param = ['search' => $request['search']];
            } else {
                $orders = Order::where(['user_id' => $id]);
            }

            $orders = $orders->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
            return view('admin-views.roles.view', compact('customer', 'orders', 'search'));
        }
        Toastr::error('Role not found!');
        return back();
    }
    public function transaction_list(Request $request, $id)
    {
        $accounts = Account::get();
        $customer = Role::where('id', $id)->first();
        if (isset($customer)) {
            $acc_id = $request['account_id'];
            $tran_type = $request['tran_type'];
            $orders = Order::where(['user_id' => $id])->get();
            $transactions = Transaction::where(['customer_id' => $id])
                ->when($acc_id != null, function ($q) use ($request) {
                    return $q->where('account_id', $request['account_id']);
                })
                ->when($tran_type != null, function ($q) use ($request) {
                    return $q->where('tran_type', $request['tran_type']);
                })->latest()->paginate(Helpers::pagination_limit())
                ->appends(['account_id' => $request['account_id'], 'tran_type' => $request['tran_type']]);
            return view('admin-views.roles.transaction-list', compact('customer', 'transactions', 'orders', 'tran_type', 'accounts', 'acc_id'));
        }
        Toastr::error(translate('Role not found'));
        return back();
    }
    public function edit(Request $request)
    {
        $customer = Role::where('id', $request->id)->first();
        return view('admin-views.roles.edit', compact('customer'));
    }
    public function update(Request $request)
    {
        $customer = Role::where('id', $request->id)->first();
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
        ]);

        $dukapaq_member = Role::where(['phone' => $request->mobile])->first();

        if ($request->is_loyalty_enrolled == 'Yes' && $dukapaq_member->is_loyalty_enrolled == 'No') {
            $dukapaq_member->update([
                'is_loyalty_enrolled' => 'Yes',
                'loyalty_points' => 100,
            ]);
        }

        $customer->name = $request->name;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        $customer->image = $request->has('image') ? Helpers::update('customer/', $customer->image, 'png', $request->file('image')) : $customer->image;
        $customer->state = $request->state;
        $customer->city = $request->city;
        $customer->zip_code = $request->zip_code;
        $customer->address = $request->address;
        $customer->balance = $request->balance;
        $customer->save();

        Toastr::success(translate('Role updated successfully'));
        return back();
    }
    public function delete(Request $request)
    {
        $customer = Role::find($request->id);
        Helpers::delete('customer/' . $customer['image']);
        $customer->delete();

        Toastr::success(translate('Role removed successfully'));
        return back();
    }
    public function update_balance(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'amount' => 'required',
            'account_id' => 'required',
            'date' => 'required',
        ]);
        $customer = Role::find($request->customer_id);

        if ($customer->balance >= 0) {
            $account = Account::find(2);
            $transaction = new Transaction;
            $transaction->tran_type = 'Payable';
            $transaction->account_id = $account->id;
            $transaction->amount = $request->amount;
            $transaction->description = $request->description;
            $transaction->debit = 0;
            $transaction->credit = 1;
            $transaction->balance = $account->balance + $request->amount;
            $transaction->date = $request->date;
            $transaction->customer_id = $request->customer_id;
            $transaction->save();

            $account->total_in = $account->total_in + $request->amount;
            $account->balance = $account->balance + $request->amount;
            $account->save();

            $receive_account = Account::find($request->account_id);
            $receive_transaction = new Transaction;
            $receive_transaction->tran_type = 'Income';
            $receive_transaction->account_id = $receive_account->id;
            $receive_transaction->amount = $request->amount;
            $receive_transaction->description = $request->description;
            $receive_transaction->debit = 0;
            $receive_transaction->credit = 1;
            $receive_transaction->balance = $receive_account->balance + $request->amount;
            $receive_transaction->date = $request->date;
            $receive_transaction->customer_id = $request->customer_id;
            $receive_transaction->save();

            $receive_account->total_in = $receive_account->total_in + $request->amount;
            $receive_account->balance = $receive_account->balance + $request->amount;
            $receive_account->save();
        } else {
            $remaining_balance = $customer->balance + $request->amount;

            if ($remaining_balance >= 0) {
                if ($remaining_balance != 0) {
                    $payable_account = Account::find(2);
                    $payable_transaction = new Transaction;
                    $payable_transaction->tran_type = 'Payable';
                    $payable_transaction->account_id = $payable_account->id;
                    $payable_transaction->amount = $remaining_balance;
                    $payable_transaction->description = $request->description;
                    $payable_transaction->debit = 0;
                    $payable_transaction->credit = 1;
                    $payable_transaction->balance = $payable_account->balance + $remaining_balance;
                    $payable_transaction->date = $request->date;
                    $payable_transaction->customer_id = $request->customer_id;
                    $payable_transaction->save();

                    $payable_account->total_in = $payable_account->total_in + $remaining_balance;
                    $payable_account->balance = $payable_account->balance + $remaining_balance;
                    $payable_account->save();
                }

                $receive_account = Account::find($request->account_id);
                $receive_transaction = new Transaction;
                $receive_transaction->tran_type = 'Income';
                $receive_transaction->account_id = $request->account_id;
                $receive_transaction->amount = $request->amount;
                $receive_transaction->description = $request->description;
                $receive_transaction->debit = 0;
                $receive_transaction->credit = 1;
                $receive_transaction->balance = $receive_account->balance + $request->amount;
                $receive_transaction->date = $request->date;
                $receive_transaction->customer_id = $request->customer_id;
                $receive_transaction->save();

                $receive_account->total_in = $receive_account->total_in + $request->amount;
                $receive_account->balance = $receive_account->balance + $request->amount;
                $receive_account->save();


                $receivable_account = Account::find(3);
                $receivable_transaction = new Transaction;
                $receivable_transaction->tran_type = 'Receivable';
                $receivable_transaction->account_id = $receivable_account->id;
                $receivable_transaction->amount = -$customer->balance;
                $receivable_transaction->description = 'update customer balance';
                $receivable_transaction->debit = 1;
                $receivable_transaction->credit = 0;
                $receivable_transaction->balance = $receivable_account->balance + $customer->balance;
                $receivable_transaction->date = $request->date;
                $receivable_transaction->customer_id = $request->customer_id;
                $receivable_transaction->save();

                $receivable_account->total_out = $receivable_account->total_out - $customer->balance;
                $receivable_account->balance = $receivable_account->balance + $customer->balance;
                $receivable_account->save();
            } else {

                $receive_account = Account::find($request->account_id);
                $receive_transaction = new Transaction;
                $receive_transaction->tran_type = 'Income';
                $receive_transaction->account_id = $receive_account->id;
                $receive_transaction->amount = $request->amount;
                $receive_transaction->description = $request->description;
                $receive_transaction->debit = 0;
                $receive_transaction->credit = 1;
                $receive_transaction->balance = $receive_account->balance + $request->amount;
                $receive_transaction->date = $request->date;
                $receive_transaction->customer_id = $request->customer_id;
                $receive_transaction->save();

                $receive_account->total_in = $receive_account->total_in + $request->amount;
                $receive_account->balance = $receive_account->balance + $request->amount;
                $receive_account->save();

                $receivable_account = Account::find(3);
                $receivable_transaction = new Transaction;
                $receivable_transaction->tran_type = 'Receivable';
                $receivable_transaction->account_id = $receivable_account->id;
                $receivable_transaction->amount = $request->amount;
                $receivable_transaction->description = 'update customer balance';
                $receivable_transaction->debit = 1;
                $receivable_transaction->credit = 0;
                $receivable_transaction->balance = $receivable_account->balance - $request->amount;
                $receivable_transaction->date = $request->date;
                $receivable_transaction->customer_id = $request->customer_id;
                $receivable_transaction->save();

                $receivable_account->total_out = $receivable_account->total_out + $request->amount;
                $receivable_account->balance = $receivable_account->balance - $request->amount;
                $receivable_account->save();
            }
        }
        $customer->balance = $customer->balance + $request->amount;
        $customer->save();

        Toastr::success(translate('Role balance updated successfully'));
        return back();
    }
}
