<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in (v2)</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url() ?>assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="<?= base_url() ?>assets/index2.html" class="h1"><b>Template</b>ICSO</a>
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
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="button" class="btn btn-primary btn-block" id="btnlogin">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

<!--       <div class="social-auth-links text-center mt-2 mb-3">
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
        </a>
      </div>
 -->      <!-- /.social-auth-links -->

<!--       <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p>
      <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
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
<script type="text/javascript">
  var baseurl = "<?php echo base_url();?>"; 
    $(document).ready(function(){
      $('#btnreg').on('click', function(){
        window.location = baseurl+'Registration';
      }); 
      $('#btnlogin').on('click', function(){
        login();    
      });
    <?php if($this->session->flashdata('success_message')) { ?>
        toastr.success("<?php echo $this->session->flashdata('success_message'); ?>");
    <?php }
          if($this->session->flashdata('warning_message')) { ?>
        toastr.warning("<?php echo $this->session->flashdata('warning_message'); ?>");
    <?php } ?>
    }); 
    $(document).keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
          $('#btnlogin').addClass("disabled"); 
          login();  
        }

    });   

    function login()
    {
      var username = $('#username').val();
      var password = $('#password').val();        
      var log_type = $('#log_type').val();        
      // var captcha = $('#captcha').val();    
      var message='';    
      if(username==''){
        message +='Username Cannot be Empty.\n';
      }
      if(password==''){
        message +='Password Cannot be Empty.\n';
      }
      // if(captcha==''){
      //   message +='Captcha Cannot be Empty.';
      // }
      if(message==''){
      $(this).addClass("disabled"); 
      $.ajax({
        type: "POST",
        url: baseurl + "Authentication/login",
        data: {
          username: username,
          password: password,
        },
        success: function(e){       
          $('#btnlogin').removeClass("disabled");
          if(e.message != "Completed"){ 
            alert(e.message);
          } else{
            window.location = baseurl+e.home_page;
          }
        }       
      });
    }else{
      alert(message);
    }
    }
    $("#register").click(function(){
      window.location = baseurl+"Registration";
    })
</script>
</body>
</html>
