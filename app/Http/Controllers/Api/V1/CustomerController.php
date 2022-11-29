<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Order;
use App\Models\Account;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    //Get Category
    public function getIndex(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $customers = Customer::withCount('orders')->where('company_id', auth('admin')->user()->company_id)->orderBy('id', 'asc')->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $customers->total(),
            'limit' => $limit,
            'offset' => $offset,
            'customers' => $customers->items(),
        ];
        return response()->json($data, 200);
    }
    //Save Category
    public function postStore(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'name' => 'required',
                'mobile' => 'required|unique:customers',
            ]);
            if (!empty($request->file('image'))) {
                $image_name = Helpers::upload('customer/', 'png', $request->file('image'));
            } else {
                $image_name = 'def.png';
            }

            $dukapaq_member = CustomerLogin::where('phone', $request->mobile)->first();

            if ($dukapaq_member == '') {
                $dukapaq_member = CustomerLogin::create([
                    'f_name' => $request->name,
                    'mobile' => $request->mobile,
                    'password' => bcrypt(123456),
                ]);

                if ($request->is_loyalty_enrolled == 'Yes') {
                    $dukapaq_member->update([
                        'loyalty_points' => 100,
                        'is_loyalty_enrolled' => $request->is_loyalty_enrolled,
                    ]);
                }
            }

            $customer = new Customer;
            $customer->member_id = $dukapaq_member->id;
            $customer->name = $request->name;
            $customer->mobile = $request->mobile;
            $customer->email = $request->email;
            $customer->image = $image_name;
            $customer->state = $request->state;
            $customer->city = $request->city;
            $customer->zip_code = $request->zip_code;
            $customer->address = $request->address;
            $customer->balance = $request->balance;
            $customer->company_id = auth('admin')->user()->company_id;
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Customer saved successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not saved',
            ], 403);
        }
    }

    public function getDetails(Request $request)
    {
        try {
            $customerDetails = Customer::findOrFail($request->id);
            return response()->json([
                'message' => 'Customer details',
                'data' => $customerDetails,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Invalid id: customer not found',
            ], 422);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function postUpdate(Request $request)
    {

        //dd($request->all());
        $customer = Customer::where('id', $request->id)->first();
        $request->validate([
            'name' => 'required',
            'mobile' => 'required|unique:customers,mobile,' . $customer->id,
        ]);
        $customer->name = $request->name;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        $customer->image = $request->has('image') ? Helpers::update('customer/', $customer->image, 'png', $request->file('image')) : $customer->image;
        $customer->state = $request->state;
        $customer->city = $request->city;
        $customer->zip_code = $request->zip_code;
        $customer->address = $request->address;
        $customer->balance = $request->balance;
        $customer->update();
        return response()->json([
            'message' => 'Customer updated successfully',
        ], 200);
    }
    //Delete Category
    public function delete(Request $request)
    {
        try {
            $customer = Customer::where('id', '!=', '0')->find($request->id);
            Helpers::delete('customer/' . $customer['image']);
            $customer->delete();
            return response()->json([
                'message' => 'Customer deleted',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Customer not deleted',
            ], 403);
        }
    }

    public function getSearch(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $search = $request->name;
        // if (!empty($search)) {
        $result = Customer::where('name', 'like', '%' . $search . '%')->orWhere('mobile', 'like', '%' . $search . '%')->where('company_id', auth('admin')->user()->company_id)->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $result->total(),
            'limit' => $limit,
            'offset' => $offset,
            'customers' => $result->items(),
        ];
        return response()->json($data, 200);
        //}
        // else {
        //     $data = [
        //         'total' => 0,
        //         'limit' => $limit,
        //         'offset' => $offset,
        //         'customers' => [],
        //     ];
        //     return response()->json($data, 200);
        // }
    }

    public function dateWiseFilter(Request $request)
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
            $result = Customer::when(($request->from && $request->to), function ($query) use ($request) {
                $query->whereBetween('date', [$request->from . ' 00:00:00', $request->to . ' 23:59:59']);
            })->where('tran_type', '=', 'Expense')->latest()->paginate($limit, ['*'], 'page', $offset);
            $data = [
                'total' => $result->total(),
                'limit' => $limit,
                'offset' => $offset,
                'customer' => $result->items(),
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'total' => 0,
                'limit' => $limit,
                'offset' => $offset,
                'customer' => [],
            ];
            return response()->json($data, 200);
        }
    }


    public function totalTransaction(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $result = Transaction::where('customer_id', $request->customer_id)->with('account')->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $result->total(),
            'limit' => $limit,
            'offset' => $offset,
            'transfers' => $result->items(),
        ];
        return response()->json($data, 200);
    }

    public function transactionFilter(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        if ($request->account_id) {
            $transactions = Transaction::where('account_id', $request->account_id)->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        } elseif ($request->transaction_type) {
            $transactions = Transaction::where('tran_type', $request->transaction_type)->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        } elseif ($request->from && $request->to) {
            $transactions = Transaction::whereBetween('date', [$request->from . ' 00:00:00', $request->to . ' 23:59:59'])->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        } else {
            $transactions = Transaction::latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        }
        $data = [
            'total' => $transactions->total(),
            'limit' => $limit,
            'offset' => $offset,
            'transfers' => $transactions->items()
        ];
        return response()->json($data, 200);
    }
    public function update_balance(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'amount' => 'required',
            'account_id' => 'required',
            'date' => 'required',
        ]);
        $customer = Customer::find($request->customer_id);

        if ($customer->balance >= 0) {
            $account = Account::find(2);
            $transaction = new Transaction();
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
            $receive_transaction = new Transaction();
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
                    $payable_transaction = new Transaction();
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
                $receive_transaction = new Transaction();
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
                $receivable_transaction = new Transaction();
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
                $receive_transaction = new Transaction();
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
                $receivable_transaction = new Transaction();
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
        return response()->json([
            'message' => 'Customer balance updated successfully',
        ], 200);
    }
}
