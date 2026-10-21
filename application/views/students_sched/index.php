<style>
  input.kolorPicker {
    width: 100% !important;
    height: calc(2.25rem + 2px) !important;
    border: 1px solid #ced4da !important;
    padding: 0.375rem 0.75rem !important;
    background: #fff url(<?= base_url("assets/js/colorpicker/style/images/color-wheel.jpg")?>) no-repeat 100% 50% !important;
  }
  div.kolorpicker-palette ul li {
        height: 100% !important;
  }
  #palette-table{
    padding-top: 10px !important;
  }
  #kolorPicker{
    width: 300px !important;
    padding-top: 50px !important;
  }
  div.x-close-box {
    height: 100% !important;
  }
  div.kolorpicker-palette ul {
      margin: 0;
      padding-left: 0px !important;
  }
  .custom-file-label::after {
    cursor: pointer;
  }

.custom-file-label::before {
    border-color: #28a745;
}

.custom-file-input:disabled + .custom-file-label::after,
.custom-file-input:disabled + .custom-file-label::before {
    background-color: #6c757d; /* Gray color */
    border-color: #6c757d; /* Gray color */
    color: #fff; /* Retain original color */
}

.custom-file-label:hover::after {
    background-color: #218838;
    border-color: #218838;
    color: #fff !important;
}

.custom-file-label:hover::before {
    border-color: #218838;
}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">EQuota</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Admin</a></li>
          <li class="breadcrumb-item active">EQuota</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="loader-container">
    <div class="loader"></div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <!-- Filters -->
        <form id="form_filter">
          <div class="row">
            <div class="col-lg-3">
              <div class="form-group">
                <label>Year</label>
                <select id="year" name="year" class="select2">
                  <option value="" selected disabled>Select Academic year</option>
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
            <div class="col-lg-3">
              <div class="form-group">
                <label>Semester</label>
                <select class="select2" name="sem" id="sem">
                  <option selected disabled value="">Select Semester</option>
                  <option value="1">1st Semester</option>
                  <option value="2">2nd Semester</option>
                </select>
              </div>
            </div>
            <div class="col-lg-1">
              <div class="form-group">
                <label>&nbsp;&nbsp;</label>
                <button id="generate" class="btn btn-success btn-block" disabled>
                  <i class="fa fa-plus"></i> Create
                </button>
              </div>
            </div>
            <div class="col-lg-1">
              <div class="form-group">
                <label>&nbsp;&nbsp;</label>
                <button id="download" class="btn btn-success btn-block" disabled>
                  <i class="fa fa-download"></i> Report
                </button>
              </div>
            </div>
            <div class="col-lg-1">
              <div class="form-group">
                <label>&nbsp;&nbsp;</label>
                <button id="nf_list" class="btn btn-success btn-block" disabled>
                  <i class="fa fa-list"></i> NF List
                </button>
              </div>
            </div>
            <div class="col-lg-3" id="nf_div">
              <label>&nbsp;</label>
              <div class="input-group">
                <div class="custom-file">
                  <input type="file" name="userfile" id="userfile" size="20" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" disabled />
                  <label class="custom-file-label" for="userfile" id="file_label">Choose file</label>
                </div>
                <div class="input-group-append">
                  <span class="input-group-text btn btn-success" id="upload_nf" disabled>Upload NF</span>
                </div>
              </div>
            </div>
          </div>
        </form>
        <!--END Filters -->
        <!-- Main Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
            </h3>
            <div class="card-tools">
            </div>
          </div>
          <div class="card-body">
            <form id="form_equota">
              <table class="table table-striped table-bordered table-hover" id="viewresult">
                <thead>
                    <tr >
                      <th width="5%">DAY</th>
                      <th width="27%">Time Slot</th>
                      <th width="10%">No. of Students</th>
                      <th width="28%">Academic Program</th>
                      <th width="5%">action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php  $this->load->view('students_sched/index_js.php'); ?>
<?php  $this->load->view('students_sched/modal'); ?>
