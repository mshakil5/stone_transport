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
                        
                                
                                <table id="daybookTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
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
                                        $balance = $totalAmount;
                                    @endphp

                                    @foreach($bankbooks as $index => $bankbook)
                                            <tr>
                                                <td> {{ $index + 1 }} </td>
                                                <td>{{ \Carbon\Carbon::parse($bankbook->date)->format('d-m-Y') }}</td>
                                                <td>
                                                    {{ $bankbook->chart_of_account_id ? $bankbook->chartOfAccount->account_name : $bankbook->description }}
                                                </td>
                                                <td>{{ $bankbook->ref }}</td>
                                                @if(in_array($bankbook->transaction_type, ['Current', 'Received', 'Sold', 'Advance']))
                                                <td>{{ $bankbook->at_amount }}</td>
                                                <td></td>
                                                <td>{{ $balance }}</td>
                                                @php
                                                    $balance = $balance - $bankbook->at_amount;
                                                @endphp
                                                @elseif(in_array($bankbook->transaction_type, ['Purchase', 'Payment', 'Prepaid']))
                                                <td></td>
                                                <td>{{ $bankbook->at_amount }}</td>
                                                <td>{{ $balance }}</td>
                                                @php
                                                    $balance = $balance + $bankbook->at_amount;
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
        </div>
    </div>
</section>



@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#daybookTable').DataTable({
            pageLength: 100,
        });
    });
</script>
@endsection
