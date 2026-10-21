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
          <div class="table-responsive">
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
                  department Name
                </td>
                <td>
                  <textarea name="department" id="department" class="form-control" placeholder="Enter department Name"></textarea>
                </td>
              </tr>
              <tr>
                <td>
                  Short Name / department Code
                </td>
                <td>
                  <input name="code" id="code" class="form-control" type="text" placeholder="Short Name / department Code">
                </td>
              </tr>
              <tr>
                <td>
                  College
                </td>
                <td>
                  <select name="college_id" id="college_id" class="select2">
                    <option value="" selected disabled>Select College</option>
                    <?php
                      foreach($colleges as $val){
                      echo "<option value='$val->id'>$val->college ($val->code)</option>";
                      }
                    ?>
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  Subjects
                </td>
                <td>
                  <select name="subject_id[]" id="subject_id" class="select2" multiple="multiple">
                    <?php
                      foreach($subjects as $val){
                      echo "<option value='$val->id'>$val->code</option>";
                      }
                    ?>
                  </select>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">
             Close
          </button>
          <button type="submit" class="btn btn-success btnSubmit" id="submit_add"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
        </div>
      </div>
    </form>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->