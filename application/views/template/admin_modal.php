<!---- VIEW ALL PROPOSALS MODAL-->
<div class="modal fade" id="alert_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header bg-green">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Close</span>
        </button>
        <h4 id="status_title">
          Alert Details
        </h4>
      </div>
      <!-- Modal Body For Update -->
      <div class="modal-body">
        <input type="hidden" name="alert_id" id="alert_id">
        <div class="div_message"></div>
        <div class="modal-footer" align="right">
          <div class="form-group">
            <div class="col-sm-12" align="right">
              <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="markAsRead">
                <i class="fa fa-eye fa-lg"></i>&nbsp; Mark as Read
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Quick search modal-->
 <div class="modal modal-default fade" id="quick_search_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header bg-blue" id="header">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Close</span>
        </button>
        <h4 class="modal-title">
         Quick Search
        </h4>
      </div>
      <input type="hidden" id="quick_search_dtsno">
      <!-- Modal Body For Update -->
      <div class="modal-body" id="quick_search_body">
      </div><!-- End modal body -->
      <div class="modal-footer bg-blue" align="right" id="obr_footer">
        <button type="button" class="btn btn-outline" data-dismiss="modal">
          <i class="fa fa-times fa-lg"></i>&nbsp; CLOSE
        </button>
        <button type="submit" class="btn btn-outline" id="view_quick_search">
          <i class="fa fa-eye fa-lg"></i> View
        </button>
      </div>  
    </div>
  </div>
</div>
<div class="modal fade" id="user_profile_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header modal-header-success">
          <button type="button" class="close" 
             data-dismiss="modal">
                 <span aria-hidden="true">&times;</span>
                 <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title" >
              Update User Information
          </h4>
      </div>
        
        <!-- Modal Body -->
      <div class="modal-body">
        <form method="POST" id="user_profile_form" class="form-horizontal" role="form" action="<?php echo base_url()."users/edit_user_profile"; ?>">
          <input type="hidden" name="uid" id="up-uid">
          <input type="hidden" name="display_pic" id="up-display_pic">
          <div class="table table-responsive">
          <table class="table table-bordered table-hover">
            <tr>
              <td colspan="2">
                First Name: <input type="text" name="fname" id="up-fname" class="form-control uppercase" required="required"/>
              </td>
              <td colspan="2">Last Name: <input type="text" name="lname" id="up-lname" class="form-control uppercase" required="required"/></td>
              <td colspan="2">Middle Name:<input type="text" name="mname" id="up-mname" class="form-control uppercase" required="required"/></td>
            </tr>
            <tr>
              <td colspan="2">username: </td>
                <td colspan="2">
                  <input type="text" disabled value="<?php echo $userdata->username ?>" class="form-control" required="required"/>
                </td>
                <td width="30%" rowspan="7" >
                  <p>Select Image: <input type="file" id="up-images" class="form-control btn btn-primary"></p>
                  <div id="user_profile_croppie_div">
                    <div class="col-sm-12" id="up-upload-image"></div>
                    <center>
                      <button type="button" id="up-rotateLeft" data-rotate="90" class="btn btn-success">Rotate Left</button>
                      <button type="button" id="up-rotateRight" data-rotate="-90" class="btn btn-success">Rotate Right</button>
                    </center>
                  </div>
                  <center>
                    <div id="up-current_dp"></div>
                  </center>
                </td>
              </tr>
              <tr>
                  <td>Password: </td>
                  <td colspan="2"><input type="password" name="password" class="form-control"/></td>
              </tr>
              <tr>
                <td>Signature: </td>
                  <td colspan="2">
                    <p>
                      <input type="file" class="upload up" id="up-signature" name="signature" accept="image/png, image/gif, image/jpeg" />
                    </p>
                  </td>
                
              </tr>
              
           </table>
         </div>
            <!--SUBMIT BUTTON -->
          <div class="modal-footer" align="right">
            <div class="form-group">
              <div class="col-sm-12" align="right">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                   Close
                </button>
                <button type="submit" class="btn btn-success" id="up-submit_add">Save</button>
              </div>
            </div>
           </div>
        </form>
      </div><!--end Modal body-->
    </div><!--end Modal content-->
  </div><!--end Modal dialog-->
</div><!--end modal receive-->
<!---- UPDATE PASSWORD-->
<div class="modal fade" id="pw_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header bg-green">
                <h4 id="status_title">
          Update Password
        </h4>

        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Close</span>
        </button>
      </div>
      <!-- Modal Body For Update -->
      <div class="modal-body">
        <div class="form-group row">
          <label for="curr_pass">
            <i class="btn btn-success btn-xs fa fa-eye tool_tip" id="curr_pass_view" title="view"></i>
            Current Password: 
          </label>
            <input type="password" class="form-control" name="curr_pass" id="curr_pass" placeholder="Current Password" required="required">
            <p id="curr_error">
              <i class="far fa-times-circle"></i>
              Incorrect Current Password
            </p>
        </div>
        <div class="form-group row">
          <label for="new_pass">
            <i class="btn btn-success btn-xs fa fa-eye tool_tip" id="new_pass_view" title="view"></i>
            New Password: 
          </label>
            <input type="password" class="form-control" name="new_pass" id="new_pass" placeholder="New Password" required="required">
        </div>
        <div class="form-group row">
          <label for="confirm_pass">
            <i class="btn btn-success btn-xs fa fa-eye tool_tip" id="confirm_pass_view" title="view"></i>
            Confirm Password: 
          </label>
            <input type="password" class="form-control" name="confirm_pass" id="confirm_pass" placeholder="Confirm Password" required="required">
            <p id="confirm_error">
              <i class="far fa-times-circle"></i>
              New password did not match
            </p>
        </div>
        <div class="modal-footer" align="right">
          <div class="form-group">
            <div class="col-sm-12" align="right">
              <button type="button" class="btn btn-success" id="save_pass">
                <i class="fa fa-save fa-lg"></i>&nbsp; Update
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>