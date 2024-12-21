<!-- New Supplier Modal -->
<div class="modal fade" id="newSupplierModal" tabindex="-1" aria-labelledby="newSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSupplierModalLabel">Add New Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- New Supplier Form -->
                <form id="newSupplierForm">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Code <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="supplier_id_number" name="id_number" placeholder="Enter code" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplier_name" name="name" placeholder="Enter name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="supplier_email" name="email" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="number" class="form-control" id="supplier_phone" name="phone" placeholder="Enter phone">
                            </div>
                        </div>
                        <div class="col-sm-6 d-none">
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" id="password" name="password" value="123456" placeholder="Enter password">
                            </div>
                        </div>
                        <div class="col-sm-6 d-none">
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" value="123456" placeholder="Enter password">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Vat Reg</label>
                                <input type="number" class="form-control" id="vat_reg1" name="vat_reg" placeholder="Enter vat reg">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Contract Date</label>
                                <input type="date" class="form-control" id="contract_date" name="contract_date" placeholder="Enter contract date">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter address"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Company</label>
                                <textarea class="form-control" id="company" name="company" rows="3" placeholder="Enter company"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <button type="button" class="btn btn-success" id="saveSupplierBtn">Save Supplier</button>
                </form>
            </div>
        </div>
    </div>
</div>