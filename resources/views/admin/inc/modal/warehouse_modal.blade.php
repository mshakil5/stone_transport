<div class="modal fade" id="newWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="newWarehouseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newWarehouseModalLabel">Add New Warehouse</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="warehouseForm">
          <div class="form-group">
            <label>Warehouse Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="warehouse_name" name="warehouse_name" required>
          </div>
          <button type="button" class="btn btn-success" id="saveWarehouseBtn">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>