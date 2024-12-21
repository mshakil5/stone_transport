<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\PurchaseHistoryLog;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class FinancialStatementController extends Controller
{
    public function balanceSheet ()
    {
        return view('admin.accounts.balance_sheet.search');
    }

    public function balanceSheetReport(Request $request)
    {

        $validatedData = $request->validate([
            'start_date' => 'required',
        ]);
        $startDate = $request->input('start_date');
        if ($startDate) {
            $yest = Carbon::parse($startDate)->subDay()->format('Y-m-d');
        } else {
            $yest = Carbon::yesterday()->format('Y-m-d');
        }

        //Net Profit Today
        $netProfit = $this->calculateNetProfit($request);

        //Net Profit Till Yesterday
        $netProfitTillYesterday = $this->calculateNetProfitTillYesterday($yest);

        //All Fixed Asset
        $fixedAssetIds = ChartOfAccount::where('sub_account_head', 'Fixed Asset')
            ->pluck('id');

        // $fixedAsset = Transaction::whereIn('chart_of_account_id', $fixedAssetIds)
        //     ->where('status', 0)
        //     ->get();

        $today = date('Y-m-d');

        //Current Asset
        $currentAssets = ChartOfAccount::where('sub_account_head', 'Current Asset')
            ->with(['transactions' => function ($query) use ($yest) {
            }])->get();

        //Debit till yesterday   
        $currentAssets->each(function ($asset) use ($yest, $today) {
            $asset->total_debit_yesterday = $asset->transactions()
                ->where('transaction_type', 'Received')
                ->whereDate('', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Credit till yesterday
        $currentAssets->each(function ($asset) use ($yest) {
            $asset->total_credit_yesterday = $asset->transactions()
                ->where('transaction_type', 'Payment')
                ->whereDate('date', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Debit Today
        $currentAssets->each(function ($asset) use ($today, $startDate) {
            $asset->total_debit_today = $asset->transactions()
                ->where('transaction_type', 'Received')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Credit Today
        $currentAssets->each(function ($asset) use ($today, $startDate) {
            $asset->total_credit_today = $asset->transactions()
                ->where('transaction_type', 'Payment')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });


        //Fixed Asset
        $fixedAssets = ChartOfAccount::where('sub_account_head', 'Fixed Asset')
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query->where('status', 0)
                    ->whereDate('date', '<=', $yest);
            }], 'at_amount')
            ->get();

        //Debit till yesterday
        $fixedAssets->each(function ($asset) use ($yest) {
            $asset->total_debit_yesterday = $asset->transactions()
                ->where('transaction_type', 'Purchase')
                ->whereDate('date', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Credit till yesterday
        $fixedAssets->each(function ($asset) use ($yest) {
            $asset->total_credit_yesterday = $asset->transactions()
                ->whereIn('transaction_type', ['Sold', 'Depreciation'])
                ->whereDate('date', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Debit Today
        $fixedAssets->each(function ($asset) use ($today, $startDate) {
            $asset->total_debit_today = $asset->transactions()
                ->where('transaction_type', 'Purchase')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        
        //Credit Today
        $fixedAssets->each(function ($asset) use ($today, $startDate) {
            $asset->total_credit_today = $asset->transactions()
                ->whereIn('transaction_type', ['Sold', 'Depreciation'])
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Account Receivable
        $accountReceiveableIds = ChartOfAccount::where('sub_account_head', 'Account Receivable')
            ->pluck('id');

        //Todays Account Receivable
        $todaysAccountReceivableCredit = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
            ->where('status', 0)
            ->where('transaction_type', 'Received')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Sales Return Credit   
        $salesReturnCredit = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('payment_type', 'Account Receivable')
            ->where('transaction_type', 'Return')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        $salesARpayment = Transaction::where('table_type', 'Sales')
            ->where('status', 0)
            ->whereNotNull('customer_id')
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->where('transaction_type', 'Received')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Total Todays Account Receivable    
        $totalTodaysAccountReceivableCredit = $todaysAccountReceivableCredit + $salesReturnCredit + $salesARpayment;

        //Todays Account Receivable Debit
        $todaysAccountReceivableDebit = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Purchase', 'Payment'])
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Todays Asset Sold Account Receivable    
        $todaysAssetSoldAR = Transaction::whereIn('asset_id', $accountReceiveableIds)
            ->where('status', 0)
            ->where('transaction_type', 'Sold')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');


        //Yesterday's account receivable debit
        $yesAccountReceiveablesDebit = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
            ->where('status', 0)
            ->where('transaction_type', 'Payment')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Yesterday's account receivable credit    
        $yesAccountReceiveablesCredit = Transaction::whereIn('chart_of_account_id', $accountReceiveableIds)
            ->where('status', 0)
            ->where('transaction_type', 'Received')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Yesterday's asset sold account receivable    
        $yesAssetSoldAR = Transaction::whereIn('asset_id', $accountReceiveableIds)
            ->where('status', 0)
            ->where('transaction_type', 'Sold')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Yesterday's product credit sold    
        $yesProductCreditSold = Transaction::where('status', 0)
            ->where('table_type', 'Income')
            ->whereNotNull('order_id')
            ->where('transaction_type', 'Current')
            ->where('payment_type', 'Account Receivable')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Yesterday's return Account Receivable    
        $yesReturnAR = Transaction::where('status', 0)
            ->where('table_type', 'Income')
            ->whereNotNull('order_id')
            ->where('transaction_type', 'Return')
            ->where('payment_type', 'Account Receivable')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Total Yesterday's account receivable    
        $yesAccountReceiveable = $yesAccountReceiveablesDebit + $yesAssetSoldAR - $yesAccountReceiveablesCredit + $yesProductCreditSold - $yesReturnAR;

        // vat payable calculation start 
        $todaysVatPayableDebit = Transaction::where('table_type', 'Sales')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->whereNotNull('customer_id')
            ->where('transaction_type', 'Current')
            ->whereBetween('date', [$startDate, $today])
            ->sum('vat_amount');
        // vat payable calculation end

        //Account Payable
        $accountPayableIds = ChartOfAccount::where('sub_account_head', 'Account Payable')
            ->pluck('id');

        //Todays Account Payable
        $todaysAccountPayableCredit = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ->where('transaction_type', 'Payment')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');
        //Todays Purchase Return Account Payable
        $todaysPurchaseReturnAP = Transaction::where('table_type', 'Purchase')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->where('payment_type', 'Account Payable')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Todays total Account Payable Credit
        $totalTodaysAccountPayableCredit = $todaysAccountPayableCredit + $todaysPurchaseReturnAP;

        //Today's account payable debit
        $todaysAccountPayableDebit = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Received'])
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Today's product purchse by credit
        $todaysCreditPurchaseAP = Transaction::where('table_type', 'Purchase')
            ->where('status', 0)
            ->where('payment_type', 'Credit')
            ->where('transaction_type', 'Due')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');
            

        //Today's product purchse payment
        $todaysPurchaseAPRcv = Transaction::where('table_type', 'Purchase')
            ->where('status', 0)
            ->whereIn('payment_type', ['Cash','Bank'])
            ->where('transaction_type', 'Current')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Todays due account payable debit   
        $todaysDueAccountPayableDebit = Transaction::whereIn('liability_id', $accountPayableIds)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Payment', 'Due', 'Purchase'])
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');


        //This query is related to account receivable
        $todaysProductCreditSold = Transaction::where('status', 0)
            ->whereNotNull('order_id')
            ->whereNotNull('customer_id')
            ->where('transaction_type', 'Current')
            ->where('payment_type', 'Credit')
            ->whereBetween('date', [$startDate, $today])
            ->sum('amount');

        //Yesterday's account payable credit    
        $yesAccountPayableCredit = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ->where('transaction_type', 'Payment')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Yesterday's account payable debit
        $yesAccountPayableDebit = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ->where('transaction_type', 'Received')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Yesterday's Asset expense and due   
        $yesAssetExpenseDue = Transaction::whereIn('liability_id', $accountPayableIds)
            ->where('status', 0)
            
            ->whereIn('transaction_type', ['Due', 'Purchase'])
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Yesterday's product purchse by credit
        $yesProductCreditPurchase = Transaction::where('status', 0)
            ->whereNotNull('purchase_id')
            ->where('transaction_type', 'Due')
            ->where('payment_type', 'Credit')
            ->where('date', '<=', $yest)
            ->sum('amount');

        //Yesterday's product purchse by credit
        $yesProductCreditPurchase = Transaction::where('status', 0)
            ->whereNotNull('supplier_id')
            ->where('transaction_type', 'Current')
            ->whereIn('payment_type', ['Cash', 'Bank'])
            ->where('date', '<=', $yest)
            ->sum('amount');
            
        //yesterday's purchase return by credit
        $yesPurchaseReturnAP = Transaction::where('table_type', 'Cogs')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->where('payment_type', 'Account Payable')
            ->where('date', '<=', $yest)
            
            ->sum('at_amount');

        //Total yesterday's account payable
        $yesAccountPayable = $yesAccountPayableDebit + $yesProductCreditPurchase + $yesAssetExpenseDue - $yesAccountPayableCredit - $yesPurchaseReturnAP;

        //Short Term Liabilities
        $shortTermLiabilities = ChartOfAccount::where('sub_account_head', 'Short Term Liabilities')
            
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query
                    ->whereDate('date', '<=', $yest)
                    ->where('status', 0);
            }], 'at_amount')
            ->get();

        //Yesterday credit   
        $shortTermLiabilities->each(function ($liability) use ($yest) {
            $liability->total_debit_yesterday = $liability->transactions()
                
                ->where('transaction_type', 'Received')
                ->whereDate('date', '<=',  $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Yesterday debit
        $shortTermLiabilities->each(function ($liability) use ($yest) {
            $liability->total_credit_yesterday = $liability->transactions()
                
                ->where('transaction_type', 'Payment')
                ->whereDate('date', '<=',  $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Today credit
        $shortTermLiabilities->each(function ($liability) use ($today, $startDate) {
            $liability->total_debit_today = $liability->transactions()
                
                ->where('transaction_type', 'Received')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Today debit
        $shortTermLiabilities->each(function ($liability) use ($today, $startDate) {
            $liability->total_credit_today = $liability->transactions()
                
                ->where('transaction_type', 'Payment')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Long Term Liabilities
        $longTermLiabilities = ChartOfAccount::where('sub_account_head', 'Long Term Liabilities')
            
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query
                    ->whereDate('date', '<=', $yest)
                    ->where('status', 0);
            }], 'at_amount')
            ->get();

        //Yesterday credit    
        $longTermLiabilities->each(function ($liability) use ($yest) {
            $liability->total_debit_yesterday = $liability->transactions()
                
                ->where('transaction_type', 'Received')
                ->whereDate('date',  '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Yesterday debit
        $longTermLiabilities->each(function ($liability) use ($yest) {
            $liability->total_credit_yesterday = $liability->transactions()
                
                ->where('transaction_type', 'Payment')
                ->whereDate('date',  '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Today credit
        $longTermLiabilities->each(function ($liability) use ($today, $startDate) {
            $liability->total_debit_today = $liability->transactions()
                
                ->where('transaction_type', 'Received')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Today debit
        $longTermLiabilities->each(function ($liability) use ($today, $startDate) {
            $liability->total_credit_today = $liability->transactions()
                
                ->where('transaction_type', 'Payment')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Current Liabilities
        $currentLiabilities = ChartOfAccount::where('sub_account_head', 'Current Liabilities')
            
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query
                    ->whereDate('date', '<=', $yest)
                    ->where('status', 0);
            }], 'at_amount')
            ->get();

        //Yesterday debit
        $currentLiabilities->each(function ($liability) use ($yest) {
            $liability->total_debit_yesterday = $liability->transactions()
                
                ->where('transaction_type', 'Received')
                ->whereDate('date', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Yesterday credit
        $currentLiabilities->each(function ($liability) use ($yest) {
            $liability->total_credit_yesterday = $liability->transactions()
                
                ->where('transaction_type', 'Payment')
                ->whereDate('date', '<=', $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Today credit    
        $currentLiabilities->each(function ($liability) use ($today, $startDate) {
            $liability->total_debit_today = $liability->transactions()
                
                ->where('transaction_type', 'Received')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Today debit
        $currentLiabilities->each(function ($liability) use ($today, $startDate) {
            $liability->total_credit_today = $liability->transactions()
                
                ->where('transaction_type', 'Payment')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Equity Capital
        $equityCapitals = ChartOfAccount::where('sub_account_head', 'Equity Capital')
            // ->withSum(['transactions' => function ($query) use ( $today) {
            //     $query->where('transaction_type', 'Payment')
            //         ->whereDate('date', '!=', $today);
            // }], 'at_amount')
            ->get();

        //yesterday credit
        $equityCapitals->each(function ($equity) use ($yest) {
            $equity->total_previous_payment = $equity->transactions()
                ->where('status', 0)
                ->where('transaction_type', 'Payment')
                ->whereDate('date', '<=', $yest)
                ->sum('at_amount');
        });

        //yesterday debit
        $equityCapitals->each(function ($equity) use ($yest) {
            $equity->total_previous_receive = $equity->transactions()
                ->where('status', 0)
                ->where('transaction_type', 'Received')
                ->whereDate('date', '<=', $yest)
                ->sum('at_amount');
        });

        //Today debit
        $equityCapitals->each(function ($equity) use ($startDate, $today) {
            $equity->total_debit_today = $equity->transactions()
                ->where('status', 0)
                ->where('transaction_type', 'Received')
                ->whereBetween('date', [$startDate, $today])
                ->sum('at_amount');
        });

        //Today credit
        $equityCapitals->each(function ($equity) use ($today, $startDate) {
            $equity->total_credit_today = $equity->transactions()
                ->where('status', 0)
                ->where('transaction_type', 'Payment')
                ->whereBetween('date', [$startDate, $today])
                ->sum('at_amount');
        });


        //Retained Earnings
        $retainedEarnings = ChartOfAccount::where('sub_account_head', 'Retained Earnings')
            
            ->withSum(['transactions' => function ($query) use ($yest) {
                $query
                    ->whereDate('date', '<=', $yest)
                    ->where('status', 0);
            }], 'at_amount')
            ->get();

        //yesterday debit    
        $retainedEarnings->each(function ($liability) use ($yest) {
            $liability->total_debit_yesterday = $liability->transactions()
                
                ->where('transaction_type', 'Received')
                ->whereDate('date', '<=',  $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //yesterday credit
        $retainedEarnings->each(function ($liability) use ($yest) {
            $liability->total_credit_yesterday = $liability->transactions()
                
                ->where('transaction_type', 'Payment')
                ->whereDate('date', '<=',  $yest)
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Today debit
        $retainedEarnings->each(function ($liability) use ($today, $startDate) {
            $liability->total_debit_today = $liability->transactions()
                
                ->where('transaction_type', 'Received')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //Today credit
        $retainedEarnings->each(function ($liability) use ($today, $startDate) {
            $liability->total_credit_today = $liability->transactions()
                
                ->where('transaction_type', 'Payment')
                ->whereBetween('date', [$startDate, $today])
                ->where('status', 0)
                ->sum('at_amount');
        });

        //All current assets
        $currentAssetIds = ChartOfAccount::where('sub_account_head', 'Current Asset')
            ->pluck('id');

        //Current Bank Asset    
        $currentBankAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            
            ->sum('at_amount');

        //Current Cash Asset    
        $currentCashAsset = Transaction::whereIn('chart_of_account_id', $currentAssetIds)
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->sum('at_amount');

        //Account Payable
        $accountPayables = Transaction::whereIn('chart_of_account_id', $accountPayableIds)
            ->where('status', 0)
            ;

        if (request()->has('startDate') && request()->has('endDate')) {
            $accountPayables->whereBetween('date', [request()->input('startDate'), request()->input('endDate')]);
        }

        //Inventory 
        $inventory = PurchaseHistoryLog::when($request->has('startDate'), function ($query) use ($request, $today) {
            $query->whereBetween('log_date', [$request->input('startDate'), $today]);
        })->sum('total_amount');

        $yesInventory = PurchaseHistoryLog::whereDate('log_date', $yest)->sum('total_amount');

        $purchaseDues = Purchase::all();

        if (request()->has('startDate')) {
            $purchaseDues->whereBetween('date', [request()->input('startDate'), $today]);
        }

        //Total account payable
        $accountPayable = $accountPayables->sum('at_amount') + $purchaseDues->sum('due_amount');

        //All current liabilities
        $currentLiabilityIds = ChartOfAccount::where('sub_account_head', 'Current Liabilities')
            ->pluck('id');

        $currentLiability = Transaction::whereIn('chart_of_account_id', $currentLiabilityIds)
            ->where('status', 0)
            
            ->sum('at_amount');

        //All long term liabilities
        $longTermLiabilityIds = ChartOfAccount::where('sub_account_head', 'Long Term Liabilities')
            ->pluck('id');

        $longTermLiability = Transaction::whereIn('chart_of_account_id', $longTermLiabilityIds)
            ->where('status', 0)
            
            ->get();

        //All equity capital
        $equityCapitalIds = ChartOfAccount::where('sub_account_head', 'Equity Capital')
            ->pluck('id');

        $equityCapital = Transaction::whereIn('chart_of_account_id', $equityCapitalIds)
            ->where('status', 0)
            
            ->sum('at_amount');

        //All retained earnings
        $retainedEarningIds = ChartOfAccount::where('sub_account_head', 'Retained Earnings')
            ->pluck('id');

        $retainedEarning = Transaction::whereIn('chart_of_account_id', $retainedEarningIds)
            ->where('status', 0)
            
            ->sum('at_amount');

        //Cash Income Increment today
        $CashIncomeIncrementToday = Transaction::where('table_type', 'Income')
            ->whereIn('transaction_type', ['Current', 'Advance'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            
            ->sum('at_amount');

        //Cash Asset Increment today
        $CashAssetIncrementToday = Transaction::where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Sold', 'Received'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            
            ->sum('at_amount');

        //Cash Liabilities Increment today
        $CashLiabilitiesIncrementToday = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            
            ->sum('at_amount');

        //Cash Equity Increment today
        $CashEquityIncrementToday = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            
            ->sum('at_amount');

        $PurchaseReturnCashIncrementToday = Transaction::where('table_type', 'Purchase')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        $salesCashIncrementToday = Transaction::where('table_type', 'Sales')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Total Cash Increment today
        $totalTodayCashIncrements = $CashIncomeIncrementToday + $CashAssetIncrementToday + $salesCashIncrementToday + $CashLiabilitiesIncrementToday + $CashEquityIncrementToday + $PurchaseReturnCashIncrementToday;

        //Bank Income Increment today
        $todayBankIncomeIncrement = Transaction::where('table_type', 'Income')
            ->whereIn('transaction_type', ['Current', 'Advance'])
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Bank Asset Increment today
        $todayBankAssetIncrement = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Sold')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Bank Liabilities Increment today
        $todayBankLiabilitiesIncrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Bank Equity Increment today
        $todayBankEquityIncrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Purchase Return Bank Increment
        $PurchaseReturnBankIncrementToday = Transaction::where('table_type', 'Purchase')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        $salesBankIncrementToday = Transaction::where('table_type', 'Sales')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Total Today Bank Increment
        $totalTodayBankIncrements = $todayBankIncomeIncrement + $todayBankAssetIncrement+ $salesBankIncrementToday + $todayBankLiabilitiesIncrement + $todayBankEquityIncrement + $PurchaseReturnBankIncrementToday;

        //Cash Expense Decrement today
        $expenseCashDecrement = Transaction::where('table_type', 'Expenses')
            ->whereIn('transaction_type', ['Current', 'Prepaid'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Cash Asset Decrement today
        $assetCashDecrement = Transaction::where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Payment', 'Purchase'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Cash Liabilities Decrement today
        $liabilitiesCashDecrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Cash Equity Decrement today
        $equityCashDecrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Cash Income Decrement today
        $incomeCashDecrement = Transaction::where('table_type', 'Income')
            ->where('transaction_type', 'Refund')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Cash Purchase Decrement today
        $purchaseCashDecrement = Transaction::where('table_type', 'Purchase')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('transaction_type', 'Current')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Cash Sales Return Decrement today    
        $salesReturnCashDecrement = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('transaction_type', 'Return')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Total Today Cash Decrement
        $totalTodayCashDecrements = $expenseCashDecrement + $assetCashDecrement + $liabilitiesCashDecrement + $equityCashDecrement + $incomeCashDecrement + $purchaseCashDecrement + $salesReturnCashDecrement;

        //Bank Expense Decrement today
        $todayExpenseBankDecrement = Transaction::where('table_type', 'Expenses')
            ->whereIn('transaction_type', ['Current', 'Prepaid'])
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Bank Asset Decrement today
        $todayAssetBankDecrement = Transaction::where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Payment', 'Purchase'])
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');


        //Bank Liabilities Decrement today
        $todayLiabilitiesBankDecrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');


        //Bank Equity Decrement today
        $todayEquityBankDecrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');


        //Bank Income Decrement today
        $todayIncomeBankDecrement = Transaction::where('table_type', 'Income')
            ->where('transaction_type', 'Refund')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Bank Purchase Decrement today    
        $purchaseBankDecrement = Transaction::where('table_type', 'Purchase')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('transaction_type', 'Current')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        //Bank Sales Return Decrement today         
        $salesReturnBankDecrement = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('transaction_type', 'Return')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');


        //Total Today Bank Decrement
        $totalTodayBankDecrements = $todayExpenseBankDecrement + $todayAssetBankDecrement + $todayLiabilitiesBankDecrement + $todayEquityBankDecrement + $todayIncomeBankDecrement + $purchaseBankDecrement + $salesReturnBankDecrement;

        //Cash in Hand and Bank
        $cashInHand = $totalTodayCashIncrements - $totalTodayCashDecrements;
        $cashInBank = $totalTodayBankIncrements - $totalTodayBankDecrements;

        //Till Yesterday Income Cash Increment
        $yestCashIncomeIncrement = Transaction::where('table_type', 'Income')
            ->whereIn('transaction_type', ['Current', 'Advance'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Asset Cash Increment    
        $yestCashAssetIncrement = Transaction::where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Received', 'Sold'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Liabilities Cash Increment
        $yestCashLiabilitiesIncrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Equity Cash Increment
        $yestCashEquityIncrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Purchase Return Cash Increment
        $yestpurchaseReturnCashIncrement = Transaction::where('table_type', 'Cogs')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Total Till Yesterday Cash Increment
        $totalYestCashIncrement = $yestCashIncomeIncrement  + $yestCashAssetIncrement + $yestCashLiabilitiesIncrement + $yestCashEquityIncrement + $yestpurchaseReturnCashIncrement;

        //Till Yesterday Expense Cash Decrement
        $yestExpenseCashDecrement = Transaction::where('table_type', 'Expenses')
            ->whereIn('transaction_type', ['Current', 'Prepaid'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Asset Cash Decrement
        $yestAssetCashDecrement = Transaction::where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Payment', 'Purchase'])
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Liabilities Cash Decrement
        $yestLiabilitiesCashDecrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Equity Cash Decrement
        $yestEquityCashDecrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Income Cash Decrement
        $yestIncomeCashDecrement = Transaction::where('table_type', 'Income')
            ->where('transaction_type', 'Refund')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Purchase Cash Decrement
        $yestPurchaseCashDecrement = Transaction::where('table_type', 'Cogs')
            ->where('transaction_type', 'Current')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Cash Sales Return Decrement    
        $yesSalesReturnCashDecrement = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->where('payment_type', 'Cash')
            ->where('transaction_type', 'Return')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Total Till Yesterday Cash Decrement
        $totalYestCashDecrement = $yestExpenseCashDecrement + $yestAssetCashDecrement + $yestLiabilitiesCashDecrement + $yestEquityCashDecrement + $yestIncomeCashDecrement + $yestPurchaseCashDecrement + $yesSalesReturnCashDecrement;

        //Till Yesterday Bank Increment
        //Till Yesterday Asset Bank Increment
        $yestAssetBankIncrement = Transaction::where('table_type', 'Assets')
            ->where('transaction_type', 'Sold')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Liabilities Bank Increment
        $yestLiabilitiesBankIncrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Equity Bank Increment
        $yestEquityBankIncrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Received')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Income Bank Increment
        $yestIncomeBankIncrement = Transaction::where('table_type', 'Income')
            ->whereIn('transaction_type', ['Current', 'Advance'])
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Purchase Bank Increment
        $yestpurchaseReturnCashIncrement = Transaction::where('table_type', 'Cogs')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');


        //Total Till Yesterday Bank Increment 
        $totalYestBankIncrement = $yestAssetBankIncrement + $yestLiabilitiesBankIncrement + $yestEquityBankIncrement + $yestIncomeBankIncrement + $yestpurchaseReturnCashIncrement;


        //Till Yesterday Bank Decrement
        //Till Yesterday Expense Bank Decrement
        $yestExpenseBankDecrement = Transaction::where('table_type', 'Expenses')
            ->whereIn('transaction_type', ['Current', 'Prepaid'])
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Asset Bank Decrement
        $yestAssetBankDecrement = Transaction::where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Payment', 'Purchase'])
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Liabilities Bank Decrement
        $yestLiabilitiesBankDecrement = Transaction::where('table_type', 'Liabilities')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Equity Bank Decrement
        $yestEquityBankDecrement = Transaction::where('table_type', 'Equity')
            ->where('transaction_type', 'Payment')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Income Bank Decrement
        $yestIncomeBankDecrement = Transaction::where('table_type', 'Income')
            ->where('transaction_type', 'Refund')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Purchase Bank Decrement
        $yestPurchaseBankDecrement = Transaction::where('table_type', 'Cogs')
            ->where('transaction_type', 'Current')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Till Yesterday Sales Return Bank Decrement
        $yestSalesRetunBankDecrement = Transaction::where('table_type', 'Income')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->where('payment_type', 'Bank')
            ->where('date', '<=', $yest)
            ->sum('at_amount');

        //Total Till Yesterday Bank Decrement
        $totalYestBankDecrement = $yestExpenseBankDecrement + $yestAssetBankDecrement + $yestLiabilitiesBankDecrement + $yestEquityBankDecrement + $yestIncomeBankDecrement + $yestPurchaseBankDecrement + $yestSalesRetunBankDecrement;

        //Yesterday Cash In Hand
        $yesCashInHand = $totalYestCashIncrement - $totalYestCashDecrement;

        //Yesterday Bank In Hand
        $yesBankInHand = $totalYestBankIncrement - $totalYestBankDecrement;

        return view('admin.accounts.balance_sheet.index', compact('currentAssetIds', 'currentBankAsset', 'currentCashAsset', 'currentLiability', 'longTermLiabilities', 'equityCapital', 'retainedEarning', 'currentAssets', 'fixedAssets', 'shortTermLiabilities', 'currentLiabilities', 'equityCapitals', 'retainedEarnings', 'cashInHand', 'cashInBank', 'inventory', 'netProfit', 'yesCashInHand', 'yesBankInHand', 'yesAccountReceiveable', 'yesInventory', 'netProfitTillYesterday', 'totalTodayCashIncrements', 'totalTodayCashDecrements', 'totalTodayBankIncrements', 'totalTodayBankDecrements', 'todaysAccountReceivableDebit', 'todaysAssetSoldAR', 'yesAccountPayable', 'totalTodaysAccountPayableCredit', 'todaysAccountPayableDebit', 'todaysProductCreditSold', 'todaysDueAccountPayableDebit', 'todaysCreditPurchaseAP', 'totalTodaysAccountReceivableCredit','startDate','todaysPurchaseAPRcv','todaysVatPayableDebit'));
    }

    public function calculateNetProfit(Request $request)
    {
        $today = date('Y-m-d');
        $startDate = $request->input('start_date');

        // Sales sum today
        $salesSumToday = Transaction::where('table_type', 'Sales')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->whereNotNull('customer_id')
            ->where('transaction_type', 'Current')
            ->whereBetween('date', [$startDate, $today])
            ->sum('amount');

        // Sales Return
        $salesReturnToday = Transaction::where('table_type', 'Sales')
            ->where('status', 0)
            ->where('transaction_type', 'Return')
            ->whereBetween('date', [$startDate, $today])
            ->sum('amount');

        // Sales Discount
        $salesDiscount = Order::when($request->has('startDate'), function ($query) use ($request, $today) {
                $query->whereBetween('created_at', [$request->input('startDate'), $today]);
            })
            ->sum('discount_amount');

        // Purchase sum (Cost of Goods Sold) Today
        $purchaseSumToday = Transaction::where('table_type', 'Purchase')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('transaction_type', 'Due')
            ->whereNull('chart_of_account_id')
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        // Operating Income today
        $operatingIncomeSumToday = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->whereIn('transaction_type', ['Current', 'Advance'])
            ->whereDate('date', $today)
            ->sum('amount');

        $operatingIncomeRefundToday = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->whereIn('transaction_type', ['Refund'])
            ->whereDate('date', $today)
            ->sum('amount');

        //Purchase return today
        $purchaseReturnToday = Transaction::where('table_type', 'Purchase')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->whereBetween('date', [$startDate, $today])
            ->sum('at_amount');

        // Operating Expenses today
        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenseSumToday = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->whereDate('date', $today)
            ->sum('amount');

        // OverHead Expenses today
        $overHeadExpenseId = ChartOfAccount::where('sub_account_head', 'Overhead Expense')->pluck('id');
        $overHeadExpenseSumToday = Transaction::whereIn('chart_of_account_id', $overHeadExpenseId)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->whereDate('date', $today)
            ->sum('amount');

        // Administrative Expenses
        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenseSumToday = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->whereDate('date', $today)
            ->sum('amount');

        //  //Fixed Assets   
        $FixedAssetId = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->where('account_head', 'Assets')->pluck('id');

        // //Fixed Asset sold depriciation Today
        $FixedAssetDepriciationToday = Transaction::whereIn('chart_of_account_id', $FixedAssetId)
            ->where('status', 0)
            ->where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Depreciation'])
            ->whereDate('date', $today)
            ->sum('amount');

        // VAT Calculations
        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->whereNull('chart_of_account_id')
            ->whereBetween('date', [$startDate, $today])
            ->sum('vat_amount');

        $salesVatSum = Transaction::where('table_type', 'Sales')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->whereBetween('date', [$startDate, $today])
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->whereDate('date', $today)
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->whereDate('date', $today)
            ->sum('vat_amount');

        // Net Profit Calculation
        $taxAndVat = $purchaseVatSum + $salesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;

        $netSalesToday = $salesSumToday - $salesReturnToday - $salesDiscount;



        $profitBeforeTax = $netSalesToday + $purchaseReturnToday - $purchaseSumToday + $operatingIncomeSumToday - $operatingIncomeRefundToday - $operatingExpenseSumToday - $administrativeExpenseSumToday - $overHeadExpenseSumToday - $FixedAssetDepriciationToday;

        $netProfit = $profitBeforeTax - $taxAndVat;

        return $netProfit;
    }

    public function calculateNetProfitTillYesterday($yest)
    {

        // Operating Income
        $yesOperatingIncome = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->whereIn('transaction_type', ['Current', 'Advance'])
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        //Operating Income Refund
        $yesOperatingIncomeRefund = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereNotNull('chart_of_account_id')
            ->where('transaction_type', 'Refund')
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        //Sales sum
        $salesSum = Transaction::where('table_type', 'Sales')
            ->where('status', 0)
            ->whereNull('chart_of_account_id')
            ->whereNot('transaction_type', 'Return')
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        //Previous Sales Return
        $salesReturn = Transaction::where('table_type', 'Sales')
            ->where('status', 0)
            ->where('transaction_type', 'Return')
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        //Sales Discount
        $salesDiscount = Order::whereDate('created_at', '<=', $yest)
            ->sum('discount_amount');

        //Purchase sum (Cost of Goods Sold)
        $purchaseSum = Transaction::where('table_type', 'Purchase')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->where('transaction_type', 'Due')
            ->whereNull('chart_of_account_id')
            ->whereDate('date', '<=', $yest)
            ->sum('at_amount');

        //Previous Purchase Return
        $previousPurchaseReturn = Transaction::where('table_type', 'Cogs')
            ->where('transaction_type', 'Return')
            ->where('status', 0)
            ->whereDate('date', '<=', $yest)
            ->sum('at_amount');

        // Operating Expenses
        $operatingExpenseId = ChartOfAccount::where('sub_account_head', 'Operating Expense')->pluck('id');
        $operatingExpenseSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->whereDate('date', '<=', $yest)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->sum('amount');

        // Overhead Expenses
        $overheadExpenseId = ChartOfAccount::where('sub_account_head', 'Overhead Expense')->pluck('id');
        $overheadExpenseSum = Transaction::whereIn('chart_of_account_id', $overheadExpenseId)
            ->where('status', 0)
            ->whereDate('date', '<=', $yest)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->sum('amount');

        // Administrative Expenses
        $administrativeExpenseId = ChartOfAccount::where('sub_account_head', 'Administrative Expense')->pluck('id');
        $administrativeExpenseSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->whereDate('date', '<=', $yest)
            ->whereIn('transaction_type', ['Current', 'Prepaid', 'Due'])
            ->sum('amount');

        //Fixed Assets   
        $FixedAssetId = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->where('account_head', 'Assets')->pluck('id');

        //Fixed Asset sold depriciation previous
        $FixedAssetDepriciation = Transaction::whereIn('chart_of_account_id', $FixedAssetId)
            ->where('status', 0)
            ->where('table_type', 'Assets')
            ->whereIn('transaction_type', ['Depreciation'])
            ->whereDate('date', '<=', $yest)
            ->sum('amount');

        //VAT Calculations
        $purchaseVatSum = Transaction::where('table_type', 'Cogs')
            ->where('status', 0)
            ->where('description', 'Purchase')
            ->whereNull('chart_of_account_id')
            ->whereDate('date', '<=', $yest)
            ->sum('vat_amount');

        //VAT Calculations    
        $salesVatSum = Transaction::where('table_type', 'Income')
            ->where('status', 0)
            ->whereDate('date', '<=', $yest)
            ->sum('vat_amount');

        $operatingExpenseVatSum = Transaction::whereIn('chart_of_account_id', $operatingExpenseId)
            ->where('status', 0)
            ->whereDate('date', '<=', $yest)
            ->sum('vat_amount');

        $administrativeExpenseVatSum = Transaction::whereIn('chart_of_account_id', $administrativeExpenseId)
            ->where('status', 0)
            ->whereDate('date', '<=', $yest)
            ->sum('vat_amount');

        // Net Profit Calculation
        $taxAndVat = $purchaseVatSum + $salesVatSum + $operatingExpenseVatSum + $administrativeExpenseVatSum;

        $netSales = $salesSum + $previousPurchaseReturn - $salesReturn - $salesDiscount + $yesOperatingIncome - $yesOperatingIncomeRefund - $FixedAssetDepriciation;

        $profitBeforeTax = $netSales - $purchaseSum - $operatingExpenseSum - $administrativeExpenseSum - $overheadExpenseSum;

        $netProfitTillYesterday = $profitBeforeTax - $taxAndVat;

        return $netProfitTillYesterday;
    }
}
