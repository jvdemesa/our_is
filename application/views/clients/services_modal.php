<div class="modal fade" id="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-green">
        <h4 class="modal-title" id="modal_title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="form1" class="form-horizontal" role="form">
        <input type="hidden" name="service_id" id="service_id">
        <input type="hidden" name="default_number" id="default_number">
        <div class="modal-body">
          <div class="custom-control custom-checkbox">
            <input class="custom-control-input custom-control-input-danger" type="checkbox" id="priority" name="priority" value="1">
            <label for="priority" class="custom-control-label">Are you Senior/PWD/Pregnant?</label>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">
             Close
          </button>
          <button class="btn btn-success btnSubmit" id="confirm">CONFIRM</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->