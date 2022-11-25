<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\CPU\Helpers;
use App\Models\Account;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\StockLimitedProductsResource;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerLoyaltyPointsSummary(Request $request)
    {
        if ($request->statistics_type == 'overall') {
            $total_payable_debit = Transaction::where('tran_type', 'Payable')->where('debit', 1)->sum('amount');
            $total_payable_credit = Transaction::where('tran_type', 'Payable')->where('credit', 1)->sum('amount');
            $total_payable = $total_payable_credit - $total_payable_debit;

            $total_receivable_debit = Transaction::where('tran_type', 'Receivable')->where('debit', 1)->sum('amount');
            $total_receivable_credit = Transaction::where('tran_type', 'Receivable')->where('credit', 1)->sum('amount');
            $total_receivable = $total_receivable_credit - $total_receivable_debit;

            $revenueSummary = [
                'totalIncome' => Transaction::where('tran_type', 'Income')->sum('amount'),
                'totalExpense' => Transaction::where('tran_type', 'Expense')->sum('amount'),
                'totalPayable' => $total_payable,
                'totalReceivable' => $total_receivable,
            ];
            return response()->json([
                'revenueSummary' => $revenueSummary
            ], 200);
        } elseif ($request->statistics_type == 'today') {

            $total_payable_debit = Transaction::where('tran_type', 'Payable')->whereDay('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_payable_credit = Transaction::where('tran_type', 'Payable')->whereDay('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_payable = $total_payable_credit - $total_payable_debit;

            $total_receivable_debit = Transaction::where('tran_type', 'Receivable')->whereDay('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_receivable_credit = Transaction::where('tran_type', 'Receivable')->whereDay('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_receivable = $total_receivable_credit - $total_receivable_debit;

            $revenueSummary = [
                'totalIncome' => Transaction::where('tran_type', 'Income')->whereDay('date', '=', Carbon::today())->sum('amount'),
                'totalExpense' => Transaction::where('tran_type', 'Expense')->whereDay('date', '=', Carbon::today())->sum('amount'),
                'totalPayable' => $total_payable,
                'totalReceivable' => $total_receivable,
            ];
            return response()->json([
                'revenueSummary' => $revenueSummary
            ], 200);
        } elseif ($request->statistics_type == 'month') {

            $total_payable_debit = Transaction::where('tran_type', 'Payable')->whereMonth('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_payable_credit = Transaction::where('tran_type', 'Payable')->whereMonth('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_payable = $total_payable_credit - $total_payable_debit;

            $total_receivable_debit = Transaction::where('tran_type', 'Receivable')->whereMonth('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_receivable_credit = Transaction::where('tran_type', 'Receivable')->whereMonth('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_receivable = $total_receivable_credit - $total_receivable_debit;

            $revenueSummary = [
                'totalIncome' => Transaction::where('tran_type', 'Income')->whereMonth('date', '=', Carbon::today())->sum('amount'),
                'totalExpense' => Transaction::where('tran_type', 'Expense')->whereMonth('date', '=', Carbon::today())->sum('amount'),
                'totalPayable' => $total_payable,
                'totalReceivable' => $total_receivable,
            ];
            return response()->json([
                'revenueSummary' => $revenueSummary
            ], 200);
        }
    }

    public function getCustomerPurchases(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $stock_limit = Helpers::get_business_settings('stock_limit');
        $stock_limited_product = Product::with('unit', 'supplier')->where('quantity', '<', $stock_limit)->orderBy('quantity')->latest()->paginate($limit, ['*'], 'page', $offset);
        $stock_limited_products = StockLimitedProductsResource::collection($stock_limited_product);

        return response()->json([
            'total' => $stock_limited_products->total(),
            'offset' => $offset,
            'limit' => $limit,
            'stock_limited_products' => $stock_limited_products->items(),
        ], 200);
    }

    public function getIndex(Request $request)
    {
        if ($request->statistics_type == 'overall') {
            $total_payable_debit = Transaction::where('tran_type', 'Payable')->where('debit', 1)->sum('amount');
            $total_payable_credit = Transaction::where('tran_type', 'Payable')->where('credit', 1)->sum('amount');
            $total_payable = $total_payable_credit - $total_payable_debit;

            $total_receivable_debit = Transaction::where('tran_type', 'Receivable')->where('debit', 1)->sum('amount');
            $total_receivable_credit = Transaction::where('tran_type', 'Receivable')->where('credit', 1)->sum('amount');
            $total_receivable = $total_receivable_credit - $total_receivable_debit;

            $revenueSummary = [
                'totalIncome' => Transaction::where('tran_type', 'Income')->sum('amount'),
                'totalExpense' => Transaction::where('tran_type', 'Expense')->sum('amount'),
                'totalPayable' => $total_payable,
                'totalReceivable' => $total_receivable,
            ];
            return response()->json([
                'revenueSummary' => $revenueSummary
            ], 200);
        } elseif ($request->statistics_type == 'today') {

            $total_payable_debit = Transaction::where('tran_type', 'Payable')->whereDay('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_payable_credit = Transaction::where('tran_type', 'Payable')->whereDay('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_payable = $total_payable_credit - $total_payable_debit;

            $total_receivable_debit = Transaction::where('tran_type', 'Receivable')->whereDay('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_receivable_credit = Transaction::where('tran_type', 'Receivable')->whereDay('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_receivable = $total_receivable_credit - $total_receivable_debit;

            $revenueSummary = [
                'totalIncome' => Transaction::where('tran_type', 'Income')->whereDay('date', '=', Carbon::today())->sum('amount'),
                'totalExpense' => Transaction::where('tran_type', 'Expense')->whereDay('date', '=', Carbon::today())->sum('amount'),
                'totalPayable' => $total_payable,
                'totalReceivable' => $total_receivable,
            ];
            return response()->json([
                'revenueSummary' => $revenueSummary
            ], 200);
        } elseif ($request->statistics_type == 'month') {

            $total_payable_debit = Transaction::where('tran_type', 'Payable')->whereMonth('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_payable_credit = Transaction::where('tran_type', 'Payable')->whereMonth('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_payable = $total_payable_credit - $total_payable_debit;

            $total_receivable_debit = Transaction::where('tran_type', 'Receivable')->whereMonth('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_receivable_credit = Transaction::where('tran_type', 'Receivable')->whereMonth('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_receivable = $total_receivable_credit - $total_receivable_debit;

            $revenueSummary = [
                'totalIncome' => Transaction::where('tran_type', 'Income')->whereMonth('date', '=', Carbon::today())->sum('amount'),
                'totalExpense' => Transaction::where('tran_type', 'Expense')->whereMonth('date', '=', Carbon::today())->sum('amount'),
                'totalPayable' => $total_payable,
                'totalReceivable' => $total_receivable,
            ];
            return response()->json([
                'revenueSummary' => $revenueSummary
            ], 200);
        }
    }

    public function productLimitedStockList(Request $request)
    {
        $limit = $request['limit'] ?? 10;
        $offset = $request['offset'] ?? 1;

        $stock_limit = Helpers::get_business_settings('stock_limit');
        $stock_limited_product = Product::with('unit', 'supplier')->where('quantity', '<', $stock_limit)->orderBy('quantity')->latest()->paginate($limit, ['*'], 'page', $offset);
        $stock_limited_products = StockLimitedProductsResource::collection($stock_limited_product);

        return response()->json([
            'total' => $stock_limited_products->total(),
            'offset' => $offset,
            'limit' => $limit,
            'stock_limited_products' => $stock_limited_products->items(),
        ], 200);
    }

    public function quantityIncrease(Request $request)
    {
        DB::table('products')->where('id', $request->id)->update(['quantity' => $request->quantity]);
        return response()->json(['message' => 'Product quantity updated successsfully']);
    }

    public function getFilter(Request $request)
    {
        if ($request->statistics_type == 'overall') {
            $total_payable_debit = Transaction::where('tran_type', 'Payable')->where('debit', 1)->sum('amount');
            $total_payable_credit = Transaction::where('tran_type', 'Payable')->where('credit', 1)->sum('amount');
            $total_payable = $total_payable_credit - $total_payable_debit;

            $total_receivable_debit = Transaction::where('tran_type', 'Receivable')->where('debit', 1)->sum('amount');
            $total_receivable_credit = Transaction::where('tran_type', 'Receivable')->where('credit', 1)->sum('amount');
            $total_receivable = $total_receivable_credit - $total_receivable_debit;
            $account = [
                'total_income' => Transaction::where('tran_type', 'Income')->sum('amount'),
                'total_expense' => Transaction::where('tran_type', 'Expense')->sum('amount'),
                'total_payable' => $total_payable,
                'total_receivable' => $total_receivable,
            ];
            return response()->json([
                'success' => true,
                'message' => "Overall Statistics",
                'data' => $account
            ], 200);
        } elseif ($request->statistics_type == 'today') {
            $total_payable_debit = Transaction::where('tran_type', 'Payable')->whereDay('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_payable_credit = Transaction::where('tran_type', 'Payable')->whereDay('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_payable = $total_payable_credit - $total_payable_debit;

            $total_receivable_debit = Transaction::where('tran_type', 'Receivable')->whereDay('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_receivable_credit = Transaction::where('tran_type', 'Receivable')->whereDay('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_receivable = $total_receivable_credit - $total_receivable_debit;

            $account = [
                'total_income' => Transaction::where('tran_type', 'Income')->whereDay('date', '=', Carbon::today())->sum('amount'),
                'total_expense' => Transaction::where('tran_type', 'Expense')->whereDay('date', '=', Carbon::today())->sum('amount'),
                'total_payable' => $total_payable,
                'total_receivable' => $total_receivable,
            ];
            return response()->json([
                'success' => true,
                'message' => "Today Statistics",
                'data' => $account
            ], 200);
        } elseif ($request->statistics_type == 'month') {

            $total_payable_debit = Transaction::where('tran_type', 'Payable')->whereMonth('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_payable_credit = Transaction::where('tran_type', 'Payable')->whereMonth('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_payable = $total_payable_credit - $total_payable_debit;

            $total_receivable_debit = Transaction::where('tran_type', 'Receivable')->whereMonth('date', '=', Carbon::today())->where('debit', 1)->sum('amount');
            $total_receivable_credit = Transaction::where('tran_type', 'Receivable')->whereMonth('date', '=', Carbon::today())->where('credit', 1)->sum('amount');
            $total_receivable = $total_receivable_credit - $total_receivable_debit;

            $account = [
                'total_income' => Transaction::where('tran_type', 'Income')->whereMonth('date', '=', Carbon::today())->sum('amount'),
                'total_expense' => Transaction::where('tran_type', 'Expense')->whereMonth('date', '=', Carbon::today())->sum('amount'),
                'total_payable' => $total_payable,
                'total_receivable' => $total_receivable,
            ];
            return response()->json([
                'success' => true,
                'message' => "Monthly Statistics",
                'data' => $account
            ], 200);
        }
    }
    public function incomeRevenue()
    {
        $year_wise_expense = Transaction::selectRaw("sum(`amount`) as 'total_amount', YEAR(`date`) as 'year', MONTH(`date`) as 'month'")->where(['tran_type' => 'Expense'])
            ->groupBy('month')
            ->orderBy('year')
            ->get();

        $year_wise_income = Transaction::selectRaw("sum(`amount`) as 'total_amount', YEAR(`date`) as 'year', MONTH(`date`) as 'month'")->where(['tran_type' => 'Income'])
            ->groupBy('month')
            ->orderBy('year')
            ->get();

        return response()->json([
            'year_wise_expense' => $year_wise_expense,
            'year_wise_income' => $year_wise_income
        ], 200);
    }
}
