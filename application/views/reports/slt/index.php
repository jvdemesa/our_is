<style>
    .badge {
        white-space: normal;
    }
</style>

<div class="content-wrapper" style="min-height: 414px;">

    <section class="content-header">
        <h1 class="page-header">
        <i class="fa fa-address-card-o"></i> Stream lining for TEV
        <small></small>
        </h1>
        <ol class="breadcrumb">
        <li class="active">Reports</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
        <br>   
            <div class="box-body">
              <div class="col-lg-12 col-md-12">

              <div class="box box-default">
                    <div class="box-body">
                        <div class="col-md-3">
                            <input type='select_dtsno' placeholder='DTS Number' class='form-control' name='select_dtsno' id='select_dtsno'/>
                        </div>
                      

                        <div class="col-md-1">
                            <button class="btn btn-primary" id="filter">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="box box-success" id="details_box">
                  <div class="box-header  with-border">
                      <h3 class="box-title" id="document_list-title">Documents</h3>
                  </div>
                  <div class="box-body" id="document_list" style="overflow-x:auto;">
                    <table class="table table-responsive table-bordered table-striped table-hover"   id="document_list_table">
                      <thead>
                        <tr>
                          <td style='width=150px;' id="first_row">Name of Office (CENRO, PENRO)</td>
                          <td style='width=150px;'>Center</td>
                          <td style='width=150px;'>Name of Client</td>
                          <td style='width=500px;'>Application / Control No</td>
                          <td style='width=500px;'>DTS No</td>
                          <td style='width=500px;'>Classification</td>
                          <td style='width=500px;'>Date & Time of Applications w/ complete requirements received</td>
                          <td style='width=500px;'>Hour. & Mins.</td>
                          <td style='width=500px;'>Primary Fees</td>
                          <td style='width=500px;'>Date Issued</td>
                          <td style='width=500px;'>Date received by client</td>
                          <td style='width=500px;'>No. of Days</td>
                          <td style='width=500px;'>No. of Hours & mins</td>
                          <td style='width=500px;'>Remarks</td>
                        </tr>
                      </thead>
                      <tbody id="tbody">
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              
            </div>
      </div>
    </section>
</div>
<?php  $this->load->view('reports/slt/index_js'); ?>
<?php  $this->load->view('reports/slt/index_modal'); ?>
