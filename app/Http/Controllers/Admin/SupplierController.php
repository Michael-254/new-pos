<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Product;
use Brian2694\Toastr\Facades\Toastr;
use App\CPU\Helpers;
use App\Models\Account;
use App\Models\Transaction;
use function App\CPU\translate;

class SupplierController extends Controller
{
    public function index()
    {
        return view('admin-views.supplier.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
        ]);

        if (!empty($request->file('image'))) {
            $image_name =  Helpers::upload('supplier/', 'png', $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $supplier = new Supplier;
        $supplier->name = $request->name;
        $supplier->mobile = $request->mobile;
        $supplier->email = $request->email;
        $supplier->image = $image_name;
        $supplier->company_id = auth('admin')->user()->company_id;

        $supplier->save();

        Toastr::success(translate('Supplier Added successfully'));
        return redirect()->route('admin.supplier.list');
    }
    public function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $suppliers = Supplier::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('mobile', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $suppliers = new Supplier;
        }
        $suppliers = $suppliers->where('company_id', auth('admin')->user()->company_id)
            ->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.supplier.list', compact('suppliers', 'search'));
    }
    public function view(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        return view('admin-views.supplier.view', compact('supplier'));
    }
    public function product_list(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $query_param = [];
        $search = $request['search'];
        $sort_oqrderQty = $request['sort_oqrderQty'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Product::where('supplier_id', $id)->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('product_code', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $query = Product::where('supplier_id', $id)
                ->when($request->sort_oqrderQty == 'quantity_asc', function ($q) use ($request) {
                    return $q->orderBy('quantity', 'asc');
                })
                ->when($request->sort_oqrderQty == 'quantity_desc', function ($q) use ($request) {
                    return $q->orderBy('quantity', 'desc');
                })
                ->when($request->sort_oqrderQty == 'order_asc', function ($q) use ($request) {
                    return $q->orderBy('order_count', 'asc');
                })
                ->when($request->sort_oqrderQty == 'order_desc', function ($q) use ($request) {
                    return $q->orderBy('order_count', 'desc');
                })
                ->when($request->sort_oqrderQty == 'default', function ($q) use ($request) {
                    return $q->orderBy('id');
                });
        }

        $products = $query->latest()->paginate(Helpers::pagination_limit())->appends(['search' => $search, 'sort_oqrderQty' => $request->sort_oqrderQty]);
        return view('admin-views.supplier.product-list', compact('supplier', 'products', 'search', 'sort_oqrderQty'));
    }
    public function transaction_list(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $accounts = Account::orderBy('id')->get();

        $from = $request->from;
        $to = $request->to;

        $query = Transaction::where('supplier_id', $id)
            ->when($from != null, function ($q) use ($request) {
                return $q->whereBetween('date', [$request['from'], $request['to']]);
            });


        $transactions = $query->latest()->paginate(Helpers::pagination_limit())->appends(['from' => $request['from'], 'to' => $request['to']]);
        return view('admin-views.supplier.transaction-list', compact('supplier', 'accounts', 'transactions', 'from', 'to'));
    }
    public function add_new_purchase(Request $request)
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
            Toastr::warning(\App\CPU\translate('you_do_not_have_sufficent_balance'));
            return back();
        }
        if ($request->paid_amount > 0) {
            $payment_transaction = new Transaction;
            $payment_transaction->tran_type = 'Expense';
            $payment_transaction->account_id = $payment_account->id;
            $payment_transaction->amount = $request->paid_amount;
            $payment_transaction->description = 'Supplier payment';
            $payment_transaction->debit = 1;
            $payment_transaction->credit = 0;
            $payment_transaction->balance = $payment_account->balance - $request->paid_amount;
            $payment_transaction->date = date("Y/m/d");
            $payment_transaction->company_id = auth('admin')->user()->company_id;
            $payment_transaction->supplier_id = $request->supplier_id;
            $payment_transaction->save();

            $payment_account->total_out = $payment_account->total_out + $request->paid_amount;
            $payment_account->balance = $payment_account->balance - $request->paid_amount;
            $payment_account->save();
        }

