<style>
hr {
    margin-top: 5px !important;
    margin-bottom: 0rem !important;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
}
.modal-body{
  min-height: 600px; 
  max-height: 600px; 
  overflow-y: auto;
  overflow-x: none;
}
iframe {
  width: 100%;
  height: 100%;
  min-height: 500px; 
  max-height: 500px; 
  border: none;
}
#contentFrame {
    width: 100% !important;
    border: none;
}
#payment_modal{
  min-height: 700px !important; 
  max-height: 700px !important; 
  overflow-y: auto;
  overflow-x: none;
}
.pointer{
  cursor: pointer;
}
.selected {
  background-color: #8D1436 !important; /* Highlight color for selected rows */
  color: white !important;
}
 @media print {
  @page {
      size: A4;
      margin: 20mm;
  }
  body {
      margin: 0;
      font-family: Arial, sans-serif;
  }
  .print-content {
      width: 100%;
      page-break-after: always;
  }
}
</style>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Transcript Requests</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Transcript Requests</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">

          <div class="col-12 col-sm-6 col-md-3 pointer" id="new_request_3_days">
            <div class="info-box">
              <span class="info-box-icon uplb-bg-yellow elevation-1"><i class="fa fa-window-close-o"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">New request more than 3 days</span>
                <span class="info-box-number" id="box1">
                  0
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>

          <div class="col-12 col-sm-6 col-md-3 pointer" id="payment_3_days">
            <div class="info-box">
              <span class="info-box-icon uplb-bg-red elevation-1"><i class="fas fa-money"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Unverified payments more than 3 days</span>
                <span class="info-box-number" id="box2">
                  0
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>

          <div class="col-12 col-sm-6 col-md-3 pointer" id="new_request_3_days">
            <div class="info-box">
              <span class="info-box-icon uplb-bg-green elevation-1"><i class="fa fa-list-alt"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Request Last Update</span>
                <span class="info-box-number" id="request_last_update">
                  
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>

          <div class="col-12 col-sm-6 col-md-3 pointer" id="new_request_3_days">
            <div class="info-box">
              <span class="info-box-icon uplb-bg-green elevation-1"><i class="fa fa-money"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Payment Last Update</span>
                <span class="info-box-number" id="payment_last_update">
                  
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>

          <div class="col-12 col-sm-12 col-md-12">
            <div class="info-box">
              <div class="col-sm-3">
                <select class="select2" id="assigned_to" onChange="refresh_table();">
                  <option value="" selected disabled>Select Assigned To</option>
                  <option value="">ALL</option>
                  <?php
                  foreach($assigned_to as $val){
                    $selected = $val->uid==$userdata->uid?"selected":"";
                    echo "<option value='$val->uid' $selected>$val->first_name $val->last_name</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-sm-2">
                <select class="select2" id="select_status" onChange="refresh_table();">
                  <option value="" selected disabled>Select Status</option>
                  <option value="">ALL</option>
                  <option value="0">New Request</option>
                  <option value="1">Assessed</option>
                  <option value="2">Assessment Sent</option>
                  <option value="3">Payment received</option>
                  <option value="4">On-Process</option>
                  <option value="5">For Release</option>
                  <option value="6">Released</option>
                  ?>
                </select>
              </div>
              <div class="col-sm-2">
                <select class="select2" id="active_status" onChange="refresh_table();">
                  <option value="" selected disabled>Select active_status</option>
                  <option value="">ALL</option>
                  <option value="0" selected>Active / Enable</option>
                  <option value="1">On-Hold</option>
                  <option value="2">Inactive / Disabled</option>
                </select>
              </div>
              <div class="col-sm-1">
                <div class="custom-control custom-checkbox">
                  <input class="custom-control-input custom-control-input-danger" type="checkbox" id="is3days" name="is3days" value="0">
                  <label for="is3days" class="custom-control-label">Show > 3Days only?</label>
                </div>
              </div>              
              <div class="col-sm-1">
                <button class="btn btn-success" id="clear_filters">Clear Filter</button>
              </div>
              <div class="col-sm-2">
              </div>              
            </div>
            <!-- /.info-box -->
          </div>
        </div>
      </div>
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <!-- Next Div -->
            <div class="col-lg-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Master List</h3>
                  <div class="card-tools">
                    <button class="btn btn-warning" id="refresh_payments" title="Check New Payments"><i class="fa fa-credit-card"></i></button>
                    <button class="btn btn-success" id="refresh_requests" title="Check New Requests"><i class="fa fa-refresh"></i> </button>
                  </div>
                </div>
                <div class="card-body table-responsive">
                  <table class="table table-striped table-bordered table-hover" id="viewresult">
                    <thead>
                      <th>TransCode</th>
                      <th>Student #</th>
                      <th>Request Date</th>
                      <th>Payment Date</th>
                      <th>First Name</th>
                      <th>Middle Name</th>
                      <th>Last Name</th>
                      <th>Email</th>
                      <th>Request List</th>
                      <th>Assigned To</th>
                      <th>Status</th>
                      <th width="2%"><i class="fa fa-print"></i></th>
                      <th width="75px">Action</th>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END Section -->
    </section>
  </div>
<?php  $this->load->view('transcript_request/index_js'); ?>
<?php  $this->load->view('transcript_request/index_modal'); ?>
