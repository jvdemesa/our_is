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
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0"><?= $labels; ?></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Admin</a></li>
          <li class="breadcrumb-item active">Services</li>
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
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><?= $this->session->flashdata('message') ?></h3>
            <div class="card-tools">
              <button id="add" class="swalDefaultSuccess btn btn-primary right">Add</button>
            </div>
          </div>
          <div class="card-body">
            <table class="table table-striped table-bordered table-hover" id="viewresult">
              <thead>
                  <tr >
                    <th width="5%">ID</th>
                    <th width="50%">College name</th>
                    <th width="20%">Short Name / Code</th>
                    <th width="10%">Status</th>
                    <th width="5%">action</th>
                  </tr>
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
</div>
<?php  $this->load->view('admin/jquery/college'); ?>
<?php  $this->load->view('admin/modal/college'); ?>