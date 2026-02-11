@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <a href="{{ route('orderList') }}" class="btn btn-secondary mb-3">Back</a>
                <div class="card card-secondary">
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="deleted_purchase_histories" name="deleted_purchase_histories" value="">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="advance_date">Advance Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="advance_date" name="advance_date" placeholder="" value="{{ $purchase->advance_date }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="purchase_date">Receiving Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" placeholder="" value="{{ $purchase->purchase_date ?? date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier <span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newSupplierModal">
                                              Add New
                                          </span>
                                        </label>
                                        <select class="form-control select2" id="supplier_id" name="supplier_id">
                                            <option value="">Select...</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" data-balance="{{ $supplier->balance }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="mother_vessels_id">Mother Vessel<span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newMotherVesselModal">
                                            Add New
                                          </span>
                                         </label>
                                        <select class="form-control select2" id="mother_vessels_id" name="mother_vessels_id">
                                            <option value="">Select...</option>
                                            @foreach($motherVassels as $mother_vassel)
                                                <option value="{{ $mother_vassel->id }}" {{ $purchase->mother_vassels_id == $mother_vassel->id ? 'selected' : '' }}>
                                                    {{ $mother_vassel->name }} - {{ $mother_vassel->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="bill_number">Bill Number</label>
                                        <input type="text" class="form-control" id="bill_number" name="bill_number" placeholder="" value="{{ $purchase->bill_number }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="purchase_type">Payment Type</label>
                                        <select class="form-control" id="purchase_type" name="purchase_type">
                                            <option value="">Select...</option>
                                            <option value="Cash" {{ $purchase->purchase_type == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="Bank" {{ $purchase->purchase_type == 'Bank' ? 'selected' : '' }}>Bank</option>
                                            <option value="Due" {{ $purchase->purchase_type == 'Due' ? 'selected' : '' }}>Due</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="advance_amount">Advance Qty</label>
                                        <input type="number" class="form-control" id="advance_quantity" name="advance_quantity" placeholder="" value="{{ $purchase->advance_quantity }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="cost_per_unit">Cost Per Unit</label>
                                        <input type="number" class="form-control" id="cost_per_unit" name="cost_per_unit" placeholder="" value="{{ $purchase->cost_per_unit }}">
                                    </div>
                                </div>
                                <div class="col-sm-1 d-none">
                                    <div class="form-group">
                                        <label>New</label>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#newSupplierModal">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-3 d-none">
                                    <div class="form-group">
                                        <label for="supplier_balance">Supplier Balance</label>
                                        <input type="text" class="form-control" id="supplier_balance" name="supplier_balance" placeholder="" readonly>
                                        <input type="hidden" id="previous_purchase_due" value="{{ $purchase->due_amount }}">
                                    </div>
                                </div>

                                <div class="col-sm-4 d-none">
                                    <div class="form-group">
                                        <label for="lighter_vessels_id">Lighter Vessel<span class="text-danger">*</span></label>
                                        <select class="form-control" id="lighter_vessels_id" name="lighter_vessels_id">
                                            <option value="">Select...</option>
                                            @foreach($lighterVassels as $lighter_vessel)
                                                <option value="{{ $lighter_vessel->id }}" {{ $purchase->lighter_vassels_id == $lighter_vessel->id ? 'selected' : '' }}>
                                                    {{ $lighter_vessel->name }} - {{ $lighter_vessel->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4 d-none">
                                    <div class="form-group">
                                        <label for="ghats_id">Ghat<span class="text-danger">*</span></label>
                                        <select class="form-control" id="ghats_id" name="ghats_id">
                                            <option value="">Select...</option>
                                            @foreach($ghats as $ghat)
                                                <option value="{{ $ghat->id }}" {{ $purchase->ghats_id == $ghat->id ? 'selected' : '' }}>
                                                    {{ $ghat->name }} - {{ $ghat->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3 d-none">
                                    <div class="form-group">
                                        <label for="vat_reg">VAT Reg#</label>
                                        <input type="text" class="form-control" id="vat_reg" name="vat_reg" placeholder="" value="{{ $purchase->vat_reg }}">
                                    </div>
                                </div>

                                <div class="col-sm-6 d-none">
                                    <div class="form-group">
                                        <label for="ref">Ref</label>
                                        <input type="text" class="form-control" id="ref" name="ref" placeholder="" value="{{ $purchase->ref }}">
                                    </div>
                                </div>

                                <div class="col-sm-6 d-none">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea class="form-control" id="remarks" name="remarks" rows="1" placeholder="">{{ $purchase->remarks }}</textarea>
                                    </div>
                                </div>

                                <div class="col-sm-12"><hr></div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="product_id">Choose Product <span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newProductModal">
                                              Add New
                                          </span>
                                        </label>
                                        <select class="form-control select2" id="product_id" name="product_id">
                                            <option value="">Select...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-name="{{ $product->name }}">{{ $product->name }} - {{ $product->product_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="lighter_vassel_id ">Choose Lighter Vessel<span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newLighterVesselModal">
                                                Add New
                                            </span>
                                          </label>
                                            <select class="form-control select2" id="lighter_vassel_id" name="lighter_vassel_id">
                                            <option value="">Select...</option>
                                            @foreach($lighterVassels as $lighter_vassel)
                                                <option value="{{ $lighter_vassel->id }}">
                                                    {{ $lighter_vassel->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="warehouse_id">Choose Warehouse<span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newWarehouseModal">
                                              Add New
                                          </span>
                                        </label>
                                        <select class="form-control select2" id="warehouse_id" name="warehouse_id">
                                            <option value="">Select...</option>
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}">
                                                    {{ $warehouse->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="ghat_id">Choose Ghat<span class="text-danger">*</span>
                                          <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#newGhatModal">
                                              Add New
                                        </label>
                                        <select class="form-control select2" id="ghat_id" name="ghat_id">
                                            <option value="">Select...</option>
                                            @foreach($ghats as $ghat)
                                                <option value="{{ $ghat->id }}">
                                                    {{ $ghat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="quantity_type">Quantity Type</label>
                                        <select class="form-control" id="quantity_type" name="quantity_type">
                                            <option value="Scale Quantity">Scale Quantity</option>
                                            <option value="Survey Quantity">Survey Quantity</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" placeholder="" oninput="validateMinValue(this, 1)">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="unloading_cost">Unloading Cost Per Unit</label>
                                        <input type="number" step="0.01" class="form-control" id="unloading_cost" name="unloading_cost" min="1" placeholder="" oninput="validateMinValue(this, 1)">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="lighter_rent">Lighter Rent Per Unit</label>
                                        <input type="number" step="0.01" class="form-control" id="lighter_rent" name="lighter_rent" min="1" placeholder="" oninput="validateMinValue(this, 1)">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="unit_price">Purchase Price Per Unit<span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" min="1" placeholder="" oninput="validateMinValue(this, 1)" value="{{ $purchase->cost_per_unit }}">
                                    </div>
                                </div>
                                <div class="col-sm-2 d-none">
                                    <div class="form-group">
                                        <label for="product_size">Size <span class="text-danger">*</span></label>
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addSizeModal">Add New</span>
                                        <select class="form-control" id="product_size" name="product_size">
                                            <option value="">Select...</option>
                                            @foreach ($sizes as $size)
                                                <option value="{{ $size->size }}">{{ $size->size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                 <div class="col-sm-2 d-none">
                                    <div class="form-group">
                                        <label for="product_color">Color <span class="text-danger">*</span></label>
                                        <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Add New</span>
                                        <select class="form-control" id="product_color" name="product_color">
                                            <option value="">Select...</option>
                                            @foreach ($colors as $color)
                                                <option value="{{ $color->color }}">{{ $color->color }}</option>
                                            @endforeach                                    
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <label for="addProductBtn">Action</label>
                                    <div class="col-auto d-flex align-items-center">
                                        <button type="button" id="addProductBtn" class="btn btn-secondary">Add</button>
                                     </div>
                                </div>

                                <div class="col-sm-12 mt-3">
                                    <h2>Product List:</h2>
                                    <table class="table table-bordered" id="productTable">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>L. Vessel</th>
                                                <th>Warehouse</th>
                                                <th>Ghat</th>
                                                <th>Unloading Cost</th>
                                                <th>Lighter Rent</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productTable">
                                            @foreach($purchase->purchaseHistory as $history)
                                            <tr data-id="{{ $history->id }}" data-product-id="{{ $history->product->id }}">
                                                <td>{{ $history->product->name }}
                                                    <input type="hidden" class="lighter_vessel_id" value="{{ $history->lighter_vassel_id }}" />
                                                    <input type="hidden" class="warehouse_id" value="{{ $history->warehouse_id  }}" />
                                                    <input type="hidden" class="ghat_id" value="{{ $history->ghat_id  }}" />
                                                    <input type="hidden" class="quantity_type" value="{{ $history->quantity_type  }}" />
                                                </td>
                                                <td>{{ $history->lighterVessel->name }}</td>                                  
                                                <td>{{ $history->warehouse->name }}</td>
                                                <td>{{ $history->ghat->name }}</td>
                                                <td><input type="number" class="form-control unloading_cost" value="{{ $history->unloading_cost }}" /></td>
                                                <td><input type="number" class="form-control lighter_rent" value="{{ $history->lighter_rent }}" /></td>
                                                <td><input type="number" class="form-control quantity" value="{{ $history->quantity }}" /></td>
                                                <td><input type="number" step="0.01" class="form-control unit_price" value="{{ $history->purchase_price }}" /></td>
                                                <td>{{ $history->total_price }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-product" title="Remove">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-sm-6 mt-4 mb-5 d-none">

                                    <div class="row d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Direct cost:</span>
                                            <input type="number" class="form-control" id="direct_cost" style="width: 100px; margin-left: auto;" value="{{ $purchase->direct_cost }}" min="0">
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">CNF cost:</span>
                                            <input type="number" class="form-control" id="cnf_cost" style="width: 100px; margin-left: auto;" value="{{ $purchase->cnf_cost }}" min="0">
                                        </div>
                                    </div>

                                    
                                    <div class="row mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Title need:</span>
                                            <input type="number" class="form-control" id="cost_a" style="width: 100px; margin-left: auto;" value="{{ $purchase->cost_a }}" min="0">
                                        </div>
                                    </div>

                                    <div class="row mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Title need:</span>
                                            <input type="number" class="form-control" id="cost_b" style="width: 100px; margin-left: auto;" value="{{ $purchase->cost_b }}" min="0">
                                        </div>
                                    </div>

                                    <div class="row mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Others cost:</span>
                                            <input type="number" class="form-control" id="other_cost" style="width: 100px; margin-left: auto;" value="{{ $purchase->other_cost }}" min="0">
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="col-sm-6 mt-4 mb-5">
                                    <h5 class="mb-3">Expenses:</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Expense Type</th>
                                                    <th>Payment Type</th>
                                                    <th>Amount</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($purchaseExpenses as $expense)
                                                    <tr>
                                                        <td>{{ $expense->chartOfAccount->account_name ?? 'N/A' }}</td>
                                                        <td>{{ $expense->payment_type }}</td>
                                                        <td>{{ number_format($expense->amount, 2) }}</td>
                                                        <td>{{ $expense->description ?? '-' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No expenses added</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-sm-6 mt-4 mb-5">
                                    <div class="row justify-content-end">
                                        <div class="col-sm-8 d-flex align-items-center">
                                            <span class="">Item Total Amount:</span>
                                            <input type="text" class="form-control" id="item_total_amount" readonly style="width: 150px; margin-left: auto;" value="{{ $purchase->item_total_amount }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-8 d-flex align-items-center">
                                              <a href="{{ route('advance.transactions', ['id' => $purchase->id]) }}" title="View Advance Transactions Details" target="_blank">
                                                <span class="">Total Advance Amount:</span>
                                              </a>
                                              <input type="text" class="form-control" id="advance_amount" readonly style="width: 150px; margin-left: auto;" value="{{ $purchase->advance_amount }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-8 d-flex align-items-center">
                                            <span class="">Discount Amount:</span>
                                            <input type="number" step="0.01" class="form-control" id="discount" name="discount" style="width: 150px; margin-left: auto;" value="{{ $purchase->discount }}">
                                            <input type="hidden" id="hidden_discount" value="{{ $purchase->discount }}" min="0">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-8 d-flex align-items-center">
                                            <span class="">VAT(%):</span>
                                            <input type="text" class="form-control" id="vat_percent" style="width: 150px; margin-left: auto;" value="{{ $purchase->vat_percent }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-8 d-flex align-items-center">
                                            <span class="">Total VAT Amount:</span>
                                            <input type="text" class="form-control" id="total_vat_amount" readonly style="width: 150px; margin-left: auto;" value="{{ $purchase->total_vat_amount }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-8 d-flex align-items-center">
                                            <span class="">Total Unloading Cost:</span>
                                            <input type="text" class="form-control" id="total_unloading_cost" readonly style="width: 150px; margin-left: auto;" value="{{ $purchase->total_unloading_cost }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-8 d-flex align-items-center">
                                            <span class="">Total Lighter Rent:</span>
                                            <input type="text" class="form-control" id="total_lighter_rent" readonly style="width: 150px; margin-left: auto;" value="{{ $purchase->total_lighter_rent }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1">
                                        <div class="col-sm-8 d-flex align-items-center">
                                            <span class="">Due Amount:</span>
                                            <input type="text" class="form-control" id="net_amount" readonly style="width: 150px; margin-left: auto;" value="{{ $purchase->net_amount }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-3 d-none">
                                        <div class="col-sm-3 d-flex align-items-center">
                                            <span class="">Paid Amount:</span>
                                            <input type="number" step="0.01" class="form-control" id="paid_amount" name="paid_amount" style="width: 150px; margin-left: auto;" value="{{ $purchase->paid_amount }}">
                                            <input type="hidden" id="hidden_paid_amount" value="{{ $purchase->paid_amount }}">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Cash Payment:</span>
                                            <input type="number" step="0.01" class="form-control" id="cash_payment" name="cash_payment" style="width: 150px; margin-left: auto;" value="{{ $cashAmount ? $cashAmount->amount : 0 }}" min="0">
                                        </div>
                                    </div>
                                    
                                    <div class="row justify-content-end mt-1 d-none">
                                        <div class="col-sm-6 d-flex align-items-center">
                                            <span class="">Bank Payment:</span>
                                            <input type="number" step="0.01" class="form-control" id="bank_payment" name="bank_payment" style="width: 150px; margin-left: auto;" value="{{ $bankAmount ? $bankAmount->amount : 0 }}" min="0">
                                        </div>
                                    </div>
                                    <div class="row justify-content-end mt-3 d-none">
                                        <div class="col-sm-3 d-flex align-items-center">
                                            <span class="">Due Amount:</span>
                                            <input type="text" class="form-control" id="due_amount" readonly style="width: 150px; margin-left: auto;">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" id="addBtn" class="btn btn-secondary" value="Create"><i class="fas fa-sync-alt"></i> @if($purchase->invoice)Update Stock @else Add To Stock @endif</button>    
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
@include('admin.inc.modal.product_modal')
@include('admin.inc.modal.lighter_vessel_modal')
@include('admin.inc.modal.warehouse_modal')
@include('admin.inc.modal.ghat_modal')

<script>
    function validateMinValue(input, minValue) {
        if (parseFloat(input.value) < minValue) {
            input.value = minValue;
        }
    }
</script>

@endsection

@section('script')

<script>
  $(document).ready(function() {

    $('#saveSupplierBtn').on('click', function() {

      let password = $('#password').val();
      let confirmPassword = $('#confirm_password').val();
      let name = $('#supplier_name').val();
      let email = $('#supplier_email').val();

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
          error: function(xhr, status, error) {
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
              console.log(xhr.responseText);
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
                  $('#mother_vessels_id').append(newOption).trigger('change');

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

    $('#saveProductBtn').on('click', function () {
        let formData = {
            name: $('#product_name').val(),
            product_code: $('#product_code').val(),
            category_id: $('#category').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '/admin/product',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.product) {
                  let newOption = $('<option>', {
                        value: response.product.id,
                        text: response.product.name + ' - ' + response.product.product_code,
                        'data-name': response.product.name,
                        selected: true
                    });
                    $('#product_id').append(newOption).trigger('change');

                    $('#newProductModal').modal('hide');
                    $('#productForm')[0].reset();

                    swal({
                        text: response.message,
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                } else {
                    swal({
                        html: true,
                        text: response.message,
                        icon: "warning"
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                alert('Server Error. Try again.');
            }
        });
    });

    $('#saveLighterVesselBtn').on('click', function () {
        let formData = {
            name: $('#lighter_name').val(),
            code: $('#lighter_code').val(),
            description: $('#lighter_description').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '/admin/lighter-vassel',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.status === 300) {
                    let newOption = new Option(`${response.data.name} - ${response.data.code ?? ''}`, response.data.id, true, true);
                    $('#lighter_vassel_id').append(newOption).trigger('change');

                    $('#newLighterVesselModal').modal('hide');
                    $('#lighterVesselForm')[0].reset();

                    swal({
                        text: "Lighter Vessel created successfully",
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

    $('#saveWarehouseBtn').on('click', function () {
        let formData = {
            name: $('#warehouse_name').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '/admin/warehouse', 
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.status === 300) {
                    let newOption = new Option(response.data.name, response.data.id, true, true);
                    $('#warehouse_id').append(newOption).trigger('change');
                    $('#newWarehouseModal').modal('hide');
                    $('#warehouseForm')[0].reset();

                    swal({
                        text: "Warehouse created successfully",
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

    $('#saveGhatBtn').on('click', function () {
        let formData = {
            name: $('#ghat_name').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: '/admin/ghat',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.status === 300) {
                    let newOption = new Option(response.data.name, response.data.id, true, true);
                    $('#ghat_id').append(newOption).trigger('change');

                    $('#newGhatModal').modal('hide');
                    $('#ghatForm')[0].reset();

                    swal({
                        text: "Ghat created successfully",
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

  });
</script>

<script>
    $(document).ready(function() {

        var deletedPurchaseHistories = [];

        function updateSummary() {
            var itemTotalAmount = 0;
            var totalVatAmount = 0;
            var totalUnloadingCost = 0;
            var totalLighterRent = 0;

            $('#productTable tbody tr').each(function () {
                var quantity = parseFloat($(this).find('input.quantity').val()) || 0;
                var unitPrice = parseFloat($(this).find('input.unit_price').val()) || 0;
                var unloadingCost = parseFloat($(this).find('input.unloading_cost').val()) || 0;
                var lighterRent = parseFloat($(this).find('input.lighter_rent').val()) || 0;

                var totalPrice = (quantity * unitPrice).toFixed(2);

                var vatPercent = parseFloat($('#vat_percent').val()) || 0;
                var vatAmount = (totalPrice * vatPercent / 100).toFixed(2);

                var totalPriceWithVat = (parseFloat(totalPrice) + parseFloat(vatAmount)).toFixed(2);

                $(this).find('td:eq(8)').text(totalPrice);
                $(this).find('input.unloading_cost').val(unloadingCost);
                $(this).find('input.lighter_rent').val(lighterRent);

                itemTotalAmount += parseFloat(totalPrice) || 0;
                totalVatAmount += parseFloat(vatAmount) || 0;
                totalUnloadingCost += parseFloat(unloadingCost) * quantity || 0;
                totalLighterRent += parseFloat(lighterRent) * quantity || 0;
            });

            $('#item_total_amount').val(itemTotalAmount.toFixed(2) || '0.00');
            $('#total_vat_amount').val(totalVatAmount.toFixed(2) || '0.00');
            $('#total_unloading_cost').val(totalUnloadingCost.toFixed(2) || '0.00');
            $('#total_lighter_rent').val(totalLighterRent.toFixed(2) || '0.00');

            var discount = parseFloat($('#discount').val()) || 0;
            var direct_cost = parseFloat($('#direct_cost').val()) || 0;
            var cnf_cost = parseFloat($('#cnf_cost').val()) || 0;
            var cost_b = parseFloat($('#cost_b').val()) || 0;
            var cost_a = parseFloat($('#cost_a').val()) || 0;
            var other_cost = parseFloat($('#other_cost').val()) || 0;

            var advanceAmount = parseFloat($('#advance_amount').val()) || 0;

            var netAmount = itemTotalAmount + totalVatAmount - discount + direct_cost + cnf_cost + cost_b + cost_a + other_cost - advanceAmount;
            $('#net_amount').val(netAmount.toFixed(2) || '0.00');

            var paidAmount = parseFloat($('#paid_amount').val()) || 0;
            var dueAmount = netAmount - paidAmount;
            $('#due_amount').val(dueAmount.toFixed(2) || '0.00');
        }

        updateSummary();

        $('#addProductBtn').click(function () {
            var selectedSize = $('#product_size').val() || '';
            var selectedColor = $('#product_color').val() || '';

            var selectedProduct = $('#product_id option:selected');
            var productId = selectedProduct.val();
            var productName = selectedProduct.data('name');
            var quantity = $('#quantity').val();
            var unitPrice = $('#unit_price').val();

            var lighterVesselId = $('#lighter_vassel_id').val();
            var lighterVesselName = $('#lighter_vassel_id option:selected').text();
            var warehouseId = $('#warehouse_id').val();
            var warehouseName = $('#warehouse_id option:selected').text();
            var ghatId = $('#ghat_id').val();
            var ghatName = $('#ghat_id option:selected').text();
            var quantityType = $('#quantity_type').val();
            var unloadingCost = $('#unloading_cost').val();
            var lighterRent = $('#lighter_rent').val();

            if (!productId) {
                alert('Please select a product.');
                return;
            }

            if (isNaN(quantity) || quantity <= 0) {
                alert('Quantity must be a positive number.');
                return;
            }

            if (isNaN(unitPrice) || unitPrice <= 0) {
                alert('Unit price must be a positive number.');
                return;
            }

            if (isNaN(unloadingCost) || unloadingCost < 0) {
                alert('Unloading cost must be a non-negative number.');
                return;
            }

            var totalPrice = (quantity * unitPrice).toFixed(2);

            // var productExists = false;
            // $('#productTable tbody tr').each(function () {
            //     var existingProductId = $(this).data('product-id');
            //     if (productId == existingProductId) {
            //         productExists = true;
            //         return false;
            //     }
            // });

            // if (productExists) {
            //     alert('This product is already in the table.');
            //     return;
            // }

            var productRow = `<tr data-id="" data-product-id="${productId}">
                                <td>
                                    ${productName}
                                    <input type="hidden" class="product_id" value="${productId}" />
                                    <input type="hidden" class="lighter_vessel_id" value="${lighterVesselId}" />
                                    <input type="hidden" class="warehouse_id" value="${warehouseId}" />
                                    <input type="hidden" class="ghat_id" value="${ghatId}" />
                                    <input type="hidden" class="quantity_type" value="${quantityType}" />
                                </td>
                                <td>${lighterVesselName}</td>
                                <td>${warehouseName}</td>
                                <td>${ghatName}</td>
                                <td><input type="number" class="form-control unloading_cost" value="${unloadingCost}" /></td>
                                <td><input type="number" class="form-control lighter_rent" value="${lighterRent}" /></td>
                                <td><input type="number" class="form-control quantity" value="${quantity}" /></td>
                                <td><input type="number" class="form-control unit_price" value="${unitPrice}" /></td>
                                <td>${totalPrice}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-product" title="Remove">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>`;
            $('#productTable tbody').append(productRow);

            $('#quantity').val('');
            // $('#unit_price').val('');
            $('#unloading_cost').val('');
            $('#lighter_rent').val('');
            $('#product_size').val('');
            $('#product_color').val('');
            $('#lighter_vassel_id').val('');
            $('#warehouse_id').val('');
            $('#ghat_id').val('');
            $('#product_id, #lighter_vassel_id, #warehouse_id, #ghat_id').val(null).trigger('change');
            updateSummary();
        });

        $(document).on('click', '.remove-product', function() {
            var purchaseHistoryId = $(this).closest('tr').data('id');
            if (purchaseHistoryId) {
                deletedPurchaseHistories.push(purchaseHistoryId);
                $('#deleted_purchase_histories').val(deletedPurchaseHistories.join(','));
            }
            $(this).closest('tr').remove();
            updateSummary();
        });

        $(document).on('input', '#productTable input.quantity, #productTable input.unit_price, #productTable input.unloading_cost, #productTable input.lighter_rent, #vat_percent, #discount, #direct_cost, #cost_a, #cost_b, #cnf_cost, #other_cost, #paid_amount', function() {
            updateSummary();
        });

        $('#addBtn').on('click', function(e) {
            e.preventDefault();
            var formData = {};
            var selectedProducts = [];

            formData.purchase_id = {{ $purchase->id }};
            formData.advance_date = $('#advance_date').val();
            formData.mother_vassels_id = $('#mother_vessels_id').val();
            formData.purchase_type = $('#purchase_type').val();
            formData.advance_amount = $('#advance_amount').val();
            formData.advance_quantity = $('#advance_quantity').val();
            formData.cost_per_unit = $('#cost_per_unit').val();
            formData.purchase_date = $('#purchase_date').val();
            formData.supplier_id = $('#supplier_id').val();
            formData.vat_reg = $('#vat_reg').val();
            formData.ref = $('#ref').val();
            formData.remarks = $('#remarks').val();
            formData.total_amount = $('#item_total_amount').val();
            formData.discount = $('#discount').val();
            formData.vat_percent = $('#vat_percent').val();
            formData.total_vat_amount = $('#total_vat_amount').val();
            formData.total_unloading_cost = $('#total_unloading_cost').val();
            formData.total_lighter_rent = $('#total_lighter_rent').val();
            formData.direct_cost = $('#direct_cost').val();
            formData.cost_a = $('#cost_a').val();
            formData.cost_b = $('#cost_b').val();
            formData.cnf_cost = $('#cnf_cost').val();
            formData.other_cost = $('#other_cost').val();
            formData.total_vat_amount = $('#total_vat_amount').val();
            formData.net_amount = $('#net_amount').val();
            formData.paid_amount = $('#paid_amount').val();
            formData.hidden_paid_amount = $('#hidden_paid_amount').val();
            formData.due_amount = $('#due_amount').val();
            formData.bank_payment = $('#bank_payment').val();
            formData.cash_payment = $('#cash_payment').val();
            formData.bill_number = $('#bill_number').val();

            $('#productTable tbody tr').each(function() {
                var purchaseHistoryId = $(this).data('id');
                var productId = $(this).data('product-id');
                var quantity = $(this).find('input.quantity').val();
                var lighter_vassel_id = $(this).find('input.lighter_vessel_id').val();
                var warehouse_id = $(this).find('input.warehouse_id').val();
                var ghat_id = $(this).find('input.ghat_id').val();
                var quantity_type = $(this).find('input.quantity_type').val();
                var unloading_cost = $(this).find('input.unloading_cost').val();
                var lighter_rent = $(this).find('input.lighter_rent').val();
                var unitPrice = $(this).find('input.unit_price').val();
                var totalPrice = $(this).find('td:eq(8)').text();

                selectedProducts.push({
                    purchase_history_id: purchaseHistoryId,
                    product_id: productId,
                    lighter_vassel_id: lighter_vassel_id,
                    warehouse_id: warehouse_id,
                    ghat_id: ghat_id,
                    unloading_cost: unloading_cost,
                    quantity: quantity,
                    unit_price: unitPrice,
                    total_price: totalPrice,
                    quantity_type: quantity_type,
                    lighter_rent: lighter_rent
                });
            });

            var finalData = { ...formData, products: selectedProducts };
            // console.log(finalData);

            $.ajax({
                url: '/admin/update-stock',
                method: 'POST',
                data: finalData,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        text: "Sent To Stock successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.href = '/admin/order-list';
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        swal({
                            title: "Error",
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

@endsection