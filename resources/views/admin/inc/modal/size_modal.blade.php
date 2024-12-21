<!-- Add Size Modal -->
<div class="modal fade" id="addSizeModal" tabindex="-1" role="dialog" aria-labelledby="addSizeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSizeModalLabel">Add New Size</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newSizeForm">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="size_name">Size</label>
                            <input type="text" class="form-control" id="size_name" name="size_name" placeholder="Enter size">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="size_price">Price</label>
                            <input type="number" class="form-control" id="size_price" name="size_price" placeholder="Enter price">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSizeBtn">Save Size</button>
            </div>
        </div>
    </div>
</div>