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
      .uplb-red {
        color: #8A1538 !important;
      }
      .uplb-green {
        color: #00573F !important;
      }
      .uplb-yellow {
        color: #FFB81C !important;
      }
      .uplb-gray {
        color: #3a3a3a !important;
      }
      .uplb-bg-red {
        background-color: #8A1538 !important;
      }
      .uplb-bg-green {
        background-color: #00573F !important;
        color: white !Important;
      }
      .uplb-bg-yellow {
        background-color: #FFB81C !important;
      }
      .uplb-bg-gray {
        background-color: #3a3a3a !important;
        color: white !Important;
      }
  </style>
</head>
<body class="hold-transition lockscreen">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper">
  <div class="lockscreen-logo">
    <b>OUR</b> Queue
  </div>
  <!-- User name -->
  <div class="lockscreen-name"><?= $userdata->last_name.", ".$userdata->first_name ?></div>
  <div class="lockscreen-item">
    <center><h1>Your account has been restricted by the administrator.</h1></center>
  </div>
  <!-- /.lockscreen-item -->
  <div class="help-block text-center">
    <center><small>If you think this is a mistake, please contact the administrator at <strong>email@admin.com</strong></small></center>
  </div>
  <div class="text-center">
    <a href="<?= base_url("Authentication/logout") ?>">Or sign out and sign in different user</a>
  </div>
  <div class="lockscreen-footer text-center">
    <strong class="uplb-red">UPLB Office of the University Registrar</strong>
  </div>
</div>
<!-- /.center -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
