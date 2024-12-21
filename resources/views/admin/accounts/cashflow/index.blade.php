@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Cash flow</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <div class="row">
                            <div class="row  justify-content-md-center mb-3">
                                <form class="form-inline" role="form" method="POST" action="{{ route('cashflow') }}">
                                    {{ csrf_field() }}
                                    
                                    <div class="form-group mx-md-3">
                                        <label class="sr-only">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ request()->input('start_date') }}">
                                    </div>
                                    
                                    <div class="form-group mx-md-3">
                                        <label class="sr-only">End Date</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ request()->input('end_date') }}">
                                    </div>

                                    <button type="submit" class="btn btn-primary">Search</button>
                                </form>
                            </div>
                            <div class="col-md-12">
                                <div class="text-center mb-4 company-name-container">
                                    @php
                                    $company = \App\Models\CompanyDetails::select('company_name')->first();
                                    @endphp
                                    <h2>{{ $company->company_name }}</h2>
                                    <h4>Cash flow</h4>
                                </div>
                        
                                <div class="table-responsive">
                                    
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Particulars</th>
                                                <th>Account Name</th>
                                                <th>Amount</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <strong>(1) Opening Balance</strong>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td><strong>00000.00</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    <strong>Cash Incoming</strong>
                                                </td>
                                            </tr>
                                            @foreach($incomes as $income)
                                                <tr>
                                                    <td></td>
                                                    <td>{{ $income->chartOfAccount->account_name ?? 'Sales' }}</td>        
                                                    <td>{{ number_format($income->at_amount, 2) }}</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td></td>
                                                <td>Asset Sold</td>        
                                                <td>{{ number_format($assetSold, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Liabilities Received</td>        
                                                <td>{{ number_format($liabilityReceived, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Equity Received</td>        
                                                <td>{{ number_format($equityReceived, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>
                                            <tr>
                                                @php
                                                    $totalCashIncoming = $incomes->sum('at_amount') + $assetSold + $liabilityReceived + $equityReceived;
                                                @endphp
                                                <td colspan="3"><strong>(2) Total Cash Incoming</strong></td>
                                                <td><strong>{{ number_format($totalCashIncoming, 2) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    <strong>Cash Outgoing</strong>
                                                </td>
                                            </tr>
                                            @foreach($expenses as $expense)
                                                <tr>
                                                    <td></td>
                                                    <td>{{ $expense->chartOfAccount->account_name ?? 'Purchase'}}</td>        
                                                    <td>{{ number_format($expense->at_amount, 2) }}</td>
                                                    <td></td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td></td>
                                                <td>Asset Purchase</td>        
                                                <td>{{ number_format($assetPurchase, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Liabilities Payment</td>        
                                                <td>{{ number_format($liabilityPayment, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Equity Payment</td>        
                                                <td>{{ number_format($equityPayment, 2) }}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                @php
                                                    $totalCashOutGoing = $expenses->sum('at_amount') + $assetPurchase + $liabilityPayment + $equityPayment;
                                                @endphp
                                                <td colspan="3"><strong>(3) Total Cash Outgoing</strong></td>
                                                <td><strong>{{ number_format($totalCashOutGoing, 2) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                            </tr>

                                            <tr>
                                                @php
                                                    $closingBalance = $totalCashIncoming - $totalCashOutGoing;
                                                @endphp
                                                <td colspan="3"><strong>Closing Balance (3-4)</strong></td>
                                                <td><strong>{{ number_format($closingBalance, 2) }}</strong></td>
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
