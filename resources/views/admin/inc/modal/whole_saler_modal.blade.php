<!-- New Whole Saler Modal -->
<div class="modal fade" id="newWholeSalerModal" tabindex="-1" aria-labelledby="newWholeSalerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newWholeSalerModalLabel">Add New WholeSaler</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- New Supplier Form -->
                <form id="newWholeSalerForm">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Surname</label>
                                <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter surname">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="Enter phone">
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
                    </div>
                    <!-- Submit Button -->
                    <button type="button" class="btn btn-success" id="saveWholeSalerBtn">Save WholeSaler</button>
                </form>
            </div>
        </div>
    </div>
</div>