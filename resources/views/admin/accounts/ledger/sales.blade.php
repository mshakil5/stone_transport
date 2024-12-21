@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a></div>
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                                <h4>Sales Ledger</h4>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        
                        <div class="text-center mb-4 company-name-container">
                            @php
                            $company = \App\Models\CompanyDetails::select('company_name')->first();
                            @endphp
                            <h2>{{ $company->company_name }}</h2>
                        
                            <h4>Sales Ledger</h4>
                        </div>

                        
                    <table id="dataTransactionsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Ref</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>                                
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $balance = $totalBalance;
                            @endphp

                            @foreach($data as $index => $data)
                                <tr>
                                    <td>{{ $data->tran_id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                    <td>{{ $data->description }} {{ $data->table_type }}</td>
                                    <td>{{ $data->ref }}</td>
                                    @if(in_array($data->transaction_type, ['Current']))
                                    <td>{{ $data->at_amount }}</td>
                                    <td></td>
                                    <td>{{ $balance }}</td>
                                    @php
                                        $balance = $balance + $data->at_amount;
                                    @endphp
                                    @elseif(in_array($data->payment_type, ['Return']))
                                    <td></td>
                                    <td>{{ $data->at_amount }}</td>
                                    <td>{{ $balance }}</td>
                                    @php
                                        $balance = $balance - $data->at_amount;
                                    @endphp
                                    @endif
                                </tr>
                            @endforeach
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
