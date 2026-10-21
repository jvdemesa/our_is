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
            <input type="hidden" name="dbval" id="dbval" value="<?= $dbval; ?>">
            <div class="form-group">
              <label for="status">Status: </label>
                <select name="status" class="form-control select2" id="status" style="width: 100%">
                  <option value="0">ENABLED</option>
                  <option value="1">DISABLED</option>
                </select>
            </div>
            <div class="form-group">
              <label for="college_id">College: </label>
              <select name="college_id" id="college_id" class="form-control select2" id="status" style="width: 100%">
                <option selected disabled>Select College</option>
                <?php foreach($colleges as $val){ ?>
                  <option value="<?= $val->id ?>"><?= $val->college."($val->code)" ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label for="academic_program">Academic Program: </label>
                  <textarea name="academic_program" id="academic_program" class="form-control" placeholder="Enter Course Name">
              </textarea>
            </div>
            <div class="form-group">
              <label for="code">Academic Program Code / Short Name: </label>
              <input name="code" id="code" class="form-control" type="text" placeholder="Short Name / College Code">
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