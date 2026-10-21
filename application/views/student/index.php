<style>

</style>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Students</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Students</li>
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
                  <h3 class="card-title">Batch <?= $batch_id ?></h3>
                  <div class="card-tools">
                  </div>
                </div>
                <div class="card-body table-responsive">
                  <table class="table table-striped table-bordered table-hover" id="viewresult">
                    <thead>
                      <th>SAIS ID</th>
                      <th>Campus ID</th>
                      <th>Last Name</th>
                      <th>First Name</th>
                      <th>Middle Name</th>
                      <th>Classification</th>
                      <th>Degree</th>
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
<script src="<?= base_url(); ?>assets/js/chart.js"></script>
<script src='<?php echo base_url(); ?>assets/js/fullcalendar.min.js'></script>
<?php  $this->load->view('student/index_js'); ?>
