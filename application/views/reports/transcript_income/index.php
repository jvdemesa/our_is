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
            <h1 class="m-0">Transcript Income</h1>
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
            <form id="form_filter">
              <div class="row">
                <div class="col-lg-3">
                  <div class="form-group">
                    <label for="request_id">Request:</label>
                    <select id="request_id" name="request_id" class="select2">
                      <option value="" selected disabled>Select Section</option>
                      <option value="">All</option>
                      <?php
                      // Generate options for the select element
                      foreach ($requests as $key=>$val) {
                          echo "<option value='$val->id'>$val->request</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-lg-3">
                  <div class="form-group">
                    <label for="queue_date">Request Date:</label>
                    <input type="text" name="request_date" id="request_date" class="form-control daterange" readonly>
                  </div>
                </div>
                <div class="col-lg-3">
                </div>
              </div>
            </form>
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
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped table-bordered table-hover" id="viewresult" style="width:100%">
                        <thead>
                          <th width="10%">Transaction Code</th>
                          <th width="15%">Requestor Name</th>
                          <th width="20%">Request</th>
                          <th width="10%">Release Date</th>
                          <th width="10%">Release To</th>
                          <th width="7%">Release By</th>
                          <th width="7%">Quantity</th>
                          <th width="7%">Pages</th>
                          <th width="9%">Price per page</th>
                          <th width="15%">Total Price</th>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8"></td>
                                <td><span class="pull-right text-danger">Total Income</span></td>
                                <td id="total_income"></td>
                            </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- END Section -->
    </section>
  </div>
<?php  $this->load->view('reports/transcript_income/index_js'); ?>
<?php  $this->load->view('reports/transcript_income/index_modal'); ?>
