@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="col-2 my-2">
        <a href="{{ route('allproduct') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Reviews for {{ $product->name }}</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>User Name</th>
                                    <th>Review Title</th>
                                    <th>Star Rating</th>
                                    <th>Review Description</th>
                                    <th>Approval Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product->reviews as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $data->user->name }}</td>
                                        <td>{{ $data->title }}</td>
                                        <td>{{ $data->rating }}/5</td>
                                        <td>{{ $data->description }}</td>
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input toggle-approval" id="customSwitchApproval{{ $data->id }}" data-id="{{ $data->id }}" {{ $data->is_approved == 1 ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="customSwitchApproval{{ $data->id }}"></label>
                                            </div>
                                        </td>
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
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        // AJAX for changing approval status
        $('.toggle-approval').change(function() {
            var review_id = $(this).data('id');
            var is_approved = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '/admin/review-status',
                method: "POST",
                data: {
                    review_id: review_id,
                    is_approved: is_approved,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: "Review status updated successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    } else {
                        swal({
                            text: "An error occurred. Please try again.",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endsection