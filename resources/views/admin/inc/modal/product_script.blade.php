<!-- Category Wise Subcategory Start -->
<script>
    $(document).ready(function() {
        $('#category').change(function() {
            var categoryId = $(this).val();
            if (categoryId) {
                $('#subcategory').val('').find('option').hide();
                $('.category-' + categoryId).show();
            } else {
                $('#subcategory').val('').find('option').hide();
                $('#subcategory').find('.subcategory-option').show();
            }
        });
    });
</script>
<!-- Category Wise Subcategory End -->

<!-- Data Table and Select2 -->
<script>
    $(function() {
        $('.select2').select2({
            placeholder: "Select sizes",
            width: '100%'
        });

        $('#long_description, #short_description').summernote({
            height: 100,
        });

        $("#feature-img").change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>

<!-- Dynamic Row Script -->
<script>
    $(document).ready(function() {
        $(document).on('click', '.add-row', function() {
            let newRow = `
            <div class="form-row dynamic-row">
                <div class="form-group col-md-5">
                    <label for="color_id">Select Color</label>
                    <select class="form-control" name="color_id[]" id="color_id">
                        <option value="">Choose Color</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}" style="background-color: {{ $color->color_code }};">
                                {{ $color->color }} ({{ $color->color_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label for="image">Select Image</label>
                    <input type="file" class="form-control" name="image[]" accept="image/*">
                </div>
                <div class="form-group col-md-1">
                    <label>Action</label>
                    <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                </div>
            </div>`;

            $('#dynamic-rows').append(newRow);
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('.dynamic-row').remove();
        });
    });
</script>

<!-- Create Product Start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#productaddBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            var formData = new FormData($('#productCreateThisForm')[0]);

            $.ajax({
                url: '{{ route("store.product") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    $('#product_id').append(`<option value="${response.product.id}">${response.product.name}</option>`);
                    $('#newProductModal').modal('hide');
                    $('#productCreateThisForm')[0].reset();
                    swal({
                        text: "Created successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    })
                    console.error(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#productaddBtn').attr('disabled', false);
                }
            });
        });

        $('#product_code').on('keyup', function() {
            let productCode = $(this).val().trim();

            if (productCode.length >= 2) {
                $.ajax({
                    url: "{{ route('check.product.code') }}",
                    method: "GET",
                    data: { product_code: productCode },
                    success: function(response) {
                        if (response.exists) {
                            $('#productCodeError').text('This product code is already in use.');
                            $('#productaddBtn').attr('disabled', true);
                        } else {
                            $('#productCodeError').text('');
                            $('#productaddBtn').attr('disabled', false);
                        }
                    }
                });
            } else {
                $('#productCodeError').text('');
                $('#productaddBtn').attr('disabled', true);
            }
        });
    });
</script>
<!-- Create Product End -->