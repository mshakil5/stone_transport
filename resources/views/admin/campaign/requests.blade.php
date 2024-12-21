@extends('admin.layouts.admin')

@section('content')

<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add new campaign request</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <div class="row">

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="campaign_id">Choose Campaign <span style="color: red;">*</span></label>
                                        <select class="form-control" id="campaign_id" name="campaign_id">
                                            <option value="">Select...</option>
                                            @foreach($campaigns as $campaign)
                                                <option value="{{ $campaign->id }}">{{ $campaign->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="product_id">Choose Product <span style="color: red;">*</span></label>
                                        <select class="form-control" id="product_id" name="product_id">
                                            <option value="">Select...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="quantity">Quantity <span style="color: red;">*</span></label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity" min="1">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="campaign_price">Unit Price <span style="color: red;">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="campaign_price" name="campaign_price" placeholder="Enter price">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="product_size">Size <span style="color: red;">*</span></label>
                                        <select class="form-control" id="product_size" name="product_size">
                                            <option value="">Select...</option>
                                            @foreach($sizes as $size)
                                                <option value="{{ $size->size }}">{{ $size->size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="product_color">Color <span style="color: red;">*</span></label>
                                        <select class="form-control" id="product_color" name="product_color">
                                            <option value="">Select...</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color->color }}">{{ $color->color }}</option>
                                            @endforeach                                    
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <label for="addProductBtn">Action</label>
                                    <div class="col-auto d-flex align-items-end">
                                        <button type="button" id="addProductBtn" class="btn btn-secondary">Add</button>
                                     </div>
                                </div>
                                <div class="col-sm-12 mt-3">
                                    <h2>Product List:</h2>
                                    <table class="table table-bordered" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" id="addBtn" class="btn btn-secondary" value="Create"><i class="fas fa-plus"></i> Create</button>
                                <button type="button" id="FormCloseBtn" class="btn btn-default">Cancel</button>  
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">All Data</h3>
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bcampaignRequested table-striped">
                            <thead>
                                <tr>
                                    <th>Campaign Title</th>
                                    <th>Supplier</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $campaignRequest)
                                <tr>
                                    <td>{{ $campaignRequest->campaign->title }}</td>
                                    <td>
                                        @if($campaignRequest->supplier)
                                            {{ $campaignRequest->supplier->name }}
                                        @else
                                            Admin
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($campaignRequest->campaign->start_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($campaignRequest->campaign->end_date)->format('d-m-Y') }}</td>
                                    <td>
                                        <select class="status-select form-control" data-campaign-request-id="{{ $campaignRequest->id }}">
                                            <option value="0" {{ $campaignRequest->status == 0 ? 'selected' : '' }}>Pending</option>
                                            <option value="1" {{ $campaignRequest->status == 1 ? 'selected' : '' }}>Approved</option>
                                            <option value="2" {{ $campaignRequest->status == 2 ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm view-details" data-id="{{ $campaignRequest->id }}">View Details</button>
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

<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Campaign Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="campaign-details">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {

        $("#addThisFormContainer").hide();

        $("#newBtn").click(function(){
            clearForm();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);
        });

        $("#FormCloseBtn").click(function(){
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearForm();
            $('.ermsg').empty();
        });

        function clearForm(){
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create').text('Create');
            $("#cardTitle").text('Add new data');
            $('#preview-image').attr('src', '#');
            $('#preview-image1').attr('src', '#');
            $('#banner_image').val('');
            $('#small_image').val('');
            $("#long_description").summernote('code', '');
            $("#short_description").summernote('code', '');
        }


        $('#addProductBtn').click(function() {
            var selectedSize = $('#product_size').val() || 'M';
            var selectedColor = $('#product_color').val() || 'Black';
            var selectedProduct = $('#product_id option:selected');
            var productId = selectedProduct.val();
            var productName = selectedProduct.data('name');
            var quantity = $('#quantity').val();
            var campaign_price = $('#campaign_price').val();

            if (isNaN(quantity) || quantity <= 0) {
                alert('Quantity must be a positive number.');
                return;
            }

            var productExists = false;
                $('#productTable tbody tr').each(function() {
                    var existingProductId = $(this).data('id');
                    if (existingProductId == productId) {
                        productExists = true;
                        return false;
                    }
                });

                if (productExists) {
                    alert('This product is already in the table.');
                    return;
                }

            if (productId && quantity && campaign_price) {
                var productRow = `<tr data-id="${productId}">
                                    <td>${productName}</td>
                                    <td>${quantity}</td>
                                    <td>${selectedSize}</td>
                                    <td>${selectedColor}</td>
                                    <td>${campaign_price}</td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-product">Remove</button></td>
                                </tr>`;
                $('#productTable tbody').append(productRow);
                $('#quantity').val('');
                $('#campaign_price').val('');
                $('#product_size').val('');
                $('#product_color').val('');
            }
        });

        $(document).on('click', '.remove-product', function() {
            $(this).closest('tr').remove();
        });

        $('#addBtn').on('click', function(e) {
            e.preventDefault();
            var formData = {};
            var selectedProducts = [];

            formData.campaign_id = $('#campaign_id').val();

            $('#productTable tbody tr').each(function() {
                var selectedRow = $(this).closest('tr');
                var product_id = $(this).data('id');
                var quantity = selectedRow.find('td:eq(1)').text();
                var product_size = selectedRow.find('td:eq(2)').text();
                var product_color = selectedRow.find('td:eq(3)').text();
                var campaign_price = selectedRow.find('td:eq(4)').text();

                selectedProducts.push({
                    product_id: product_id,
                    quantity: quantity,
                    campaign_price: campaign_price,
                    product_size: product_size,
                    product_color: product_color,
                });
            });

            var finalData = { ...formData, products: selectedProducts };
            // console.log(finalData);

            $.ajax({
                url: '{{ route("admin.campaign.request.store") }}',
                method: 'POST',
                data: finalData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Created successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        swal({
                            title: "Validation Error",
                            text: errorMessage,
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    }
                }
            });

        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var price = selectedOption.data('price');
            var maxQuantity = selectedOption.data('quantity');

            if (price !== undefined) {
                $('#campaign_price').val(price);
            } else {
                $('#campaign_price').val('');
            }

            if (maxQuantity !== undefined) {
                $('#quantity').attr('max', maxQuantity).attr('placeholder', 'Enter quantity (Max: ' + maxQuantity + ')').val('');
            } else {
                $('#quantity').removeAttr('max').attr('placeholder', 'Enter quantity').val('');
            }
        });

        $('#quantity').on('input', function() {
            var max = $(this).attr('max');
            var value = $(this).val();

            if (parseInt(value) > parseInt(max)) {
                $(this).val(max);
            }
        });
    });
