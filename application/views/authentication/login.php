<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="manifest" href="<?= base_url(); ?>/manifest.json">
  <link rel="icon" type="image/x-icon" href="<?= base_url('assets/images/logo.png'); ?>">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OUR - QUEUE</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/toastr/toastr.min.css">
  <style type="text/css">
      #loader{
        display: none;
        position: fixed;
        left: 0%;
        top: 0%;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: rgba(0,0,0,0.6);
      }
      #loader2{
        position: fixed;
        left: 50%;
        top: 50%;
        width: 100%;
        height: 100%;
        z-index: 9999;
      }
      .red{
        border:  2px solid red;
      }
      .red_text{
        color:  red;
      }
      .responsive-image {
        max-width: 100%; /* set the maximum width of the image to 100% of its parent container */
        max-height: 100%; /* set the maximum height of the image to 100% of its parent container */
        display: block; /* set the display property to block to prevent any padding/margin issues */
      }
      .card-uplb-g.card-outline {
          border-top: 3px solid #00573F;
      }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-uplb-g">
    <div class="card-header text-center">
      <img src="<?= base_url('assets/images/logo.png'); ?>" alt="Image" class="responsive-image">
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username" id="username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" id="password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember_me" name="remember_me">
              <label for="remember_me">
                Remember Me
              </label>
            </div>
            <ul>
<!--               <li>
                <a href="#" id="forgot">I forgot my password</a>
              </li>
              <li>
                <a href="<?= base_url("Registration/") ?>" class="text-center">Click here to Register</a>
              </li>
 -->            </ul>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="button" class="btn btn-primary btn-block" id="btnlogin">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

       <div class="social-auth-links text-center mt-2 mb-3">
<!--         <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
        </a>
 -->        
<!--         <a href="<?= filter_var($google_auth_url, FILTER_SANITIZE_URL) ?>" class="btn btn-block btn-danger" id="google_login">
          <i class="fab fa-google mr-2"></i> Sign in using Gmail
        </a>
 -->      </div>
      <!-- /.social-auth-links -->

<!--        <p class="mb-1">
        <a href="#" id="forgot">I forgot my password</a>
      </p>
      <p class="mb-0">
        <a href="<?= base_url("Registration/") ?>" class="text-center">Click here to Register</a>
      </p>
 -->    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->
<!-- jQuery -->
<script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>assets/dist/js/adminlte.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/toastr/toastr.min.js"></script>
<?php  $this->load->view('authentication/spinner'); ?>
<?php  $this->load->view('authentication/modal'); ?>
<?php  $this->load->view('authentication/js'); ?>
</body>
</html>
