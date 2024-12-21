<!-- Add Color Modal -->
<div class="modal fade" id="addColorModal" tabindex="-1" role="dialog" aria-labelledby="addColorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="addColorModalLabel">Add New Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newColorForm">
                    <div class="form-group">
                        <label for="color_name">Color</label>
                        <input type="text" class="form-control" id="color_name" name="color_name" placeholder="Enter color">
                    </div>
                    <div class="form-group">
                        <label for="color_code">Color Code</label>
                        <input type="color" class="form-control" id="color_code" name="color_code" placeholder="Enter color code">
                    </div>
                    <div class="form-group">
                        <label for="color_price">Price</label>
                        <input type="number" class="form-control" id="color_price" name="color_price" placeholder="Enter price">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveColorBtn">Save Color</button>
            </div>
        </div>
    </div>
</div>