<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Regist</h1>
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
<!--               <div class="form-group">
                <label>&nbsp;&nbsp;</label>
                <button id="download" class="btn btn-success btn-block" disabled>
                  <i class="fa fa-download"></i> Report
                </button>
              </div>
 -->            </div>
            <div class="col-lg-1">
<!--               <div class="form-group">
                <label>&nbsp;&nbsp;</label>
                <button id="nf_list" class="btn btn-success btn-block" disabled>
                  <i class="fa fa-list"></i> NF List
                </button>
              </div>
 -->            </div>
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
                      <th width="5%">ID</th>
                      <th width="15%">Run By</th>
                      <th width="10%">Enlisted</th>
                      <th width="28%">Not Enlisted</th>
                      <th width="10%">Used Slots</th>
                      <th width="5%">Unused Slots</th>
                      <th width="10%">Total Slots</th>
                      <th width="15%">action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="8">
                    </td>
                  </tr>
                </tfoot>
              </table>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php  $this->load->view('regist/index_js.php'); ?>
<?php  $this->load->view('regist/index_modal.php'); ?>
