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
      <div class="row mb-3">
        <div class="col-md-3">
          <label for="filter_term">Term</label>
          <select name="term_id[]" id="filter_term" class="form-control select2" multiple>
            <?php
            foreach($term_list as $val){
            echo "<option value='$val->term_id'>$val->term_id</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-4">
          <label for="filter_program">Degree Program</label>
          <select id="filter_program" class="form-control">
            <option value="">-- Select Degree Program --</option>
            <option value="BSIT">BS Information Technology</option>
            <option value="BSCS">BS Computer Science</option>
            <option value="BSECE">BS Electronics Engineering</option>
          </select>
        </div>

        <div class="col-md-2">
          <label for="filter_year">Year</label>
          <select id="filter_year" class="form-control">
            <option value="">-- Select Year --</option>
            <?php 
            $currentYear = date('Y');
            for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                echo "<option value='$i'>$i</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
          <button id="btn_filter" class="btn btn-primary btn-block">
            <i class="fas fa-filter"></i> Apply Filter
          </button>
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
                      <select class="select2" name="file_from" id="file_from">
                        <option value="sais">sais</option>
                        <option value="amis">amis</option>
                      </select>
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

                    <!-- New upload: Student Transcript Information Import -->
                    <form id="uploadAdmissionForm" enctype="multipart/form-data" style="margin-top:8px;">
                      <div class="input-group">
                        <div class="custom-file">
                          <input type="file" name="userfile_admission" id="userfile_admission" size="20" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx" />
                          <label class="custom-file-label" for="userfile_admission" id="file_label_admission">Choose Student Info file</label>
                        </div>
                        <div class="input-group-append">
                          <span class="input-group-text" id="upload_admission">Upload Student Info</span>
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
                      <th>Action</th>
<!--                       <th>Term</th>
                      <th>Course No</th>
                      <th>Grade / Removal</th>
                      <th>credit</th>
 -->                    </thead>
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
<?php  $this->load->view('transcript_imported_grades/index_js'); ?>
