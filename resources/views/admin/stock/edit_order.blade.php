@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Edit Order</h3>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="advance_date">Advance Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="advance_date" name="advance_date" required value="{{ $purchase->advance_date }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="consignment_number">Consignment Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="consignment_number" name="consignment_number" required value="{{ $purchase->consignment_number }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="mother_vassels_id">Mother Vessel <span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newMotherVesselModal">
                                              Add New
                                          </span>
                                        </label>
                                        <select class="form-control select2" id="mother_vassels_id" name="mother_vassels_id" required>
                                            <option value="">Select...</option>
                                            @foreach($motherVassels as $mother_vassel)
                                                <option value="{{ $mother_vassel->id }}" @selected($purchase->mother_vassels_id == $mother_vassel->id)>{{ $mother_vassel->name }} - {{ $mother_vassel->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier <span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newSupplierModal">
                                              Add New
                                          </span>
                                        </label>
                                        <select class="form-control select2" id="supplier_id" name="supplier_id" required>
                                            <option value="">Select...</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" @selected($purchase->supplier_id == $supplier->id)>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="purchase_type">Payment Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="purchase_type" name="purchase_type" required>
                                            <option value="">Select...</option>
                                            <option value="Cash" @selected($purchase->purchase_type == 'Cash')>Cash</option>
                                            <option value="Bank" @selected($purchase->purchase_type == 'Bank')>Bank</option>
                                            <option value="Due" @selected($purchase->purchase_type == 'Due')>Due</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="advance_amount">Advance Amount</label>
                                        <input type="number" step="0.01" class="form-control" id="advance_amount" name="advance_amount" value="{{ $purchase->advance_amount ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="advance_quantity">Advance Quantity</label>
                                        <input type="number" class="form-control" id="advance_quantity" name="advance_quantity" min="1" value="{{ $purchase->advance_quantity ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="cost_per_unit">Cost Per Unit</label>
                                        <input type="number" class="form-control" id="cost_per_unit" name="cost_per_unit" min="1" value="{{ $purchase->cost_per_unit ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-sm-6 mt-4">
                                    <div class="modal fade" id="chartModal" tabindex="-1" role="dialog" aria-labelledby="chartModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-md" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Add Expense</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>   
                                                </div>
                                                <form class="form-horizontal" id="customer-form">
                                                    <div class="modal-body">
                                                        <div class="row d-none">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="account_head" class="col-form-label">Account Head</label>
                                                                    <select class="form-control" name="account_head" id="account_head">
                                                                        <option value="Expenses" selected>Expenses</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="sub_account_head" class="col-form-label">Account Sub Head</label>
                                                                    <select class="form-control" name="sub_account_head" id="sub_account_head">
                                                                        <option value='Cost Of Good Sold' selected>Cost Of Good Sold</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="account_name" class="col-form-label">Account Name</label>
                                                                    <input type="text" name="account_name" class="form-control" id="account_name" >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="description" class="col-form-label">Description</label>
                                                                    <textarea class="form-control" id="description" rows="3" name="description"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary submit-btn save-btn">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#chartModal">Add New Expense</span>
                                    <div id="expense-container">
                                        @forelse($purchaseExpenses as $expense)
                                        <div class="row mt-1 expense-row" id="row-{{ $expense->id }}">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <select class="form-control expense-type" style="width: 200px;">
                                                    <option value="">Select Expense</option>
                                                    @foreach($expenses as $exp)
                                                        <option value="{{ $exp->id }}" @selected($expense->chart_of_account_id == $exp->id)>{{ $exp->account_name }}</option>
                                                    @endforeach
                                                </select>
                                                <select class="form-control payment-type" style="width: 100px; margin-left: 10px;">
                                                    <option value="Bank" @selected($expense->payment_type == 'Bank')>Bank</option>
                                                    <option value="Cash" @selected($expense->payment_type == 'Cash')>Cash</option>
                                                </select>
                                                <input type="number" class="form-control expense-amount" style="width: 100px; margin-left: 10px;" min="0" value="{{ $expense->amount }}">                              
                                                <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" value="{{ $expense->description ?? '' }}">
                                                <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" value="{{ $expense->note ?? '' }}">
                                                @if ($loop->first)
                                                <button type="button" class="btn btn-success add-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-plus"></i></button>
                                                @else
                                                <button type="button" class="btn btn-danger remove-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-trash"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                        @empty
                                        <div class="row mt-1 expense-row" id="row-default">
                                            <div class="col-sm-12 d-flex align-items-center">
                                                <select class="form-control expense-type" style="width: 200px;" >
                                                    <option value="" selected>Select Expense</option>
                                                    @foreach($expenses as $expense)
                                                        <option value="{{ $expense->id }}">{{ $expense->account_name }}</option>
                                                    @endforeach
                                                </select>
                                                <select class="form-control payment-type" style="width: 100px; margin-left: 10px;">
                                                    <option value="Bank">Bank</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                                <input type="number" class="form-control expense-amount" style="width: 100px; margin-left: 10px;" min="0" placeholder="Amount">                              
                                                <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" placeholder="Description">
                                                <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" placeholder="Note">
                                                <button type="button" class="btn btn-success add-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" id="addBtn" class="btn btn-secondary" value="Update"><i class="fas fa-edit"></i> Update</button>
                                <a href="{{ route('orderList') }}" class="btn btn-secondary">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('admin.inc.modal.mother_vessel_modal')
@include('admin.inc.modal.supplier_modal')

@endsection

@section('script')
<script>
    var expensesList = @json($expenses);
</script>

<script>
    $(document).on('click', '.save-btn', function () {
        let formData = {
            account_head: $('#account_head').val(),
            sub_account_head: $('#sub_account_head').val(),
            account_name: $('#account_name').val(),
            description: $('#description').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: "/admin/chart-of-account",
            method: "POST",
            data: formData,
            success: function (res) {
                if (res.status === 200) {
                    $('#chartModal').modal('hide');
                    $('#account_name').val(''); 
                    $('#description').val(''); 
                    swal({
                        title: "Created Successfully",
                        text: "",
                        icon: "success",
                    });
                    appendExpenseToSelects(res.data.id, res.data.account_name);
                } else {
                    alert(res.message);
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    function appendExpenseToSelects(id, name) {
        const option = `<option value="${id}">${name}</option>`;
        $('.expense-type').append(option);
        expensesList.push({ id: id, account_name: name });
    }
</script>

<script>
    $(document).ready(function() {

        function generateDropdownOptions() {
            let options = '';
            expensesList.forEach(e => {
                options += `<option value="${e.id}">${e.account_name}</option>`;
            });
            return options;
        }

        function checkDuplicateExpense(selectElement) {
            const selectedValue = $(selectElement).val();
            let hasDuplicate = false;

            $('.expense-row').each(function () {
                const expenseId = $(this).find('.expense-type').val();
                if (expenseId && expenseId === selectedValue && $(selectElement).closest('.expense-row')[0] !== this) {
                    hasDuplicate = true;
                    return false;
                }
            });

            if (hasDuplicate) {
                swal({
                    title: "Duplicate Expense",
                    text: "This expense is already added!",
                    icon: "warning",
                });
                $(selectElement).val('');
            }
        }

        function addExpenseRow() {
            const expenseDropdown = generateDropdownOptions();
            const uniqueId = Date.now();

            const row = `
                <div class="row mt-1 expense-row" id="row-${uniqueId}">
                    <div class="col-sm-12 d-flex align-items-center">
                        <select class="form-control expense-type" style="width: 200px;">
                            <option value="" selected>Select Expense</option>
                            ${expenseDropdown}
                        </select>
                        <select class="form-control payment-type" style="width: 100px; margin-left: 10px;">
                            <option value="Bank">Bank</option>
                            <option value="Cash">Cash</option>
                        </select>
                        <input type="number" class="form-control expense-amount" style="width: 100px; margin-left: 10px;" min="0" placeholder="Amount">
                        <input type="text" class="form-control expense-description" style="width: 150px; margin-left: 10px;" placeholder="Description">
                        <input type="text" class="form-control expense-note" style="width: 150px; margin-left: 10px;" placeholder="Note">
                        <button type="button" class="btn btn-danger remove-expense btn-sm" style="margin-left: 10px;"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            $('#expense-container').append(row);
        }

        $(document).on('click', '.remove-expense', function () {
            $(this).closest('.expense-row').remove();
        });

        $(document).on('click', '.add-expense', function () {
            addExpenseRow();
        });

        $(document).on('change', '.expense-type', function () {
            checkDuplicateExpense(this);
        });

        $('#saveSupplierBtn').on('click', function() {
            let password = $('#password').val();
            let confirmPassword = $('#confirm_password').val();
            let name = $('#supplier_name').val();

            if (name == '') {
                swal({
                    text: "Name is required !",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                })
                return false;
            }

            if (password !== confirmPassword) {
                swal({
                    text: "Passwords do not match !",
                    icon: "error",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
                return false;
            }

            let formData = {
                id_number: $('#supplier_id_number').val(),
                name: $('#supplier_name').val(),
                email: $('#supplier_email').val(),
                phone: $('#supplier_phone').val(),
                password: $('#password').val(),
                vat_reg: $('#vat_reg1').val(),
                contract_date: $('#contract_date').val(),
                address: $('#address').val(),
                company: $('#company').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: '{{ route('supplier.store') }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        let newOption = new Option(response.data.name, response.data.id, false, true);
                        $('#supplier_id').append(newOption).trigger('change');
                        $('#newSupplierModal').modal('hide');
                        $('#newSupplierForm')[0].reset();
                        swal({
                            text: "Created successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    } else {
                        alert('Failed to add supplier.');
                    }
                },
                error: function(xhr) {
                    var errorMessage = "An error occurred. Please try again later.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors)[0][0];
                    }
                    swal({
                        text: errorMessage,
                        icon: "error",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    })
                }
            });
        });

        $('#saveMotherVesselBtn').on('click', function () {
            let formData = {
                name: $('#name').val(),
                code: $('#code').val(),
                description: $('#description').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: '/admin/mother-vassel',
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.status === 300) {
                        let newOption = new Option(`${response.data.name} - ${response.data.code ?? ''}`, response.data.id, true, true);
                        $('#mother_vassels_id').append(newOption).trigger('change');
                        $('#newMotherVesselModal').modal('hide');
                        $('#motherVesselForm')[0].reset();
                        swal({
                            text: "Mother Vessel created successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        });
                    } else {
                        swal({
                            html: true,
                            text: $(response.message).text(),
                            icon: "warning"
                        });
                    }
                },
                error: function (xhr) {
                    alert('Server Error. Try again.');
                }
            });
        });

        $('#addBtn').on('click', function(e) {
            e.preventDefault();
            
            if (!$('#consignment_number').val()) {
                swal("Error!", "Consignment Number is required!", "error");
                return;
            }
            if (!$('#mother_vassels_id').val()) {
                swal("Error!", "Mother Vessel is required!", "error");
                return;
            }
            if (!$('#supplier_id').val()) {
                swal("Error!", "Supplier is required!", "error");
                return;
            }
            if (!$('#advance_date').val()) {
                swal("Error!", "Advance Date is required!", "error");
                return;
            }
            if (!$('#purchase_type').val()) {
                swal("Error!", "Payment Type is required!", "error");
                return;
            }

            var formData = {
                consignment_number: $('#consignment_number').val(),
                advance_date: $('#advance_date').val(),
                mother_vassels_id: $('#mother_vassels_id').val(),
                supplier_id: $('#supplier_id').val(),
                purchase_type: $('#purchase_type').val(),
                advance_amount: $('#advance_amount').val() || 0,
                advance_quantity: $('#advance_quantity').val() || 0,
                cost_per_unit: $('#cost_per_unit').val() || 0,
                _token: '{{ csrf_token() }}'
            };

            var expenses = [];
            $('#expense-container .expense-row').each(function() {
                var $r = $(this);
                var expenseId = $r.find('.expense-type').val();
                if (!expenseId) return;

                expenses.push({
                    expense_id: expenseId,
                    payment_type: $r.find('.payment-type').val(),
                    amount: parseFloat($r.find('.expense-amount').val()) || 0,
                    description: $r.find('.expense-description').val(),
                    note: $r.find('.expense-note').val()
                });
            });

            formData.expenses = expenses;

            $.ajax({
                url: '{{ route("updateOrder", $purchase->id) }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    swal("Success!", "Order updated successfully!", "success").then(() => {
                        window.location.href = "{{ route('orderList') }}";
                    });
                },
                error: function(xhr) {
                    var errorMessage = "An error occurred!";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    swal("Error!", errorMessage, "error");
                    console.log(xhr.responseText);
                }
            });
        });

        $('#advance_amount, #advance_quantity, #cost_per_unit').on('input', function() {
            if ($(this).val() < 0) {
                $(this).val(0);
            }
        });
    });
</script>
@endsection