<div class="modal fade" id="modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
              Status
              <select name="status" class="form-control select2" id="status" style="width: 100%">
                <option value="0">ENABLED</option>
                <option value="1">DISABLED</option>
              </select>
            </div>
            <div class="form-group">
              Academic Program
              <select name="ap_id" id="ap_id" class="select2 form-control" id="status" style="width: 100%">
                <option selected disabled>Select Academic Program</option>
                <?php foreach($academic_program as $val){ ?>
                  <option value="<?= $val->id ?>"><?= $val->academic_program."($val->code)" ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label>Multiple subject input</label>
              <textarea id="multiple_subject" class="form-control"></textarea>
            </div>
            <div class="form-group">
              Subjects
              <select name="subjects[]" id="subjects" class="form-control select2" id="status" style="width: 100%" multiple>
                <?php foreach($subjects as $val){ ?>
                  <option value="<?= $val->id ?>"><?= $val->code ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              Curriculum Year
              <select id="year" name="year" class="select2">
                <?php
                // Get the current year
                $currentYear = date("Y");

                // Generate options for the select element
                for ($i = $currentYear + 1; $i >= $currentYear - 20; $i--) {
                    echo "<option value='$i'>$i</option>";
                }
                ?>
              </select>
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