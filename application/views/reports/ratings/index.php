<style>
    .badge {
        white-space: normal;
    }
    .red{
        color: red;
        font-size: 12pt;
        font-weight: bold;
    }
</style>

<div class="content-wrapper" style="min-height: 414px;">

    <section class="content-header">
        <h1 class="page-header">
        <i class="fa fa-star"></i> Ratings
        <small></small>
        </h1>
        <ol class="breadcrumb">
        <li class="">Reports</li>
        <li class="active">Ratings</li>
        </ol>
    </section>

    <section class="content">
       

        <div class="row">
        <br>   
            <div class="box-body">
              <div class="col-lg-12 col-md-12">

                <div class="box box-default <?=$display_division?>">
                    <div class="box-body">
                        <div class="col-lg-1  pull-right">
                            <button class="btn btn-primary" id="filter">Filter</button>
                        </div>
                        <div class="col-lg-3  pull-right">
                            <div id="div_unit"> 
                                <input type="text" name="date_created" id="date_created" class="daterange form-control" placeholder="Date Created">
                            </div>
                        </div>
                        <div class="col-lg-2  pull-right">
                        </div>
                    </div>
                </div>
                <div class="box box-success" id="details_box">
                  <div class="box-header  with-border">
                      <h3 class="box-title" id="document_list-title">Documents</h3>
                  </div>
                  <div class="box-body" id="document_list" style="overflow-x:auto;">
                    <table class="table table-responsive table-bordered table-hover" id="ratings_table">
                      <thead>
                        <tr>
                          <td style='width=500px;'>DTSNO</td>
                          <td style='width=500px;'>Date Created</td>
                          <td style='width=500px;'>Division / Unit</td>
                          <td style='width=150px;'>Responsiveness</td>
                          <td style='width=150px;'>Reliability</td>
                          <td style='width=150px;'>Communication</td>
                          <td style='width=500px;'>Courtesy and integrity</td>
                          <td style='width=150px;'>Assurance</td>
                          <td style='width=150px;'>Overall Satisfaction</td>
                          <td style='width=150px;'>Rating Average</td>
                          <td style='width=150px;'>Time Average</td>
                        </tr>
                      </thead>
                      <tbody id="tbody">
                      </tbody>
                      <tfoot id="tfoot">
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              
            </div>
      </div>
    </section>
</div>
<?php  $this->load->view('reports/ratings/index_js'); ?>
<?php  $this->load->view('reports/ratings/index_modal'); ?>
