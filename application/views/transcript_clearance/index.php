<style>

</style>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Transcript Cleared Students</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Transcript Cleared Students</li>
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
                  <h3 class="card-title">Master List</h3>
                  <div class="card-tools">
                    <form id="uploadForm" enctype="multipart/form-data">
                      <div class="input-group">
                        <div class="custom-file">
                          <input type="file" name="userfile" id="userfile" size="20" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                          <label class="custom-file-label" for="userfile" id="file_label">Choose file</label>
                        </div>
                        <div class="input-group-append">
                          <span class="input-group-text" id="upload">Upload</span>
                        </div>
                      </div>
                    </form>                  
                  </div>
                </div>
                <div class="card-body table-responsive">
                  <table class="table table-striped table-bordered table-hover" id="viewresult">
                    <thead>
                      <th>Full Name</th>
                      <th>Student No.</th>
                      <th>Program</th>
                      <th>College</th>
                      <th>Date Approved</th>
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
<?php  $this->load->view('transcript_clearance/index_js'); ?>
<div id="modal-overlay" class="overlay" style="display: none;">
  <div class="loader"></div>
</div>
