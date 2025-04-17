<div class="modal fade" id="newGhatModal" tabindex="-1" role="dialog" aria-labelledby="newGhatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newGhatModalLabel">Add New Ghat</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="ghatForm">
          <div class="form-group">
            <label for="ghat_name">Ghat Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="ghat_name" name="ghat_name" required>
          </div>
          <button type="button" class="btn btn-success" id="saveGhatBtn">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>