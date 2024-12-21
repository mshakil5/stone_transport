@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Balance Sheet</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <div class="row">
                            <div class="col-md-12">

                                <div class="text-center mb-4 company-name-container">
                                    @php
                                    $company = \App\Models\CompanyDetails::select('company_name')->first();
                                    @endphp
                                    <h2>{{ $company->company_name }}</h2>
                                    <h4>Balance Sheet</h4>
                                    <h5>From {{date('d-m-Y', strtotime($startDate))}} to {{date('d-m-Y')}}</h5>
                                </div>
                        
                                <div class="table-responsive">
                                    <table id="cashIncomingTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Asset Start -->
                                            <tr>
                                                <td><strong>Asset</strong></td>
                                                <td><strong>Sub Asset Type</strong></td>
                                                <td><strong>Opening Balance</strong></td>
                                                <td><strong>Debit Movement</strong></td>
                                                <td><strong>Credit Movement</strong></td>
                                                <td><strong>Closing Balance</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Current Asset</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                {{-- <td><strong>Total Till Yesterday</strong></td>
                                                <td><strong>Total Of All Debit Txn Today</strong></td>
                                                <td><strong>Total Of All Credit Txn Today</strong></td>
                                                <td><strong>CB= OB+DMV+CMV </strong></td> --}}
                                            </tr>

                                            <!-- current assets  -->

                                            <tr>
                                                <td></td>
                                                <td>Cash In Hand</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($yesCashInHand, 2) }}
                                                </td>
                                                <td style="text-align: right;">{{ number_format($totalTodayCashIncrements, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalTodayCashDecrements, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($yesCashInHand + $cashInHand, 2) }}</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>Cash at Bank</td>
                                                <td style="text-align: right;"> 
                                                        {{ number_format($yesBankInHand, 2) }}
                                                </td>
                                                <td style="text-align: right;">                
                                                        {{ number_format($totalTodayBankIncrements, 2) }}
                                                </td>
                                                <td style="text-align: right;">
                                                        {{ number_format($totalTodayBankDecrements, 2) }}
                                                </td>
                                                <td style="text-align: right;">{{ number_format($cashInBank + $yesBankInHand, 2) }}</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>Account Receivable</td>
                                                <td style="text-align: right;">         
                                                    {{ number_format($yesAccountReceiveable, 2) }}
                                                </td>
                                                <td style="text-align: right;">{{ number_format($todaysAccountReceivableDebit + $todaysAssetSoldAR +$todaysProductCreditSold, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalTodaysAccountReceivableCredit, 2) }}</td>
                                                @php
                                                    $totalReceivable = $yesAccountReceiveable - $totalTodaysAccountReceivableCredit + $todaysAssetSoldAR + $todaysProductCreditSold + $todaysAccountReceivableDebit
                                                @endphp
                                                <td style="text-align: right;">{{ number_format( $totalReceivable, 2) }}</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>Inventory</td>
                                                <td style="text-align: right;">                            
                                                        {{ number_format($yesInventory, 2) }}       
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align: right;">{{ number_format($inventory, 2) }}</td>
                                            </tr>

                                            @foreach($currentAssets as $currentAsset)
                                            <tr>
                                                <td></td>
                                                <td>{{ $currentAsset->account_name }}</td>
                                                <td style="text-align: right;">{{ number_format($currentAsset->total_debit_yesterday - $currentAsset->total_credit_yesterday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($currentAsset->total_debit_today, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($currentAsset->total_credit_today, 2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format(
                                                        $currentAsset->total_debit_yesterday - $currentAsset->total_credit_yesterday +
                                                        $currentAsset->total_debit_today + 
                                                        $currentAsset->total_credit_today, 
                                                        2
                                                    ) }}
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td><strong>Fixed Asset</strong></td>
                                                <td colspan="5"></td>
                                            </tr>

                                            <!-- fixed assets  -->

                                            @php
                                                // Sum of yesterday's balance
                                                $fixedAssetTotalYesterdayBalance = $fixedAssets->sum(function($asset) {
                                                    return $asset->total_debit_yesterday - $asset->total_credit_yesterday;
                                                });

                                                // Sum of today's debits
                                                $fixedAssetTotalDebitToday = $fixedAssets->sum('total_debit_today');

                                                // Sum of today's credits
                                                $fixedAssetTotalCreditToday = $fixedAssets->sum('total_credit_today');

                                                // Sum of the final balance calculation
                                                $fixedAssetTotalFinalBalance = $fixedAssets->sum(function($asset) {
                                                    return ($asset->total_debit_yesterday - $asset->total_credit_yesterday) + ($asset->total_debit_today - $asset->total_credit_today);
                                                });
                                            @endphp

                                            @foreach($fixedAssets as $fixedAsset)
                                            <tr>
                                                <td></td>
                                                <td>{{ $fixedAsset->account_name }}</td>                            
                                                <td style="text-align: right;">{{ number_format($fixedAsset->total_debit_yesterday - $fixedAsset->total_credit_yesterday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($fixedAsset->total_debit_today, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($fixedAsset->total_credit_today, 2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format(
                                                        $fixedAsset->total_debit_yesterday - $fixedAsset->total_credit_yesterday + $fixedAsset->total_debit_today - $fixedAsset->total_credit_today, 2
                                                    ) }}
                                                </td>                           
                                            </tr>
                                            @endforeach
                        
                                            <tr>
                                                <td>
                                                    <strong>Total Asset</strong>
                                                </td>
                                                <td></td>
                                                @php
                                                $closingAssetBalance = $yesCashInHand + $cashInHand + $cashInBank + $yesBankInHand + $fixedAssetTotalFinalBalance  + $totalReceivable;
                                                @endphp
                                                <td style="text-align: right;">{{ number_format($yesBankInHand + $yesCashInHand + $yesAccountReceiveable + $fixedAssetTotalYesterdayBalance, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalTodayBankIncrements + $totalTodayCashIncrements + $fixedAssetTotalDebitToday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalTodayBankDecrements + $totalTodayCashDecrements + $fixedAssetTotalCreditToday, 2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($closingAssetBalance, 2) }}
                                                </td>
                                            </tr>
                                            <!-- Asset End -->

                                            <!-- Liabilities Start -->
                                            <tr>
                                                <td colspan="6"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Liability</strong></td>
                                                <td colspan="5"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Short Term Liability</strong></td>
                                                <td colspan="5"></td>
                                            </tr>

                                            <!-- short term liability  -->
                                            @foreach($shortTermLiabilities as $shortTermLiability)
                                            <tr>
                                                <td></td>
                                                <td>{{ $shortTermLiability->account_name }}</td>
                                                <td style="text-align: right;">{{ number_format($shortTermLiability->total_debit_yesterday - $shortTermLiability->total_credit_yesterday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($shortTermLiability->total_debit_today, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($shortTermLiability->total_credit_today, 2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($shortTermLiability->total_debit_yesterday - $shortTermLiability->total_credit_yesterday + $shortTermLiability->total_debit_today - $shortTermLiability->total_credit_today, 2) }}
                                                </td>
                                            </tr>
                                            @endforeach
                            
                                            <tr>
                                                <td><strong>Long Term Liability</strong></td>
                                                <td colspan="5"></td>
                                            </tr>

                                            <!-- long term liability  -->
                                            @foreach($longTermLiabilities as $longTermLiability)
                                            <tr>
                                                <td></td>
                                                <td>{{ $longTermLiability->account_name }}</td>
                                                <td style="text-align: right;">{{ number_format($longTermLiability->total_debit_yesterday - $longTermLiability->total_credit_yesterday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($longTermLiability->total_debit_today, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($longTermLiability->total_credit_today, 2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format(
                                                        $longTermLiability->total_debit_yesterday - $longTermLiability->total_credit_yesterday + 
                                                        $longTermLiability->total_debit_today - 
                                                        $longTermLiability->total_credit_today, 
                                                        2
                                                    ) }}
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td><strong>Current Liability</strong></td>
                                                <td colspan="5"></td>
                                            </tr>

                                            <!-- current liability  -->
                                            @foreach($currentLiabilities as $currentLiability)
                                            <tr>
                                                <td></td>
                                                <td>{{ $currentLiability->account_name }}</td>
                                                <td style="text-align: right;">{{ number_format($currentLiability->total_debit_yesterday - $currentLiability->total_credit_yesterday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($currentLiability->total_debit_today, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($currentLiability->total_credit_today, 2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format(
                                                        $currentLiability->total_debit_yesterday - $currentLiability->total_credit_yesterday + 
                                                        $currentLiability->total_debit_today - 
                                                        $currentLiability->total_credit_today, 
                                                        2
                                                    ) }}
                                                </td>
                                            </tr>
                                            @endforeach

                                            <!-- Account Payable -->
                                            <tr>
                                                <td></td>
                                                <td>Account Payable</td>
                                                <td style="text-align: right;">         
                                                    {{ number_format($yesAccountPayable, 2) }}
                                                </td>
                                                <td style="text-align: right;">{{ number_format($todaysAccountPayableDebit + $todaysDueAccountPayableDebit + $todaysCreditPurchaseAP, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalTodaysAccountPayableCredit + $todaysPurchaseAPRcv, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($yesAccountPayable - $totalTodaysAccountPayableCredit  - $todaysPurchaseAPRcv + $todaysCreditPurchaseAP + $todaysAccountPayableDebit+ $todaysDueAccountPayableDebit , 2) }}</td>
                                            </tr>
                                            <!-- Account Payable -->

                                            <!-- Vat Payable -->
                                            <tr>
                                                <td></td>
                                                <td>Vat Payable</td>
                                                <td style="text-align: right;">         
                                                    
                                                </td>
                                                <td style="text-align: right;">{{ number_format($todaysVatPayableDebit, 2) }}</td>
                                                <td style="text-align: right;"></td>
                                                <td style="text-align: right;">{{ number_format($todaysVatPayableDebit, 2) }}</td>
                                            </tr>
                                            <!-- Vat Payable -->

                                            <tr>
                                                <td>
                                                    <strong>Total Liability</strong>
                                                </td>
                                                <td></td>
                                                @php
                                                    $totalLiabilitySum = collect($shortTermLiabilities)->sum('total_debit_yesterday') - collect($shortTermLiabilities)->sum('total_credit_yesterday') +
                                                    collect($longTermLiabilities)->sum('total_debit_yesterday') - collect($longTermLiabilities)->sum('total_credit_yesterday') +
                                                                            collect($currentLiabilities)->sum('total_debit_yesterday') - collect($currentLiabilities)->sum('total_credit_yesterday') + $yesAccountPayable;
                                                    $totalLiabilityDebitToday = collect($shortTermLiabilities)->sum('total_debit_today') +
                                                                            collect($longTermLiabilities)->sum('total_debit_today')+
                                                                            collect($currentLiabilities)->sum('total_debit_today') + $todaysAccountPayableDebit + $todaysDueAccountPayableDebit + $todaysCreditPurchaseAP + $todaysVatPayableDebit;
                                                    $totalLiabilityCreditToday = collect($shortTermLiabilities)->sum('total_credit_today') +
                                                                            collect($longTermLiabilities)->sum('total_credit_today')+
                                                                            collect($currentLiabilities)->sum('total_credit_today') + $todaysPurchaseAPRcv + $totalTodaysAccountPayableCredit;
                                                    $closingLiabilityBalance = $totalLiabilitySum + $totalLiabilityDebitToday - $totalLiabilityCreditToday;                                             
                                                    
                                                @endphp
                                                <td style="text-align: right;">{{ number_format($totalLiabilitySum, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalLiabilityDebitToday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalLiabilityCreditToday, 2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($closingLiabilityBalance, 2) }}
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="6"></td>
                                            </tr>
                                            <!-- Liabilities End -->

                                            <!-- Equity Start -->
                                            <tr>
                                                <td><strong>Equity</strong></td>
                                                <td colspan="5"></td>
                                            </tr>

                                            <tr>
                                                <td colspan="6"></td>
                                            </tr>

                                            <tr>
                                                <td><strong>Equity Capitals</strong></td>
                                                <td colspan="5"></td>
                                            </tr>

                                            <!-- Equity Capitals  -->
                                            @foreach($equityCapitals as $equityCapital)
                                            <tr>
                                                <td></td>
                                                <td>{{ $equityCapital->account_name }}</td>
                                                <td style="text-align: right;">{{ number_format($equityCapital->total_previous_receive - $equityCapital->total_previous_payment, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($equityCapital->total_debit_today, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($equityCapital->total_credit_today, 2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($equityCapital->total_previous_receive - $equityCapital->total_previous_payment + $equityCapital->total_debit_today - $equityCapital->total_credit_today, 2) }}
                                                </td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <td><strong>Retained Earnings</strong></td>
                                                <td colspan="5"></td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td>Net Profit</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($netProfitTillYesterday, 2) }}
                                                </td>                           
                                                <td style="text-align: right;">
                                                    {{ number_format($netProfit, 2) }}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{-- {{ number_format($todayProfit, 2) }} --}}
                                                </td>
                                                <td style="text-align: right;">
                                                    {{ number_format($netProfit + $netProfitTillYesterday, 2) }}
                                                </td>
                                            </tr>

                                            <!-- Retained Earnings  -->
                                            @foreach($retainedEarnings as $retainedEarning)
                                            <tr>
                                                <td></td>
                                                <td>{{ $retainedEarning->account_name }}</td>
                                                <td style="text-align: right;">{{ number_format($retainedEarning->total_debit_yesterday - $retainedEarning->total_credit_yesterday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($retainedEarning->total_debit_today, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($retainedEarning->total_credit_today, 2) }}</td>
                                                <td style="text-align: right;">
                                                    {{ number_format($retainedEarning->total_debit_yesterday - $retainedEarning->total_credit_yesterday + $retainedEarning->total_debit_today - $retainedEarning->total_credit_today, 2) }}
                                                </td>
                                            </tr>
                                            @endforeach
                                            
                                            <tr>
                                                <td>
                                                    <strong>Total Equity</strong>
                                                </td>
                                                <td></td>
                                                @php
                                                    $totalEquitySum = collect($equityCapitals)->sum('total_previous_receive') - collect($equityCapitals)->sum('total_previous_payment') +
                                                    collect($retainedEarnings)->sum('total_debit_yesterday') - collect($retainedEarnings)->sum('total_credit_yesterday');
                                                                        
                                                    $totalEquityDebitToday = collect($equityCapitals)->sum('total_debit_today') +
                                                                            collect($retainedEarnings)->sum('total_debit_today');
                                                    $totalEquityCreditToday = collect($equityCapitals)->sum('total_credit_today') +
                                                                            collect($retainedEarnings)->sum('total_credit_today');
                                                    $closingEquityBalance = $totalEquitySum  + $netProfitTillYesterday + $totalEquityDebitToday - $totalEquityCreditToday + $netProfit;                                             
                                                @endphp
                                                <td style="text-align: right;">{{ number_format($totalEquitySum + $netProfitTillYesterday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalEquityDebitToday + $netProfit, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalEquityCreditToday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($closingEquityBalance, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                            </tr>
                                            <!-- Equity End -->

                                            <!-- Total Liability and Equity -->
                                            <tr>
                                                <td><strong>Total Liability and Equity</strong></td> 
                                                <td></td>
                                                @php

                                                
                                                    $totalCapitalClosingBalanceSum = collect($equityCapitals)->sum('total_previous_receive') - collect($equityCapitals)->sum('total_previous_payment') + collect($equityCapitals)->sum('total_debit_today') - collect($equityCapitals)->sum('total_credit_today');


                                                    $totalLiabilityEquitySum = $totalLiabilitySum + $totalEquitySum;
                                                    $toalLiabilityEquityDebitSum = $totalLiabilityDebitToday + $totalEquityDebitToday + $netProfit;
                                                    $totalLiabilityEquityCreditSum = $totalLiabilityCreditToday + $totalEquityCreditToday;
                                                    $totalLiabilityEquityClosingSum = $closingLiabilityBalance + $closingEquityBalance;


                                                @endphp
                                                <td style="text-align: right;">{{ number_format($totalLiabilityEquitySum + $netProfitTillYesterday, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($toalLiabilityEquityDebitSum, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalLiabilityEquityCreditSum, 2) }}</td>
                                                <td style="text-align: right;">{{ number_format($totalLiabilityEquityClosingSum, 2) }}</td>
                                            </tr>

                                        </tbody>

                                    </table>
                                </div>
                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection

@section('script')

@endsection
