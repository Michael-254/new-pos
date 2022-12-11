<?php

namespace App\Http\Controllers\Api\V1;

use App\CPU\Helpers;
use App\Models\Account;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    //Get Category
    public function getIndex(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $suppliers = Supplier::withCount('products')
            ->where('company_id', auth()->user()->company_id)
            ->latest()->paginate($limit, ['*'], 'page', $offset);
        $data =  [
            'total' => $suppliers->total(),
            'limit' => $limit,
            'offset' => $offset,
            'suppliers' => $suppliers->items()
        ];
        return response()->json($data, 200);
    }
    //Save Category
    public function postStore(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        //Image Upload
        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('supplier/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }
        try {
            $supplier->name = $request->name;
            $supplier->mobile = $request->mobile;
            $supplier->company_id = auth()->user()->company_id;
            $supplier->email = $request->email;
            $supplier->image = $image_name;
            $supplier->due_amount = $request->due_amount;
            $supplier->company_id = auth()->user()->company_id;
            $supplier->save();
            return response()->json([
                'success' => true,
                'message' => 'Supplier saved successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not saved'
            ], 403);
        }
    }

    public function getDetails(Request $request)
    {
        $supplier = Supplier::findOrFail($request->id);
        return response()->json([
            'success' => true,
            'message' => 'Supplier details',
            'supplier' => $supplier
        ], 200);
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
        try {
            $supplier = Supplier::findOrFail($request->id);
            $request->validate([
                'name' => 'required',
                'mobile' => 'required',
                'email' => 'required',
            ]);
            $supplier->name = $request->name;
            $supplier->mobile = $request->mobile;
            $supplier->email = $request->email;
            $supplier->image = $request->has('image') ? Helpers::update('supplier/', $supplier->image, 'png', $request->file('image')) : $supplier->image;
            $supplier->save();
            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully',
            ], 200);
        } catch (\Throwable $th) {
            //dd($th);
            return response()->json([
                'success' => true,
                'message' => 'Supplier not updated',
            ], 403);
        }
    }
    //Delete Category
    public function delete(Request $request)
    {
        try {
            $supplier = Supplier::findOrFail($request->id);
            Helpers::delete('supplier/' . $supplier['image']);
            $supplier->delete();
            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier not deleted'
            ], 403);
        }
    }

    public function getSearch(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        $search = $request->name;
        // if (!empty($search)) {
        $result = Supplier::where('name', 'like', '%' . $search . '%')->orWhere('mobile', 'like', '%' . $search . '%')->latest()->paginate($limit, ['*'], 'page', $offset);
        $data = [
            'total' => $result->total(),
            'limit' => $limit,
            'offset' => $offset,
            'suppliers' => $result->items(),
        ];
        return response()->json($data, 200);
        // }
    }

    public function filterByCity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;
        if (!empty($request->city)) {
            $result = Supplier::where('city', $request->city)->latest()->paginate($limit, ['*'], 'page', $offset);
            $data = [
                'total' => $result->total(),
                'limit' => $limit,
                'offset' => $offset,
                'supplier' => $result->items(),
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'total' => 0,
                'limit' => $limit,
                'offset' => $offset,
                'supplier' => [],
            ];
            return response()->json($data, 200);
        }
    }
    public function transactions(Request $request)
    {
        $transactions = Transaction::with('account')->where('supplier_id', $request->supplier_id)->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $data = [
            'total' => $transactions->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'transfers' => $transactions->items()
        ];
        return response()->json($data, 200);
    }

    public function transactionsDateFilter(Request $request)
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
            })->where('supplier_id', '=', $request->supplier_id)->latest()->paginate($limit, ['*'], 'page', $offset);
            $data = [
                'total' => $result->total(),
                'limit' => $limit,
                'offset' => $offset,
                'transfers' => $result->items(),
            ];
            return response()->json($data, 200);
        }
    }
    public function payment(Request $request)
    { {
            $request->validate([
                'supplier_id' => 'required',
                'total_due_amount' => 'required',
                'pay_amount' => 'required',
                'remaining_due_amount' => 'required',
                'payment_account_id' => 'required',
            ]);

            $payment_account = Account::find($request->payment_account_id);
            if ($payment_account->balance < $request->pay_amount) {
                $data = [
                    'success' => true,
                    'message' => 'You do not have sufficent balance!'
                ];
                return response()->json($data);
            }

            if ($request->pay_amount > 0) {
                $payment_transaction = new Transaction();
                $payment_transaction->tran_type = 'Expense';
                $payment_transaction->account_id = $payment_account->id;
                $payment_transaction->amount = $request->pay_amount;
                $payment_transaction->description = 'Supplier due payment';
                $payment_transaction->debit = 1;
                $payment_transaction->credit = 0;
                $payment_transaction->balance = $payment_account->balance - $request->pay_amount;
                $payment_transaction->date = date("Y/m/d");
                $payment_transaction->company_id = auth()->user()->company_id;
                $payment_transaction->supplier_id = $request->supplier_id;
                $payment_transaction->save();

                $payment_account->total_out = $payment_account->total_out + $request->pay_amount;
                $payment_account->balance = $payment_account->balance - $request->pay_amount;
                $payment_account->save();

                $payable_account = Account::find(2);
                $payable_transaction = new Transaction();
                $payable_transaction->tran_type = 'Payable';
                $payable_transaction->account_id = $payable_account->id;
                $payable_transaction->amount = $request->pay_amount;
                $payable_transaction->description = 'Supplier due payment';
                $payable_transaction->debit = 1;
                $payable_transaction->credit = 0;
                $payable_transaction->balance = $payable_account->balance - $request->pay_amount;
                $payable_transaction->date = date("Y/m/d");
                $payable_transaction->company_id = auth()->user()->company_id;
                $payable_transaction->supplier_id = $request->supplier_id;
                $payable_transaction->save();

                $payable_account->total_out = $payable_account->total_out + $request->pay_amount;
                $payable_account->balance = $payable_account->balance - $request->pay_amount;
                $payable_account->save();
            }

            $supplier = Supplier::find($request->supplier_id);
            $supplier->due_amount = $supplier->due_amount - $request->pay_amount;
            $supplier->save();

            $data = [
                'success' => true,
                'message' => 'Supplier payment successfully'
            ];
            return response()->json($data);
        }
    }

    public function newPurchase(Request $request)
    {

        $request->validate([
            'supplier_id' => 'required',
            'purchased_amount' => 'required',
            'paid_amount' => 'required',
            'due_amount' => 'required',
            'payment_account_id' => 'required',
        ]);

        $payment_account = Account::find($request->payment_account_id);

        if ($payment_account->balance < $request->paid_amount) {
            $data = [
                'success' => true,
                'message' => 'You do not have sufficent balance!'
            ];
            return response()->json($data);
        }
        if ($request->paid_amount > 0) {
            $payment_transaction = new Transaction();
            $payment_transaction->tran_type = 'Expense';
            $payment_transaction->account_id = $payment_account->id;
            $payment_transaction->amount = $request->paid_amount;
            $payment_transaction->description = 'Supplier payment';
            $payment_transaction->debit = 1;
            $payment_transaction->credit = 0;
            $payment_transaction->balance = $payment_account->balance - $request->paid_amount;
            $payment_transaction->date = date("Y/m/d");
            $payment_transaction->company_id = auth()->user()->company_id;
            $payment_transaction->supplier_id = $request->supplier_id;
            $payment_transaction->save();

            $payment_account->total_out = $payment_account->total_out + $request->paid_amount;
            $payment_account->balance = $payment_account->balance - $request->paid_amount;
            $payment_account->save();
        }

        if ($request->due_amount > 0) {
            $payable_account = Account::find(2);
            $payable_transaction = new Transaction();
            $payable_transaction->tran_type = 'Payable';
            $payable_transaction->account_id = $payable_account->id;
            $payable_transaction->amount = $request->due_amount;
            $payable_transaction->description = 'Supplier payment';
            $payable_transaction->debit = 0;
            $payable_transaction->credit = 1;
            $payable_transaction->balance = $payable_account->balance + $request->due_amount;
            $payable_transaction->date = date("Y/m/d");
            $payable_transaction->company_id = auth()->user()->company_id;
            $payable_transaction->supplier_id = $request->supplier_id;
            $payable_transaction->save();

            $payable_account->total_in = $payable_account->total_in + $request->due_amount;
            $payable_account->balance = $payable_account->balance + $request->due_amount;
            $payable_account->save();

            $supplier = Supplier::find($request->supplier_id);
            $supplier->due_amount = $supplier->due_amount + $request->due_amount;
            $supplier->save();
        }
        $data = [
            'success' => true,
            'message' => 'Supplier new purchase added successfully'
        ];
        return response()->json($data);
    }
}
