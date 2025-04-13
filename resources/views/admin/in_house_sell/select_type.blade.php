@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Select Sale Type</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('inhousesellPage') }}">
                            @csrf
                            <div class="form-group">
                                <label>How do you want to sell?</label>
                                <select name="sale_type" class="form-control" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="1">By Road</option>
                                    <option value="2">By Lighter Vessel</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Continue</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection