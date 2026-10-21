  <style>
    .bg-uplb-red{
      background-color: #8A1538;
    }
    .fa{
      color: white;
    }
    .info-box:hover {
      box-shadow: 0 0 2px rgba(0,0,0,.125), 0 5px 3px #8A1538;
      color:#8A1538;
    }
    .info-box:hover .info-box-icon{
      background-color: #FFB81C !important;
    }

    .info-box:hover .fa{
      color: #8A1538 !important;
    }
    .info-box-text{
      text-align: center;
    }
    .main-btn{
      font-size: 50pt;
    }
    .info-box .info-box-icon {
      font-size: 50pt;
      width: 10%;
    }
  </style>
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Client Services</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Client Services</li>
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
          <?php
          foreach($services as $val){
          ?>
          <div class="col-12 col-sm-12 col-md-12 get_service" service_id="<?= $val->id ?>" service="<?= $val->description ?>" default_number=<?= $val->default_number ?>>
            <div class="info-box mb-12">
              <div class="info-box-content">
                <span class="info-box-text"><span class="main-btn"><?= $val->description ?></span></span>
              </div>
              <span class="info-box-icon bg-uplb-red elevation-1"><i class="fa fa-solid fa-chevron-right"></i></span>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <?php
          }
          ?>
      </div>
      <!-- END Section -->
    </section>
  </div>
<?php  $this->load->view('clients/services_js'); ?>
<?php  $this->load->view('clients/services_modal'); ?>
