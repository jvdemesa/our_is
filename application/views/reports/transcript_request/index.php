<style>
hr {
    margin-top: 0rem !important;
    margin-bottom: 0rem !important;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
}
.red{
  color: red;
  margin-bottom: 0px !important;
  margin-top: 0px !important;
}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Time Analysis</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Admin</a></li>
          <li class="breadcrumb-item active">Time Analysis</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <form id="form_filter">
          <div class="row">
            <div class="col-lg-3">
              <div class="form-group">
                <label for="request_type">Request Type:</label>
                <select id="request_type" name="request_type[]" class="select2" multiple>
                  <?php
                  // Generate options for the select element
                  foreach ($request_type as $key=>$val) {
                      echo "<option value='$val->id'>$val->request</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-lg-3">
              <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status[]" class="select2" multiple>
                  <option value="0">New Request</option>
                  <option value="1">Assessed</option>
                  <option value="2">Assessment Sent</option>
                  <option value="3">Proof of payment received</option>
                  <option value="4">Payment verified - On Process</option>
                  <option value="5">For release</option>
                  <option value="6">Released</option>
                </select>
              </div>
            </div>
            <div class="col-lg-2">
              <div class="form-group">
                <label for="uid">Served By:</label>
                <select id="uid" name="uid" class="select2">
                  <option value="" selected disabled>Select User</option>
                  <option value="0">ALL</option>
                  <?php
                  // Generate options for the select element
                  foreach ($served_by as $key=>$val) {
                      echo "<option value='$val->uid'>$val->first_name $val->last_name </option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-lg-2">
              <div class="form-group">
                <label for="queue_date">Degree Program:</label>
                <input type="text" name="degree" id="degree" class="form-control">
              </div>
            </div>
            <div class="col-lg-2">
              <div class="form-group">
                <label for="queue_date">Request date:</label>
                <input type="text" name="request_date" id="request_date" class="form-control daterange" readonly>
              </div>
            </div>
<!--             <div class="col-lg-1">
              <div class="form-group">
                <label for="filter">
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </label>
                <button id="filter" class="btn btn-info">Filter</button>
              </div>
            </div>
 -->          </div>
        </form>
        <div class="card">
          <div class="card-header">
            List
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover" id="viewresult" style="width:100%">
                <thead>
                  <th width="10%">Transaction Code</th>
                  <th width="10%">Student Number</th>
                  <th width="10%">Client Name</th>
                  <th width="10%">Assigned To</th>
                  <th width="10%">Degree Program</th>
                  <th width="12%">Request Type</th>
                  <th width="3%">Q</th>
                  <th width="10%">Request date</th>
                  <th width="10%">Assessment date</th>
                  <th width="10%">Payment date</th>
                  <th width="10%">Payment Confirmation</th>
                  <th width="10%">Ready Date</th>
                  <th width="10%">status</th>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile">
            <h3 class="profile-username text-center">Assesment Time</h3>
            <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
            <b>Minimum Assessment Time</b> <a class="float-right" id="assessment_min_waiting_time"></a>
            </li>
            <li class="list-group-item">
            <b>Maximum Assessment Time</b> <a class="float-right" id="assessment_max_waiting_time"></a>
            </li>
            <li class="list-group-item">
            <b>Average Assessment Time</b> <a class="float-right" id="assessment_avg_waiting_time"></a>
            </li>
            </ul>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile">
            <h3 class="profile-username text-center">Payment to Ready Time</h3>
            <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
            <b>Minimum Ready Time</b> <a class="float-right" id="pay_min"></a>
            </li>
            <li class="list-group-item">
            <b>Maximum Ready Time</b> <a class="float-right" id="pay_max"></a>
            </li>
            <li class="list-group-item">
            <b>Average Ready Time</b> <a class="float-right" id="pay_avg"></a>
            </li>
            </ul>
          </div>
        </div>
      </div>
  
      <div class="col-md-4">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile">
             <canvas id="paidvsunpaid" style="max-height: 200px"></canvas>
          </div>
        </div>
      </div>

      <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-header">
            <div class="card-tools">
              <button class="btn btn-success btn-sm" id="download_chart"><i class="fa fa-download"></i> Download Chart</button>
            </div>
          </div>
          <div class="card-body box-profile">
             <canvas id="daily_graph" style="max-height: 400px"></canvas>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</div>
<script src="<?= base_url(); ?>assets/js/chart.js"></script>
<?php  $this->load->view('reports/transcript_request/index_js'); ?>
<?php  $this->load->view('reports/transcript_request/index_modal'); ?>
