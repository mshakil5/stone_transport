@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Special Offers</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Offer</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($specialOffers as $key => $specialOffer)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $specialOffer->offer_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($specialOffer->start_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($specialOffer->end_date)->format('d-m-Y') }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-info view-offer-btn" data-offer-id="{{ $specialOffer->id }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('special-offer.edit', $specialOffer->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger delete-special-offer-btn" data-special-sell-id="{{ $specialOffer->id }}">
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
<div class="modal fade" id="viewSpecialOfferModal" tabindex="-1" aria-labelledby="viewSpecialOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSpecialOfferModalLabel">View Special Offer Details</h5>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col"><strong>Offer Name:</strong> <span id="offerName"></span></div>
                    <div class="col"><strong>Offer Title:</strong> <span id="offerTitle"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Start Date:</strong> <span id="offerStartDate"></span></div>
                    <div class="col"><strong>End Date:</strong> <span id="offerEndDate"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col"><strong>Description:</strong> <span id="offerDescription"></span></div>
                </div>

                <div class="mb-3">
                    <h5>Offer Details</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Old Price</th>
                                <th>Offer Price</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="offerDetailsTableBody">
                           
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
        function showViewSpecialOfferModal(offerId) {
            $.ajax({
                url: '/admin/special-offer/' + offerId + '/details',
                type: 'GET',
                success: function(response) {

                    $('#offerName').text(response.offer_name);
                    $('#offerTitle').text(response.offer_title);
                    var formattedStartDate = moment(response.start_date).format('DD-MM-YYYY');
                    var formattedEndDate = moment(response.end_date).format('DD-MM-YYYY');
                    $('#offerStartDate').text(formattedStartDate);
                    $('#offerEndDate').text(formattedEndDate);
                    $('#offerDescription').text(response.offer_description);

                    if (response.special_offer_details && response.special_offer_details.length > 0) {
                        let offerDetailsHtml = '';
                        response.special_offer_details.forEach(function(detail) {
                            offerDetailsHtml += `
                                <tr>
                                    <td>${detail.product.name}</td>
                                    <td>${detail.old_price}</td>
                                    <td>${detail.offer_price}</td>
                                    <td>${detail.quantity}</td>
                                </tr>`;
                        });

                        $('#offerDetailsTableBody').html(offerDetailsHtml);
                    } else {
                        $('#offerDetailsTableBody').html('<tr><td colspan="4">No details found.</td></tr>');
                    }

                    $('#viewSpecialOfferModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Failed to fetch special offer details.');
                }
            });
        }

        $(document).on('click', '.view-offer-btn', function() {
            const offerId = $(this).data('offer-id');
            showViewSpecialOfferModal(offerId);
        });

        $('#closeModalBtn').click(function() {
            $('#viewSpecialOfferModal').modal('hide');
        });
    });
</script>

<script>
    function deleteSpecialOffer(specialOfferId) {
        $.ajax({
            url: '/admin/special-offer/' + specialOfferId,
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
        $(document).on('click', '.delete-special-offer-btn', function() {
            const specialOfferId = $(this).data('special-sell-id');
            if (confirm('Are you sure you want to delete this flash sell?')) {
                deleteSpecialOffer(specialOfferId);
            }
        });
    });
</script>

@endsection