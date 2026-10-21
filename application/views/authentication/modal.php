<div class="modal fade" id="forgot_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal_title">Forgot password</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modal-body-forgot">
        <div class="form-group">
          <label for="exampleInputEmail1">Registered Email address</label>
          <input type="email" class="form-control" id="email" placeholder="Registered Enter email" name="email">
        </div>
        <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
        <button type="button" class="btn btn-success check_email" id="check_email">Submit</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
</div>

<!-- /.modal -->
<div class="modal fade" id="change_password_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modal_title">Change Password</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="uid" name="uid">
        <input type="hidden" id="code_id" name="code_id">
        <div class="form-group">
          <label for="password">New Password</label>
          <input type="password" class="form-control pass" id="new_password" placeholder="New Password" name="password" onKeyup="cpass()">
        </div>
        <div class="form-group">
          <label for="cpassword">Confirm Password<small id="cpass_label"></small></label>
          <input type="password" class="form-control pass" id="cpassword" placeholder="Confirm Password" name="password" onKeyup="cpass()">
        </div>
        <div class="form-group">
          <label for="vcode">Verification Code <span class="countdown"></span><small style="color:red;"><br>*This code has been sent to your registered email</small></label>
          <input type="text" class="form-control" id="vcode" placeholder="Verification Code" name="vcode">
        </div>
        <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">
           Close
        </button>
        <button type="button" class="btn btn-success update_password" id="update_password">Submit</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
</div>

<div class="modal fade" id="galleryModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Los Ba&ntilde;os Business Permit</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="<?= base_url("assets/images/new.jpg")?>" class="d-block w-100">
            </div>
            <div class="carousel-item">
              <img src="<?= base_url("assets/images/renew.jpg")?>" class="d-block w-100">
            </div>
          </div>
          <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
