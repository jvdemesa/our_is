<style>
hr {
    margin-top: 5px !important;
    margin-bottom: 0rem !important;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
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
<?php
  $id_maker = "";
  $id_approver = "";
  if(in_array(8, $userdata->access)){
    $id_approver="selected";
  }
  if(in_array(9, $userdata->access)){
    $id_maker="selected";
  }
?>

</style>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">ID Requests</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">ID Requests</li>
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
          <div class="col-12 col-sm-12 col-md-12">
            <div class="info-box">
              <div class="col-sm-2">
                <label>Request Status:</label>
                <select class="select2" id="status" onChange="refresh_table();">
                  <option value="" selected disabled>Select Status</option>
                  <option value="">ALL</option>
                  <option value="0" <?= $id_approver ?>>Pending Approval</option>
                  <option value="1" <?= $id_maker ?>>Approved</option>
                  <option value="2">Disapproved</option>
                  <option value="3">Release Email Sent</option>
                  <option value="4">Non-compliance Email Sent</option>
                  ?>
                </select>
              </div>
              <div class="col-sm-2">
                <label>Request Type:</label>
                <select class="select2" id="request_type" onChange="refresh_table();">
                  <option value="" selected disabled>Select Request Type</option>
                  <option value="">ALL</option>
                  <option value="1">NEW: New first year undergraduate</option>
                  <option value="2">NEW: Transferee</option>
                  <option value="3">NEW: Second degree</option>
                  <option value="4">NEW: Non-degree</option>
                  <option value="5">NEW: New Graduate Student</option>
                  <option value="6">NEW: Exchange student</option>
                  <option value="7">REPLACEMENT: Lost ID</option>
                  <option value="8">REPLACEMENT: Damaged ID</option>
                  <option value="9">REPLACEMENT: Change information</option>
                  <option value="10">REPLACEMENT: shifted to another degree program</option>
                  <option value="11">Continuing undergraduate: First Request</option>
                  <option value="12">Continuing Graduate Student: First Request</option>
                  ?>
                </select>
              </div>
              <div class="col-sm-6">
              </div>
              <div class="col-sm-2">
                <label>Set date of release:</label>
                <div class="input-group">
                  <input type="text"
                         id="release_date"
                         class="datepicker form-control"
                         placeholder="Select date of release">
                  <div class="input-group-append">
                    <span class="input-group-text" id="total_release">0</span>
                  </div>
                </div>
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
                    <div class="card-tools d-flex align-items-center gap-2">
                        <button class="btn btn-success"
                                id="refresh_requests"
                                title="Check New Requests">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </div>                
                  </div>
                <div class="card-body table-responsive">
                  <table class="table table-striped table-bordered table-hover" id="viewresult">
                    <thead>
                      <th>Student Number</th>
                      <th>Email</th>
                      <th>First Name</th>
                      <th>Middle Name</th>
                      <th>Last Name</th>
                      <th>College</th>
                      <th>Birthday</th>
                      <th>Address</th>
                      <th>Request Type</th>
                      <th>Status</th>
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
      <input type="hidden" name="id_request_id" id="id_request_id">
      <input type="hidden" name="email" id="email">
      <input type="hidden" name="fname" id="fname">
      <input type="hidden" name="lname" id="lname">
      <input type="hidden" name="mname" id="mname">
      <input type="hidden" name="college" id="college">
      <input type="hidden" name="birthday" id="birthday">
      <input type="hidden" name="address" id="address">
      <input type="hidden" name="transaction_id" id="transaction_id">
      <input type="hidden" name="rejection_count" id="rejection_count">
      <input type="hidden" name="student_number" id="student_number">
      <!-- END Section -->
    </section>
  </div>
<?php  $this->load->view('id_requests/index_js'); ?>
<?php  $this->load->view('id_requests/index_modal'); ?>
