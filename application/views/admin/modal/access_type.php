<?php
$id_name = "at_id";
$table = "access_type";
$labels = "Access Type";
?>

<div class="modal fade" id="modal" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel">
                  <p id="modal_title"></p>
              </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
          </div>
          
          <!-- Modal Body -->
          <div class="modal-body">
              
            <form method="POST" id="form1" class="form-horizontal" role="form">
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
                      <td><?= $labels; ?></td>
                      <td><input type="text" name="dbval" id="dbval" class="form-control uppercase" required="required"/></td>
                  </tr>
              </table>
                <!--SUBMIT BUTTON -->
              <div class="modal-footer" align="right">
                <div class="form-group">
                  <div class="col-sm-12" align="right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                       Close
                    </button>
                    <button type="submit" class="btn btn-success btnSubmit" id="submit_add">SAVE</button>
                  </div>
                </div>
               </div>
              </form>
          </div><!--end Modal body-->
      </div><!--end Modal content-->
  </div><!--end Modal dialog-->
</div><!--end modal receive-->

<div class="modal fade" id="exemption_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header alert alert-success">
        <h4 class="modal-title" id="myModalLabel">
          <p id="exemption_title"></p>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>               
<!-- Modal Body For Update -->
      <div class="modal-body"> 
        <input type="hidden" name="at_id" id="at_id">
        <div id="reg_details">
              <div class="form-row">
                <div class="col-sm-9 col-xs-9">
                  <form id="exempted_form">
                    <select name="link_id" id='links' style='width: 100%;' required="required">
                     <option value='' selected disabled>Select Client</option>
                    </select> 
                  </form>                        
                </div>
                <div class="col-sm-3 col-xs-3">
                  <button id="add_link" class="btn btn-success"><i class="fa fa-save fa-lg"></i>&nbsp; ADD</button>
                </div>
              </div>
              <div class="form-row">
                <div class="col-sm-12 col-xs-12">
                  <div class="info-box main-box table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="links_table" width="100%">
                      <thead>
                        <tr>
                          <td>Accessible Links</td>
                          <td>Action</td>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
        <!--SUBMIT BUTTON -->
          <div class="modal-footer" align="right">
            <div class="form-group">
              <div class="col-sm-12" align="right">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                  <i class="fa fa-times fa-lg"></i>&nbsp; Close
                </button>
              </div>
            </div>
          </div>
      </div><!--end Modal body-->
    </div><!--end Modal content-->
  </div><!--end Modal dialog-->
</div><!--end modal receive-->