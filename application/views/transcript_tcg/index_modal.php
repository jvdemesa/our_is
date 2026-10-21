<div class="modal fade" id="match_modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-green">
        <h4 class="modal-title" id="modal_title">Match to request</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="form1" class="form-horizontal" role="form">
        <input type="hidden" name="payment_id" id="payment_id">
        <div class="modal-body">
          <div class="form-group">
            <label for="trans_code">Transaction Code</label>
            <select class="select2" id="trans_code" name="trans_code" required>
            </select>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">
             Close
          </button>
          <div class="btn btn-group">
            <button class="btn btn-success" id="confirm_btn">CONFIRM</button>
          </div>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="payment_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        <iframe src="" id="payment_iframe"></iframe>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="compare_email_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-green">
        <h4 class="modal-title" id="modal_title">Matched Email</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <input type="hidden" name="epayment_id" id="epayment_id">
      <div class="modal-body">
        <table class="table table-bordered table-striped" id="matched_email_table" style="width: 100%">
          <thead>
            <th>Transcode</th>
            <th>Requestor name</th>
            <th>Status</th>
            <th>Assigned to</th>
            <th>Action</th>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->