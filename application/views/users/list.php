<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Users</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Admin</a></li>
          <li class="breadcrumb-item active">Links</li>
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
            <div class="card-tools">
              <button id="add" class="btn btn-primary right">Add</button>
            </div>
          </div>
          <div class="card-body">
            <table style="width: 100%;" class="table table-striped table-bordered table-hover" id="viewresult">
                <thead>
                    <tr >
                        <th width="10%">User ID</th>
                        <th width="">Full Name</th>
                        <th width="">Username</th>
                        <th width="10%">Access</th>
                        <th style="width:100px;"><center>Action</center></th>
                    </tr>
                </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<style>
.fileUpload {
    position: relative;
    overflow: hidden;
}
.fileUpload input.upload {
    position: absolute;
    top: 0;
    right: 0;
    margin: 0;
    padding: 0;
    font-size: 20px;
    cursor: pointer;
    opacity: 0;
    filter: alpha(opacity=0);
}
</style>
<?php  $this->load->view('users/users_modal'); ?>
<?php  $this->load->view('users/users_js'); ?>