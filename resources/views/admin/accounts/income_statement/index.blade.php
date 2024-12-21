@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Income Statement</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        
                        
                        <div class="row mb-3 d-none">
                            <form class="form-inline" role="form" method="POST" action="{{ route('admin.incomestatement') }}">
                                {{ csrf_field() }}

                                <div class="form-group mx-sm-3">
                                    <label class="sr-only">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ request()->input('start_date') }}">
                                </div>

                                <div class="form-group mx-sm-3">
                                    <label class="sr-only">End Date</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ request()->input('end_date') }}">
                                </div>
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                        </div>



                        <div class="row">
                            <div class="col-md-12">

                                <div class="text-center mb-4 company-name-container">
                                    @php
                                    $company = \App\Models\CompanyDetails::select('company_name')->first();
                                    @endphp
                                    <h2>{{ $company->company_name }}</h2>
                                    @if (isset(Auth::user()->branch))
                                        <h3>{{ Auth::user()->branch->name }} Branch</h3>
                                    @endif
                                    <h4>Income Statement</h4>
                                    <h5>From {{$start_date}} to {{$end_date}}</h5>
                                </div>
                        
                                <div class="table-responsive">
                                    <table id="cashIncomingTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Particulars</th>
                                                <th>Account Name</th>
                                                <th>Amount</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <strong>A</strong>
                                                </td>
                                                <td>
                                                    <strong>Sales Revenue</strong>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>Sales</td>
                                                <td>{{ number_format($salesSum, 2) }}</td>
                                                <td></td>
                                            </tr>  
                    
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>Sales Return</td>
                                                <td>{{ number_format($salesReturn, 2) }}</td>
                                                <td></td>
                                            </tr>  
                    
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>Discount</td>
                                                <td>{{ number_format($salesDiscount, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            
                                            <tr>
                                                <td></td>
                                                <td><strong>Net Sales</strong></td>
                                                <td colspan="2"></td>
                                                <td>
                                                    @php
                                                        $netSales = $salesSum - $salesReturn - $salesDiscount;
                                                    @endphp
                                                    {{ number_format($netSales, 2) }}
                                                </td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong>B</strong>
                                                </td>
                                                <td>
                                                    <strong>Cost of Goods Sold</strong>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>Opening Stock</td>
                                                <td>
                                                    @if(request('end_date'))
                                                        {{ number_format($totalOpeningStock, 2) }}
                                                    @else
                                                        0.00
                                                    @endif
                                                </td>
                                                <td></td>
                                            </tr>
                                            
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>Purchase</td>
                                                <td>{{ number_format($purchaseSum, 2) }}</td>
                                                <td></td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>Closing Stock</td>
                                                <td>{{ number_format($totalClosingStock, 2) }}</td>
                                                <td></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong>AB</strong>
                                                </td>
                                                <td>
                                                    <strong>Gross Profit</strong>
                                                </td>
                                                <td>
                                                    A - B
                                                </td>
                                                <td></td>
                                                <td>
                                                    @php
                                                        $grossProfit =  $netSales - $purchaseSum
                                                    @endphp
                                                    {{ number_format($grossProfit, 2) }}
                                                </td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                </td>
                                                <td>
                                                    <strong>Operating Income</strong>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>
                    
                                            @foreach ($operatingIncomes as $operatingIncome)
                                            <tr>
                                                <td colspan="2">
                                                
                                                </td>
                                                <td>
                                                    {{ $operatingIncome->chartOfAccount->account_name }}
                                                </td>
                                                <td colspan="2">{{ number_format($operatingIncome->total_amount, 2) }}</td>
                                            </tr>
                                            @endforeach
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong>C</strong>
                                                </td>
                                                <td>
                                                    <strong>Operating Expenses</strong>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>
                    
                                            @foreach($operatingExpenses as $operatingExpense)
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>{{ $operatingExpense->chartOfAccount->account_name }}</td>                           
                                                <td>{{ number_format($operatingExpense->total_amount, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            @endforeach
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong></strong>
                                                </td>
                                                <td>
                                                    <strong>OverHead Expenses</strong>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>
                    
                                            @foreach($overHeadExpenses as $overHeadExpense)
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>{{ $overHeadExpense->chartOfAccount->account_name }}</td>                           
                                                <td>{{ number_format($overHeadExpense->total_amount, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            @endforeach
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong>D</strong>
                                                </td>
                                                <td>
                                                    <strong>Administrative Expenses</strong>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>Depreciation Expense</td>                      
                                                <td>{{ number_format($fixedAssetDepriciation, 2) }}</td>
                                                <td></td>
                                            </tr>
                    
                                            @foreach($administrativeExpenses as $administrativeExpense)
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>{{ $administrativeExpense->chartOfAccount->account_name }}</td>                      
                                                <td>{{ number_format($administrativeExpense->total_amount, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            @endforeach
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong>E</strong>
                                                </td>
                                                <td>
                                                    <strong>Profit before tax</strong>
                                                </td>
                                                <td>AB - C -D</td>
                                                <td></td>
                                                <td>
                                                    @php
                                                        $totalOperatingExpenses = $operatingExpenses->sum('total_amount');
                                                        $totalAdministrativeExpenses = $administrativeExpenses->sum('total_amount');
                                                        $totalOverHeadExpenses = $overHeadExpenses->sum('total_amount');
                                                        $profitBeforeTax =  $grossProfit +$operatingIncomeSums - $totalOperatingExpenses +$purchaseReturn - $totalAdministrativeExpenses - $operatingIncomeRefundSum - $totalOverHeadExpenses - $fixedAssetDepriciation
                                                    @endphp
                                                    {{ number_format($profitBeforeTax, 2) }}
                                                </td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong>F</strong>
                                                </td>
                                                <td>
                                                    <strong>Tax and VAT</strong>
                                                </td>
                                                <td colspan="2"></td>
                                                <td>
                                                    {{ number_format($taxAndVat, 2) }}
                                                </td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong>G</strong>
                                                </td>
                                                <td>
                                                    <strong>Net Profit</strong>
                                                </td>
                                                <td>E - F</td>
                                                <td></td>
                                                <td>
                                                    @php
                                                        $netProfit =  $profitBeforeTax - $taxAndVat
                                                    @endphp
                                                    {{ number_format($netProfit, 2) }}
                                                </td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong>H</strong>
                                                </td>
                                                <td>
                                                    <strong>Dvidend</strong>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>
                    
                                            <tr>
                                                <td colspan="5"></td>
                                            </tr>
                    
                                            <tr>
                                                <td>
                                                    <strong>I</strong>
                                                </td>
                                                <td>
                                                    <!-- <strong>Net Profit transferred to BS as Retained Earnings</strong> -->
                                                    <strong>Net Profit</strong>
                                                </td>
                                                <td>
                                                    G - H
                                                </td>
                                                <td colspan="2"></td>
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
