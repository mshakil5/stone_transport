@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Account Names</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <table id="chartOfAccountsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Assets</th>
                                    <th>Expenses</th>
                                    <th>Income</th>
                                    <th>Liabilities</th>
                                    <th>Equity</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td>
                                            @foreach($chartOfAccounts as $asset)
                                                @if($asset->account_head == 'Assets')   
                                                    <a href="{{ url('/admin/ledger/asset-details/' . $asset->id) }}" class="btn btn-block btn-default btn-xs">{{ $asset->account_name }}</a>
                                                @endif  
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($chartOfAccounts as $expense)
                                                @if($expense->account_head == 'Expenses')   
                                                    <a href="{{ url('/admin/ledger/expense-details/' . $expense->id) }}" class="btn btn-block btn-default btn-xs">{{ $expense->account_name }}</a>
                                                @endif  
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($chartOfAccounts as $income)
                                                @if($income->account_head == 'Income')   
                                                    <a href="{{ url('/admin/ledger/income-details/' . $income->id) }}" class="btn btn-block btn-default btn-xs">{{ $income->account_name }}</a>
                                                @endif  
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($chartOfAccounts as $liability)
                                                @if($liability->account_head == 'Liabilities')   
                                                    <a href="{{ url('/admin/ledger/liability-details/' . $liability->id) }}" class="btn btn-block btn-default btn-xs">{{ $liability->account_name }}</a>
                                                @endif  
                                            @endforeach
                                            <hr>
                                            @foreach ($suppliers as $supplier)
                                            @if ($supplier->supplier_transaction_sum_at_amount-$supplier->total_decreament > 0)
                                                
                                            <a href="{{ url('/admin/supplier/transactions/' . $supplier->id) }}" class="btn btn-block btn-default btn-xs">{{ $supplier->name }} (Suppliers)</a>
                                            @endif
                                            @endforeach

                                        </td>
                                        <td>
                                            @foreach($chartOfAccounts as $equity)
                                                @if($equity->account_head == 'Equity')   
                                                    <a href="{{ url('/admin/ledger/equity-details/' . $equity->id) }}" class="btn btn-block btn-default btn-xs">{{ $equity->account_name }}</a>
                                                @endif  
                                            @endforeach
                                        </td>
                                    </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Account Name</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <table id="chartOfAccountsTable" class="table table-striped table-bordered">
                            
                            <tbody>
                                    <tr>
                                        <td> 
                                            <p class="btn btn-block btn-default btn-xs"> <a href="{{route('ledger.purchase')}}">Purchase</a></p>
                                        </td>
                                        <td>
                                            <p class="btn btn-block btn-default btn-xs"> <a href="{{route('ledger.sales')}}">Sales</a> </p>
                                        </td>
                                    </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection

@section('script')

@endsection
