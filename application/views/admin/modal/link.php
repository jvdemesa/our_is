<?php
$id_name = "link_id";
$table = "link";
$labels = "Link";
?>

<div class="modal fade" id="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal_title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="form1" class="form-horizontal" role="form">
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
                  Title Only (If true, Leave Link as Blank)
                </td>
                <td>
                  <select name="multiple" class="form-control select2" id="multiple" style="width: 100%">
                    <option value="0">False</option>
                    <option value="1">True</option>
                  </select>
                </td>
              </tr>
              <tr>
                    <td><?= $labels; ?></td>
                    <td><input type="text" name="dbval" id="dbval" class="form-control" required="required"/></td>
              </tr>
              <tr>
                    <td>Icon</td>
                    <td><input type="text" name="icon" id="icon" class="form-control" required="required"/></td>
              </tr>
              <tr>
                    <td>Label</td>
                    <td><input type="text" name="label" id="label" class="form-control" required="required"/></td>
              </tr>
            <tr>
                <td>
                  Link After
                </td>
                <td>
                  <select name="arrangement" class="form-control select2" id="arrangement" style="width: 100%">
                    <option value="0">First Link</option>
                    <?php
                      foreach($list as $val){ 
                        $a = $val->arrangement+1;
                        echo "<option value='".$a."'>".$val->label."</option>";
                      }
                    ?>
                  </select>
                </td>
              </tr>
            <tr>
                <td>
                  Parent
                </td>
                <td>
                  <select name="parent_id" class="form-control select2" id="parent_id" style="width: 100%">
                    <option value="0">No Parent</option>
                    <?php
                      foreach($list as $val){ 
                        echo "<option value='".$val->link_id."'>".$val->label."</option>";
                      }
                    ?>
                  </select>
                </td>
              </tr>
            </table>
          </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">
             Close
          </button>
          <button type="submit" class="btn btn-success btnSubmit" id="submit_add">SAVE</button>
        </div>
      </div>
    </form>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->