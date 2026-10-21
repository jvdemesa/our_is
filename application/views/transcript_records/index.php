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
            <h1 class="m-0">Transcript Records</h1>
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
<!--                     <button class="btn btn-warning" id="refresh_payments" title="Check New Payments"><i class="fa fa-credit-card"></i></button>
                    <button class="btn btn-success" id="refresh_requests" title="Check New Requests"><i class="fa fa-refresh"></i> </button>
 -->                  </div>
                </div>
                <div class="card-body table-responsive">
                  <table class="table table-striped table-bordered table-hover" id="viewresult">
                    <thead>
                      <th>ID</th>
                      <th>Fullname</th>
                      <th>Filename</th>
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
<?php  $this->load->view('transcript_records/index_js'); ?>
<?php  $this->load->view('transcript_records/index_modal'); ?>
<div id="modal-overlay" class="overlay" style="display: none;">
  <div class="loader"></div>
</div>