        if ($request->due_amount > 0) {
            $payable_account = Account::find(2);
            $payable_transaction = new Transaction;
            $payable_transaction->tran_type = 'Payable';
            $payable_transaction->account_id = $payable_account->id;
            $payable_transaction->amount = $request->due_amount;
            $payable_transaction->description = 'Supplier payment';
            $payable_transaction->debit = 0;
            $payable_transaction->credit = 1;
            $payable_transaction->balance = $payable_account->balance + $request->due_amount;
            $payable_transaction->date = date("Y/m/d");
            $payable_transaction->company_id = auth('admin')->user()->company_id;
            $payable_transaction->supplier_id = $request->supplier_id;
            $payable_transaction->save();

            $payable_account->total_in = $payable_account->total_in + $request->due_amount;
            $payable_account->balance = $payable_account->balance + $request->due_amount;
            $payable_account->save();

            $supplier = Supplier::find($request->supplier_id);
            $supplier->due_amount = $supplier->due_amount + $request->due_amount;
            $supplier->save();
        }

        Toastr::success(translate('Supplier new payment added successfully'));
        return back();
    }
    public function pay_due(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'total_due_amount' => 'required',
            'pay_amount' => 'required',
            'remaining_due_amount' => 'required',
            'payment_account_id' => 'required',
        ]);

        $payment_account = Account::find($request->payment_account_id);
        if ($payment_account->balance < $request->pay_amount) {
            Toastr::warning(\App\CPU\translate('you_do_not_have_sufficent_balance!'));
            return back();
        }

        if ($request->pay_amount > 0) {
            $payment_transaction = new Transaction;
            $payment_transaction->tran_type = 'Expense';
            $payment_transaction->account_id = $payment_account->id;
            $payment_transaction->amount = $request->pay_amount;
            $payment_transaction->description = 'Supplier due payment';
            $payment_transaction->debit = 1;
            $payment_transaction->credit = 0;
            $payment_transaction->balance = $payment_account->balance - $request->pay_amount;
            $payment_transaction->date = date("Y/m/d");
            $payment_transaction->company_id = auth('admin')->user()->company_id;
            $payment_transaction->supplier_id = $request->supplier_id;
            $payment_transaction->save();

            $payment_account->total_out = $payment_account->total_out + $request->pay_amount;
            $payment_account->balance = $payment_account->balance - $request->pay_amount;
            $payment_account->save();

            $payable_account = Account::find(2);
            $payable_transaction = new Transaction;
            $payable_transaction->tran_type = 'Payable';
            $payable_transaction->account_id = $payable_account->id;
            $payable_transaction->amount = $request->pay_amount;
            $payable_transaction->description = 'Supplier due payment';
            $payable_transaction->debit = 1;
            $payable_transaction->credit = 0;
            $payable_transaction->balance = $payable_account->balance - $request->pay_amount;
            $payable_transaction->date = date("Y/m/d");
            $payable_transaction->company_id = auth('admin')->user()->company_id;
            $payable_transaction->supplier_id = $request->supplier_id;
            $payable_transaction->save();

            $payable_account->total_out = $payable_account->total_out + $request->pay_amount;
            $payable_account->balance = $payable_account->balance - $request->pay_amount;
            $payable_account->save();
        }

        $supplier = Supplier::find($request->supplier_id);
        $supplier->due_amount = $supplier->due_amount - $request->pay_amount;
        $supplier->save();

        Toastr::success(translate('Supplier payment due successfully'));
        return back();
    }
    public function edit($id)
    {
        $supplier = Supplier::find($id);
        return view('admin-views.supplier.edit', compact('supplier'));
    }
    public function update(Request $request)
    {
        $supplier = Supplier::where('id', $request->id)->first();
        $request->validate([
            'name' => 'required',
            'mobile' => 'required|unique:suppliers,mobile,' . $supplier->id,
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
        ]);

        $supplier->name = $request->name;
        $supplier->mobile = $request->mobile;
        $supplier->email = $request->email;
        $supplier->company_id = auth('admin')->user()->company_id;
        $supplier->image = $request->has('image') ? Helpers::update('supplier/', $supplier->image, 'png', $request->file('image')) : $supplier->image;
        $supplier->save();

        Toastr::success(translate('Supplier updated successfully'));
        return back();
    }
    public function delete(Request $request)
    {
        $supplier = Supplier::find($request->id);
        Helpers::delete('supplier/' . $supplier['image']);
        $supplier->delete();

        Toastr::success(translate('Supplier removed successfully'));
        return back();
    }
}
