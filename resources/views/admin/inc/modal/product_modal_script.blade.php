<!-- Create Modals Start-->
<script>
    $(document).ready(function () {
        $('#saveCategoryBtn').click(function (e) {
            e.preventDefault();
            
            let categoryName = $('#category_name').val();

            if(categoryName === '') {
                swal({
                    text: "Category name is required !",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                })
                return;
            }

            $.ajax({
                url: '{{ route('category.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": categoryName
                },
                success: function (response) {
                    $('#addCategoryModal').modal('hide');
                    $('#category_name').val('');
                    $('#category').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Category added successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    })
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    })
                    // console.error(xhr.responseText);
                }
            });
        });

        $('#saveSubCategoryBtn').click(function (e) {
            e.preventDefault();
            
            let categoryId = $('#category_id').val();
            let subcategoryName = $('#subcategory_name').val();

            if (categoryId === '') {
                swal({
                    text: "Please select a category!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            if (subcategoryName === '') {
                swal({
                    text: "Subcategory name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('subcategory.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "category_id": categoryId,
                    "name": subcategoryName
                },
                success: function (response) {
                    $('#addSubCategoryModal').modal('hide');
                    $('#subcategory_name').val('');
                    $('#category_id').val('');

                    $('#subcategory').append(`<option class="subcategory-option category-${response.category_id}" value="${response.id}" selected>${response.name}</option>`);

                    swal({
                        text: "Subcategory added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

        $('#saveBrandBtn').click(function (e) {
            e.preventDefault();
            
            let brandName = $('#brand_name').val();

            if(brandName === '') {
                swal({
                    text: "Brand name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('brand.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": brandName
                },
                success: function (response) {
                    $('#addBrandModal').modal('hide');
                    $('#brand_name').val('');
                    $('#brand').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Brand added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

        $('#saveModelBtn').click(function (e) {
            e.preventDefault();
            
            let modelName = $('#model_name').val();

            if(modelName === '') {
                swal({
                    text: "Model name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('product-model.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": modelName
                },
                success: function (response) {
                    $('#addModelModal').modal('hide');
                    $('#model_name').val('');
                    $('#model').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Model added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

        $('#saveUnitBtn').click(function (e) {
            e.preventDefault();
            
            let unitName = $('#unit_name').val();

            if(unitName === '') {
                swal({
                    text: "Unit name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('unit.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": unitName
                },
                success: function (response) {
                    $('#addUnitModal').modal('hide');
                    $('#unit_name').val('');
                    $('#unit').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Unit added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

        $('#saveGroupBtn').click(function (e) {
            e.preventDefault();
            
            let groupName = $('#group_name').val();

            if(groupName === '') {
                swal({
                    text: "Group name is required!",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return;
            }

            $.ajax({
                url: '{{ route('group.store') }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": groupName
                },
                success: function (response) {
                    $('#addGroupModal').modal('hide');
                    $('#group_name').val('');
                    $('#group').append(`<option value="${response.id}" selected>${response.name}</option>`);
                    swal({
                        text: "Group added successfully!",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (xhr) {
                    swal({
                        text: xhr.responseJSON.message,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                }
            });
        });

        $('#saveSizeBtn').click(function() {

            let size = $('#size_name').val();
            let price = $('#size_price').val();

            $.ajax({
                url: '{{ route('size.store') }}',
                type: 'POST',
                data: {
                    size: size,
                    price: price,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: "Size added successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            $('#size_ids').append(`<option value="${response.data.id}">${response.data.size}</option>`);
                            
                            $('#addSizeModal').modal('hide');
                            $('#newSizeForm')[0].reset();
                        });
                    } else {
                        swal({
                            text: "Failed to add size",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--error"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Error adding size. Please try again.";
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    
                    swal({
                        text: errorMessage,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    });
                }
            });
        });

        $('#saveColorBtn').click(function() {
            let colorName = $('#color_name').val();
            let color_code = $('#color_code').val();
            let price = $('#color_price').val();

            $.ajax({
                url: '{{ route('color.store') }}',
                type: 'POST',
                data: {
                    color_name: colorName,
                    color_code: color_code,
                    price: price,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            text: "Color added successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            $('#color_id').append(`<option value="${response.data.id}" style="background-color: ${response.data.color_code};">${response.data.color} (${response.data.color_code})</option>`);
                            $('#addColorModal').modal('hide');
                            $('#newColorForm')[0].reset();
                        });
                    } else {
                        swal({
                            text: "Failed to add color",
                            icon: "error",
                            button: {
                                text: "OK",
                                className: "swal-button--error"
                            }
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Error adding color. Please try again.";
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("\n");
                    }
                    
                    swal({
                        text: errorMessage,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--error"
                        }
                    });
                }
            });
        });
    });
</script>
<!-- Create Modals End-->