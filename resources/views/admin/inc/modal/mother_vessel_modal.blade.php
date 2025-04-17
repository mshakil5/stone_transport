<div class="modal fade" id="newMotherVesselModal" tabindex="-1" role="dialog" aria-labelledby="newMotherVesselModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newMotherVesselModalLabel">Add New Mother Vessel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="motherVesselForm">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label>Code</label>
                <input type="text" class="form-control" id="code" name="code">
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label>Description</label>
                <input type="text" class="form-control" id="description" name="description">
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-success" id="saveMotherVesselBtn">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>