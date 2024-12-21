@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Share Holders</h3>
                        <button class="btn btn-lg btn-success pull-right" data-toggle="modal" data-target="#chartModal" data-purpose="0">
                            + Add New Share Holder
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>
                            @component('components.table')
                                @slot('tableID')
                                    chartTBL
                                @endslot
                                @slot('head')
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Company Name</th>
                                    <th>Tax Number</th>
                                    <th>Tin Number</th>
                                    <th>Phone</th>
                                    <th>Ledger</th>
                                    <th><i class=""></i> Action</th>
                                @endslot
                            @endcomponent
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="chartModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Share Holder</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
            </div>
            <form class="form-horizontal" id="customer-form">
            <div id="alert-container1"></div>
                <div class="modal-body">
                    {{csrf_field()}}
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Name</label>
                                <input type="text" name="name" class="form-control " id="name" placeholder="John Doe">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_name" class="control-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control " id="company_name" placeholder="Company Name">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="control-label">phone</label>
                                <input type="number" name="phone" class="form-control " id="phone">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tax_number" class="control-label">Tax Number</label>
                                <input type="number" name="tax_number" class="form-control " id="tax_number">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tin" class="control-label">Tin Number</label>
                                <input type="number" name="tin" class="form-control " id="tin">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address" class="control-label">Address</label>
                                <textarea class="form-control" id="address" rows="3" placeholder="Address" name="address"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary submit-btn save-btn"> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
    
@section('script')

<script>
    
    var charturl = "{{URL::to('/admin/share-holders')}}";


    var customerTBL = $('#chartTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
        url: charturl,
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        },
        deferRender: true,
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'company_name', name: 'company_name'},
            {data: 'tax_number', name: 'tax_number'},
            {data: 'tin', name: 'tin'},
            {data: 'phone', name: 'phone'},
            {
                data: null,
                name: 'ledger_action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var ledgerUrl = '{{ url("admin/shareholder-ledger") }}/' + row.id;
                    return '<a href="' + ledgerUrl + '" class="btn btn-primary btn-xs" title="Ledger"><i class="fa fa-book" aria-hidden="true"></i></a>';
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    let button = `<button type="button" class="btn btn-warning btn-xs edit-btn" data-toggle="modal" data-target="#chartModal" value="${row.id}" title="Edit" data-purpose='1'><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>`;
                    if (row.amount < 0) {
                    }
                    return button;
                }
            },
        ]
    });

    $('#chartModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        let purpose = button.data('purpose');
        var modal = $(this);
        if (purpose) {
            let id = button.val();
            $.ajax({
                url: charturl +'/' + id,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    modal.find('#name').val(response.name);
                    modal.find('#company_name').val(response.company_name);
                    modal.find('#phone').val(response.phone);
                    modal.find('#tax_number').val(response.tax_number);
                    modal.find('#tin').val(response.tin);
                    modal.find('#address').val(response.address);
                    $('#chartModal .submit-btn').removeClass('save-btn').addClass('update-btn').text('Update').val(response.id);
                }
            });
        } else {
            $('#customer-form').trigger('reset');
            $('#customer-form textarea').text('');
            $('#chartModal .submit-btn').removeClass('update-btn').addClass('save-btn').text('Save').val("");
        }
    });

    // save button event

    $(document).on('click', '.save-btn', function () {
        let formData = $('#customer-form').serialize();
        $.ajax({
            url: charturl,
            type: 'POST',
            data: formData,
            beforeSend: function (request) {
                request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                if (response.status === 200) {
                    $('#chartModal').modal('toggle');
                    swal({
                        text: "Saved successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                    customerTBL.draw();
                } else if (response.status === 303) {
                    let alertMessage = `<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>${response.message}</b></div>`;
                    $('#alert-container1').html(alertMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // update button event

    $(document).on('click', '.update-btn', function () {
        let formData = $('#customer-form').serialize();
        let id = $(this).val();
        $.ajax({
            url: charturl + '/' + id,
            type: 'PUT',
            data: formData,
            beforeSend: function (request) {
                request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                if (response.status === 200) {
                    $('#chartModal').modal('toggle');
                    swal({
                        text: "Updated successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                    customerTBL.draw();
                } else if (response.status === 303) {
                    let alertMessage = `<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>${response.message}</b></div>`;
                    $('#alert-container').html(alertMessage);
                }
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    });

</script>

<script>

    function clearSubAccountHead() {
        $("#sub_account_head").empty();
    }

    $('#chartModal').on('hidden.bs.modal', function () {
        clearSubAccountHead();
    });

    function clearfield() {
        $("#sub_account_head").html("<option value=''>Please Select</option>");
    }

    $("#account_head").change(function(){
          $(this).find("option:selected").each(function(){
              var val = $(this).val();
              if( val == "Assets"){
                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Current Asset'>Current Asset</option><option value='Fixed Asset'>Fixed Asset</option><option value='Account Receivable'>Account Receivable</option>");

              } else if(val == "Expenses"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Cost Of Good Sold'>Cost Of Good Sold</option><option value='Overhead Expense'>Overhead Expense</option>");

              }else if(val == "Income"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Direct Income'>Direct Income</option><option value='Indirect Income'>Indirect Income</option>");

              }else if(val == "Liabilities"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Current Liabilities'>Current Liabilities</option><option value='Long Term Liabilities'>Long Term Liabilities</option> <option value='Account Payable'>Account Payable</option>");

              }else if(val == "Equity"){

                  clearfield();
                  $("#sub_account_head").html("<option value=''>Please Select</option><option value='Equity Capital'>Equity Capital</option><option value='Retained Earnings'>Retained Earnings</option>");

              }else{
                
              }
          });
    }).change();
</script>

@endsection