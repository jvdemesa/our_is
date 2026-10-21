<style>
    .badge {
        white-space: normal;
    }
</style>

<div class="content-wrapper" style="min-height: 414px;">

    <section class="content-header">
        <h1 class="page-header">
        <i class="fa fa-car"></i> Travel Order
        <small></small>
        </h1>
        <ol class="breadcrumb">
        <li class="">Reports</li>
        <li class="active">Travel Order</li>
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
                            <button class="btn btn-primary" onclick="filter();">Filter</button>
                        </div>
                        <div class="col-lg-2  pull-right">
                            <div id="div_unit"> 
                                <select class="form-control select2 " name="status" id="status">
                                    <option value="" selected disabled>Select Status</option>
                                    <option value="">ALL</option>
                                    <option value="0">Active - Pending Approval</option>
                                    <option value="2">Active - Approved</option>
                                    <option value="1">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3  pull-right">
                            <div id="div_unit"> 
                                <select class="form-control select2 " name="uid" id="uid">
                                    <option value="" disabled selected>SELECT USER</option>
                                    <option value="">ALL</option>
                                    <?php foreach($users as $val){
                                        echo "<option value='$val->uid'> $val->fname  $val->lname</option>";
                                    }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3  pull-right">
                            <div id="div_unit"> 
                                <select class="form-control select2 " name="div_id" id="div_id">
                                    <option value="" disabled selected>SELECT DIVISION</option>
                                    <option value="">ALL</option>
                                    <?php foreach($division as $val){
                                        echo "<option value='$val->divunit_id'> $val->divunit_abv - $val->divunit_name</option>";
                                    }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3  pull-right">
                            <div id="div_unit"> 
                                <input type="text" name="travel_date" id="travel_date" class="daterange form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box box-success" id="details_box">
                  <div class="box-header  with-border">
                      <h3 class="box-title" id="document_list-title">Documents</h3>
                  </div>
                  <div class="box-body" id="document_list" style="overflow-x:auto;">
                    <table class="table table-responsive table-bordered table-hover"   id="document_list_table">
                      <thead>
                        <tr>
                          <td style='width=500px;'>Name</td>
                          <td style='width=500px;'>Division / Unit</td>
                          <td style='width=150px;' id="first_row">Travel Date</td>
                          <td style='width=150px;'>DTSNO</td>
                          <td style='width=150px;'>Travel Order Number</td>
                          <td style='width=500px;'>Doc Details</td>
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
<?php  $this->load->view('reports/travel_order/index_js'); ?>
<?php  $this->load->view('reports/travel_order/index_modal'); ?>
