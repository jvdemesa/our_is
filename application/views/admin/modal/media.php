<div class="modal fade" id="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal_title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="uploadForm" class="form-horizontal" role="form" enctype="multipart/form-data">
        <div class="modal-body">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="table" id="table" value="<?= $table; ?>">
            <input type="hidden" name="id_name" id="id_name" value="<?= $id_name; ?>">
             <table class="table table-bordered table-hover">
              <tr>
                <td>
                  Status
                </td>
                <td>
                  <select name="status" class="form-control select2" id="status" style="width: 100%">
                    <option value="0">ENABLED</option>
                    <option value="1">DISABLED</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  Media File
                </td>
                <td>
                  <div class="input-group">
                    <div class="custom-file">
                      <input type="file" name="userfile" id="userfile" size="20" accept="image/*,video/mp4" />
                      <label class="custom-file-label" for="userfile" id="file_label">Choose file</label>
                    </div>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">
             Close
          </button>
          <button type="submit" class="btn btn-success btnSubmit" id="submit_add">Upload</button>
        </div>
      </div>
    </form>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->