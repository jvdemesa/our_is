<div class="modal fade" id="file_list_modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-green">
        <h4 class="modal-title" id="modal_title">Uploaded File</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-striped" id="file_table">
          <thead>
            <tr>
              <th width="50">
                <input type="checkbox" id="check_all">
              </th>
              <th>File</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <input type="checkbox" class="file_checkbox" value="id_picture" data-file-id="">
              </td>
              <td>ID Picture</td>
              <td class="id_picture_file"></td>
            </tr>

            <tr>
              <td>
                <input type="checkbox" class="file_checkbox" value="signature" data-file-id="">
              </td>
              <td>Signature</td>
              <td class="signature_file"></td>
            </tr>

            <tr>
              <td>
                <input type="checkbox" class="file_checkbox" value="form5">
              </td>
              <td>Form 5</td>
              <td class="form5_file"></td>
            </tr>

            <tr>
              <td>
                <input type="checkbox" class="file_checkbox" value="first_year_form5">
              </td>
              <td>Form 5 During First Year</td>
              <td class="first_year_form5_file"></td>
            </tr>

            <tr>
              <td>
                <input type="checkbox" class="file_checkbox" value="proof_of_payment">
              </td>
              <td>Proof of payment</td>
              <td class="proof_of_payment_file"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
        <button class="btn btn-warning non_compliance" id="non_compliance" todo="Approval">
          <i class="fa fa-thumbs-down"></i> Non-Compliance
        </button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<div class="modal fade" id="approve_modal">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-green">
        <h4 class="modal-title" id="modal_title">Approve ID Request</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <label for="request_type_label" class="col-sm-2 col-form-label">Request Type</label>
          <div class="col-sm-10">
            <input type="input" class="form-control" id="request_type_label" readonly="readonly">
          </div>
        </div>
        <div class="row mb-3">
          <label for="requestor" class="col-sm-2 col-form-label">requestor</label>
          <div class="col-sm-10">
            <input type="input" class="form-control" id="requestor" readonly="readonly">
          </div>
        </div>
        <table class="table table-bordered table-striped" id="approval_file_table">
          <thead>
            <th>File</th>
            <th>Action</th>
          </thead>
          <tbody>
            <tr>
              <td>Form 5</td>
              <td class="form5_file"></td>
            </tr>              
          </tbody>
        </table>      
      </div>
      <div class="modal-footer ">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
        <button class="btn btn-danger confirm_disapproval" id="confirm_disapproval" todo="Disapproval"  status="2">
          <i class="fa fa-thumbs-down"></i> Disapprove
        </button>
        <button class="btn btn-success confirm_approval" id="approve_application" todo="Approval"  status="1">
          <i class="fa fa-thumbs-up"></i> Approve
        </button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="show_file_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        <iframe src="" id="file_iframe"></iframe>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
        <button class="btn btn-primary" id="download_file">
          <i class="fa fa-download"></i> Download
        </button>
      </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->



