<div class="modal fade" id="details_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-green">
        <h4 class="modal-title" id="modal_title">Request Details (<span id="trans_code_display" class="uplb-yellow"></span>) - <span id="tr_status_display"></span></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="form1" class="form-horizontal" role="form">
        <input type="hidden" name="trd_id" id="trd_id">
        <input type="hidden" name="status" id="status">
        <input type="hidden" name="grand_total" id="grand_total">
        <input type="hidden" name="email" id="email">
        <input type="hidden" name="trans_code" id="trans_code">
        <input type="hidden" name="file_link" id="file_link">
        <input type="hidden" name="staff_name" id="staff_name">
        <div class="modal-body modal-body-1st_layer">
          <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
              <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="step1-tab" data-toggle="pill" href="#step1" role="tab" aria-controls="step1" aria-selected="true">Client Details</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="step2-tab" data-toggle="pill" href="#step2" role="tab" aria-controls="step2" aria-selected="false">Academic Information</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="step3-tab" data-toggle="pill" href="#step3" role="tab" aria-controls="step3" aria-selected="false">Schools Attended</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="step4-tab" data-toggle="pill" href="#step4" role="tab" aria-controls="step4" aria-selected="false">Request List</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="step5-tab" data-toggle="pill" href="#step5" role="tab" aria-controls="step5" aria-selected="false">Uploaded Files</a>
                </li>
              </ul>
            </div>
            <div class="tab-content" id="pills-tabContent">
              <!-- STEP 1 -->
              <div class="tab-pane fade show active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <label>First Name</label>
                      <p id="fname"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Middle Name</label>
                      <p id="mname"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Last Name</label>
                      <p id="lname"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Sex at birth</label>
                      <p id="sex"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Student Number</label>
                      <p id="student_no"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Email Address</label>
                      <p id="email_display"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Date of Birth</label>
                      <p id="birthday"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Place of Birth</label>
                      <p id="pob"></p>
                    </div>
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                      <label>Father's Name</label>
                      <p id="fathers_name"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Mother's Maiden Name</label>
                      <p id="mothers_name"></p>
                    </div>
                    <div class="col-md-4">
                      <label>OSAM Cleared Date</label>
                      <p id="cleared_date"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Assigned to</label>
                      <p id="staff_name_display"></p>
                    </div>
                    <div class="col-md-12">
                      <p id="dup_note" class="text-danger"></p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <label>Sem Admitted</label>
                      <p id="sem_admitted"></p>
                    </div>
                    <div class="col-md-4">
                      <label>AY Admitted</label>
                      <p id="ay_admitted"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Admitted as</label>
                      <p id="admitted_as"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Last semester / summer enrolled</label>
                      <p id="sem_last_enrolled"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Last academic year enrolled</label>
                      <p id="ay_last_enrolled"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Degree(s) / Title(s) earned</label>
                      <p id="degree"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Status</label>
                      <p id="student_status"></p>
                    </div>
                    <div class="col-md-4">
                      <label>Cross-registered in other UP Units?</label>
                      <p id="cross_reg"></p>
                    </div>
                    <div class="col-md-4">
                      <label>UP Campus</label>
                      <p id="up_campus"></p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="step3-tab">
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th></th>
                          <th>School Name</th>
                          <th>Location</th>
                          <th>Course</th>
                          <th>Date of Graduation</th>
                        </tr>
                      </thead>
                      <tbody id="schools_tbody">
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab">
                <div class="card-body">
                  <div class="table-responsive">
                    OTR Purpose: <span id="otr_purpose" class="uplb-red"></span>
                    <button class="btn btn-primary btn-sm pull-right" id="add_request"><i class="fa fa-plus-circle"></i> Add Request</button>
                    <table class="table table-striped table-bordered" id="request_table">
                      <thead>
                        <tr>
                          <th width="45%">
                            <div class="custom-control custom-checkbox">
                              <input class="custom-control-input custom-control-input-danger" type="checkbox" id="check_all">
                              <label for="check_all" class="custom-control-label">
                                Request
                              </label>
                            </div>
                          </th>
                          <th width="10%">Price</th>
                          <th width="10%">Quantity</th>
                          <th width="10%">Pages</th>
                          <th width="15%">Total</th>
                          <th width="10%">Status</th>
                        </tr>
                      </thead>
                      <tbody id="requests_tbody">
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="4" style="text-align: right;">
                            Grand Total:
                          </td>
                          <td colspan="2" id="grand_total_display" class="uplb-red"></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane fade" id="step5" role="tabpanel" aria-labelledby="step5-tab">
                <div class="row">
                  <div class="col-md-3" id="uploads_list">
                  </div>
                  <div class="col-md-9">
                    <iframe id="contentFrame" name="contentFrame" ></iframe>
                  </div>
                </div>
              </div>

            </div><!-- END TAB CONTENT -->
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">
             Close
          </button>
          <div class="btn btn-group">
            <button class="btn btn-primary" id="print_btn"><i class="fa fa-print"></i> PRINT</button>
            <button class="btn btn-info" id="update_btn"><i class="fa fa-save"></i> UPDATE</button>
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
        <button class="btn btn-danger" id="confirm_payment_btn"><i class="fa fa-check"></i> Verify Payment</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="hold_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h4 class="modal-title" id="modal_title">Hold Status</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="card-body">
          <input type="hidden" name="hold_trans_code" id="hold_trans_code">
          <div class="form-group">
            <label for="hold_status">Active Status</label>
            <select id="active_status_option" name="active_status_option" class="select2">
              <option value="0">Active / Enable</option>
              <option value="1">On-Hold</option>
              <option value="2">In-Active / Disabled</option>
            </select>
          </div>
          <div class="form-group">
            <label for="hold_remarkds">Remarks</label>
            <textarea id="hold_remarks" name="hold_remarks" class="form-control"></textarea>
          </div>
          <hr>
          <table class="table table-striped table-bordered">
            <thead>
              <th width="20%">Active Status</th>
              <th width="40%">Remarks</th>
              <th width="20%">Updated by</th>
              <th width="20%">Date Added</th>
            </thead>
            <tbody id="hold_tbody">
            </tbody> 
          </table>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
        <button class="btn btn-warning" id="confirm_active_status_btn"><i class="fa fa-save"></i> Update</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="add_request_modal" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title" id="modal_title">Add Request</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 250px !important">
        <div class="card-body">
          <div class="form-group">
            <label for="hold_status">Request</label>
            <select id="request_id" name="request_id" class="select2" required>
              <option value="" selected disabled>Select Request Type</option>
              <?php 
              foreach($request_lists as $key=>$val){
              ?>
                <option value="<?= $val->id ?>"><?= $val->request."(Php ".$val->price.")" ?></option>
              <?php 
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="hold_remarkds">Quantity</label>
            <input type="Number" value="1" id="request_quantity" name="request_quantity" class="form-control" required>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
        <button class="btn btn-primary" id="confirm_add_request"><i class="fa fa-save"></i> Confirm</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<div class="modal fade" id="lname_modal" aria-labelledby="exampleModalLabel" aria-hidden="true" >
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title" id="modal_title_lname"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 250px !important">
        <div class="card-body">
          <table id="table_lname" class="table table-striped table-bordered" style="width: 100% !important">
            <thead>
              <th>fullname</th>
              <th>filename</th>
            </thead>
          </table>
        </div>
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

<div class="modal fade" id="transfer_modal" aria-labelledby="exampleModalLabel" aria-hidden="true" >
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title" id="transfer_modal_title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 250px !important">
        <div class="card-body">
          <input type="hidden" id="transfer_trans_code">
          <input type="hidden" id="transfer_tr_id">
          <label for="transfer_to_id">Transfer From</label>
          <p id="transfer_from"></p>
          <label for="transfer_to_id">Transfer To</label>
          <select name="assign_to_id" id="assign_to_id" class="form-control select2">
            <option value="">Select User</option>
            <?php foreach($assigned_to as $user){
              echo "<option value='$user->uid'>{$user->first_name} {$user->last_name}</option>";
            } ?>
          </select>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
        <button class="btn btn-primary" id="transfer_btn"><i class="fa fa-save"></i>Confirm</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->