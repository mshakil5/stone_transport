@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Flash Sells</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Flash Sell Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($flashSells as $key => $flashSell)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $flashSell->flash_sell_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($flashSell->start_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($flashSell->end_date)->format('d-m-Y') }}</td>

                                    <td>
                                        <a class="btn btn-sm btn-info view-flash-sell-btn" data-flash-sell-id="{{ $flashSell->id }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('flash-sell.edit', $flashSell->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-flash-sell-btn" data-flash-sell-id="{{ $flashSell->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Modal -->
<div class="modal fade" id="viewFlashSellModal" tabindex="-1" aria-labelledby="viewFlashSellModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewFlashSellModalLabel">View Flash Sell Details</h5>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col"><strong>Flash Sell Name:</strong> <span id="flashSellName"></span></div>
                    <div class="col"><strong>Flash Sell Title:</strong> <span id="flashSellTitle"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Start Date:</strong> <span id="flashSellStartDate"></span></div>
                    <div class="col"><strong>End Date:</strong> <span id="flashSellEndDate"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Description:</strong> <span id="flashSellDescription"></span></div>
                </div>

                <div class="mb-3">
                    <h5>Flash Sell Details</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Old Price</th>
                                <th>Flash Sell Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="flashSellDetailsTableBody">
                           
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeModalBtn">Close</button>
            </div>
        </div>
    </div>
</div>

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
    });
</script>

<script>
    $(document).ready(function() {
        function showViewFlashSellModal(flashSellId) {
            $.ajax({
                url: '/admin/flash-sell/' + flashSellId + '/details',
                type: 'GET',
                success: function(response) {

                    $('#flashSellName').text(response.flash_sell_name);
                    $('#flashSellTitle').text(response.flash_sell_title);
                    var formattedStartDate = moment(response.start_date).format('DD-MM-YYYY');
                    var formattedEndDate = moment(response.end_date).format('DD-MM-YYYY');
                    $('#flashSellStartDate').text(formattedStartDate);
                    $('#flashSellEndDate').text(formattedEndDate);
                    $('#flashSellDescription').text(response.flash_sell_description);

                    if (response.flash_sell_details && response.flash_sell_details.length > 0) {
                        let flashSellDetailsHtml = '';
                        response.flash_sell_details.forEach(function(detail) {
                            flashSellDetailsHtml += `
                                <tr>
                                    <td>${detail.product.name}</td>
                                    <td>${detail.old_price}</td>
                                    <td>${detail.flash_sell_price}</td>
                                    <td>${detail.quantity}</td>
                                </tr>`;
                        });

                        $('#flashSellDetailsTableBody').html(flashSellDetailsHtml);
                    } else {
                        $('#flashSellDetailsTableBody').html('<tr><td colspan="4">No details found.</td></tr>');
                    }

                    $('#viewFlashSellModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Failed to fetch flash sell details.');
                }
            });
        }

        $(document).on('click', '.view-flash-sell-btn', function() {
            const flashSellId = $(this).data('flash-sell-id');
            showViewFlashSellModal(flashSellId);
        });

        $('#closeModalBtn').click(function() {
            $('#viewFlashSellModal').modal('hide');
        });
    });
</script>

<script>
    function deleteFlashSell(flashSellId) {
        $.ajax({
            url: '/admin/flash-sell/' + flashSellId,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                swal({
                    text: "Deleted",
                    icon: "success",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    $(document).ready(function() {
        $(document).on('click', '.delete-flash-sell-btn', function() {
            const flashSellId = $(this).data('flash-sell-id');
            if (confirm('Are you sure you want to delete this flash sell?')) {
                deleteFlashSell(flashSellId);
            }
        });
    });
</script>

@endsection
