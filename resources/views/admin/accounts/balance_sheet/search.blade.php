@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Balance Sheet</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <form class="" role="form" method="POST" action="{{ route('admin.balancesheet.report') }}">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-3">
                                    <label>Start Date</label>
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control" name="start_date" value="{{ request()->input('start_date') }}">
                                    @if ($errors->has('start_date'))
                                        <span class="text-danger">{{ $errors->first('start_date') }}</span>
                                    @endif

                                </div>
                                <div class="col-3">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </form>

                        


                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection

@section('script')

@endsection
