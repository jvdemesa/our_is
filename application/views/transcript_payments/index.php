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
</style>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Transcript Payments</h1>
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
              <div class="col-sm-3">
                <select class="select2" id="select_status" onChange="refresh_table();">
                  <option value="" selected disabled>Select Payment Status</option>
                  <option value="0">No matching request</option>
                  <option value="1">With matching Request</option>
                  ?>
                </select>
              </div>
              <div class="col-sm-3">
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
                  <div class="card-tools">
                  </div>
                </div>
                <div class="card-body table-responsive">
                  <table class="table table-striped table-bordered table-hover" id="viewresult">
                    <thead>
                      <th>Transaction Code</th>
                      <th>Referrence Number</th>
                      <th>Date Uploaded</th>
                      <th>Requestor Name</th>
                      <th>Email</th>
                      <th>file</th>
                      <th>Status</th>
                      <th>is Matched?</th>
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
<?php  $this->load->view('transcript_payments/index_js'); ?>
<?php  $this->load->view('transcript_payments/index_modal'); ?>
