<?php
$id_name = "link_id";
$table = "link";
$labels = "Link";
?>

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
            <h3 class="card-title"><?= $this->session->flashdata('message') ?></h3>
            <div class="card-tools">
              <button id="add" class="swalDefaultSuccess btn btn-primary right">Add</button>
            </div>
          </div>
          <div class="card-body">
              <table class="table table-striped table-bordered table-hover" id="viewresult">
              <thead>
                  <tr >
                    <th width="20%"><?= $labels; ?> ID</th>
                    <th width="40%"><?= $labels; ?></th>
                    <th width="10%">Status</th>
                    <th width="5%">action</th>
                  </tr>
              </thead>
              <?php 
              if($list != false){
                  foreach($list as $value){ 
              ?>
              <tr>
                  <td><?php echo $value->{$id_name}; ?></td>
                  <td><?php echo $value->label; ?></td>
                  <td><?php echo ($value->status=='0')?'ENABLED':'DISABLED'; ?></td>
                  <td>
                    <center>
                      <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-sm edit_modal"title="edit"
                        id="<?php echo $value->{$id_name}; ?>" 
                        dbval="<?php echo $value->{$table}; ?>" 
                        status="<?php echo $value->status; ?>" 
                        icon="<?php echo $value->icon; ?>" 
                        label="<?php echo $value->label; ?>" 
                        multiple="<?php echo $value->multiple; ?>" 
                        parent_id="<?php echo $value->parent_id; ?>" 
                        arrangement="<?php echo $value->arrangement; ?>" 
                        >
                          <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm delete_modal" title="delete" 
                        id="<?php echo $value->{$id_name}; ?>" 
                        dbval="<?php echo $value->{$table}; ?>" 
                        status="<?php echo $value->status; ?>" 
                        icon="<?php echo $value->icon; ?>" 
                        label="<?php echo $value->label; ?>" 
                        multiple="<?php echo $value->multiple; ?>" 
                        parent_id="<?php echo $value->parent_id; ?>" 
                        >
                          <i class="fa fa-trash"></i>
                        </button>
                      </div> 
                    </center>
                  </td>
              </tr>
              <?php 
                  }
              }
              ?>
          </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php  $this->load->view('admin/jquery/link'); ?>
<?php  $this->load->view('admin/modal/link'); ?>