</script>

<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
    $(document).ready(function() {
        $('#product_id, #campaign_id').select2({
            placeholder: "Select product...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(document).on('change', '.status-select', function() {
            var status = $(this).val();
            var campaignRequestId = $(this).data('campaign-request-id');
            var row = $(this).closest('tr');

            $.ajax({
                url: '{{ route('campaign.request.status.update') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    campaign_request_id: campaignRequestId,
                    status: status,
                },
                success: function(response) {
                    swal({
                        text: "Updated successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.view-details').on('click', function() {
            var campaignRequestId = $(this).data('id');

            $.ajax({
                url: '/admin/campaign-request/' + campaignRequestId,
                method: 'GET',
                success: function(response) {
                    var details = `
                        <h5><strong>Campaign Title</strong>: ${response.campaign.title}</h5>
                        <p><strong>Start Date:</strong> ${moment(response.campaign.start_date).format('DD-MM-YYYY')}  ||  
                        <strong>End Date:</strong> ${moment(response.campaign.end_date).format('DD-MM-YYYY')}</p>
                        <h6><strong>Products:</strong></h6>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Size</th>
                                    <th>Color</th>
                                    <th>Campaign Price</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    response.products.forEach(function(product) {
                        details += `
                            <tr>
                                <td>${product.product.name}</td>
                                <td>${product.quantity}</td>
                                <td>${product.product_size}</td>
                                <td>${product.product_color}</td>
                                <td>${product.campaign_price}</td>
                            </tr>
                        `;
                    });

                    details += `</tbody></table>`;

                    $('#campaign-details').html(details);
                    $('#detailsModal').modal('show');
                },

                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

@endsection