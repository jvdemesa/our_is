<!---- CONFIRM FORSIGNATURE / INITIAL MODAL-->
<div class="modal fade" id="confirm_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header alert alert-success">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Close</span>
        </button>
        <h4 id="status_title">
          Embed Digital Signature / Initial
        </h4>
      </div>
      <!-- Modal Body For Update -->
      <div class="modal-body table-responsive">
        <input type="hidden" name="dtsno" id="dtsno">
        <h2>Are you sure you want to embed your digital signature to this document?</h2>
        <div class="modal-footer" align="right">
          <div class="form-group">
            <div class="col-sm-12" align="right">
              <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times fa-lg"></i> Close
              </button>
              <button type="button" class="btn btn-success" id="confirm_btn">
                <i class="fa fa-thumbs-up fa-lg"></i> Confirm
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>