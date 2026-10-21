<div class="modal fade" id="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal_title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="form1" class="form-horizontal" role="form" action="<?php echo base_url()."users/add_users"; ?>">
        <input type="hidden" name="uid" id="uid">
        <div class="modal-body">
          <div class="form-group row">
            <label for="first_name" class="col-sm-3 col-form-label">First Name: </label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name" required="required">
            </div>
          </div>
          <div class="form-group row">
            <label for="middle_name" class="col-sm-3 col-form-label">Middle Name: </label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="middle_name" id="middle_name" placeholder="Middle Name">
            </div>
          </div>
          <div class="form-group row">
            <label for="last_name" class="col-sm-3 col-form-label">Last Name: </label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="last_name" id="last_name" placeholder="last Name" required="required">
            </div>
          </div>
          <div class="form-group row">
            <label for="username" class="col-sm-3 col-form-label">User Name: </label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="username" id="username" placeholder="User Name" required="required">
            </div>
          </div>
          <div class="form-group row">
            <label for="email" class="col-sm-3 col-form-label">E-Mail: </label>
            <div class="col-sm-9">
              <input type="email" class="form-control" name="email" id="email" placeholder="E-Mail" required="required">
            </div>
          </div>
          <div class="form-group row">
            <label for="mobile_number" class="col-sm-3 col-form-label">Mobile Number: </label>
            <div class="col-sm-9">
              <input type="number" class="form-control" name="mobile_number" id="mobile_number" placeholder="Mobile Number" required="required">
            </div>
          </div>
          <div class="form-group row">
            <label for="section_id" class="col-sm-3 col-form-label">Section: </label>
            <div class="col-sm-9">
              <select name="section_id" id="section_id" class="form-control select2" style="width: 100%" required="required">
                <?php foreach($section_list as $val){
                  echo "<option value='".$val->id."'>".$val->section."</option>";
                }?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="on_leave" class="col-sm-3 col-form-label">On Leave? </label>
            <div class="col-sm-9">
              <select name="on_leave" id="on_leave" class="form-control select2" style="width: 100%" required="required">
                <option value="0">No</option>
                <option value="1">Yes</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="access" class="col-sm-3 col-form-label">Access: </label>
            <div class="col-sm-9">
              <select name="access[]" id="access" class="form-control select2"  multiple="multiple" style="width: 100%" required="required">
                <?php foreach($at_list as $val){
                  echo "<option value='".$val->at_id."'>".$val->access_type."</option>";
                }?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="home_page" class="col-sm-3 col-form-label">Home Page: </label>
            <div class="col-sm-9">
              <select name="home_page" id="home_page" class="form-control select2" style="width: 100%"  required="required">
                <option value='' selected disabled>Select Home Page</option>
                <?php foreach($link_list as $val){
                  echo "<option value='".$val->link_id."'>".$val->link."</option>";
                }?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">
             Close
          </button>
          <button type="submit" class="btn btn-success btnSubmit" id="submit_add">SAVE</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="reset_pw_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header modal-header-success">
          <h4 class="modal-title" id="myModalLabel">
              Password Reset
          </h4>
          <button type="button" class="close" 
             data-dismiss="modal">
                 <span aria-hidden="true">&times;</span>
                 <span class="sr-only">Close</span>
          </button>
      </div>
        
        <!-- Modal Body -->
      <div class="modal-body">
        <div id="pw_body">
        </div>
            <!--SUBMIT BUTTON -->
          <div class="modal-footer" align="right">
            <div class="form-group">
              <div class="col-sm-12" align="right">
                <button type="button" class="btn btn-success" id="copy_pw">Copy and Close</button>
              </div>
            </div>
           </div>
        </form>
      </div><!--end Modal body-->
    </div><!--end Modal content-->
  </div><!--end Modal dialog-->
</div><!--end modal receive-->