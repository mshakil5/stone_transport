@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-7">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Income Statement</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        
                        
                        <div class="row mb-3">
                            <form class="form-inline" role="form" method="POST" action="{{ route('admin.incomestatement.report') }}">
                                {{ csrf_field() }}

                                <div class="form-group mx-sm-3">
                                    <label class="">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ request()->input('start_date') }}">
                                </div>

                                <div class="form-group mx-sm-3">
                                    <label class="">End Date</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ request()->input('end_date') }}">
                                </div>
                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
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
