<?php
if (isset($this->session->userdata['logged_in'])) {

header("location: http://localhost/ci/login.php/user_authentication/user_login_process");
}
?>

<link href="<?php echo base_url('asset/css/signin.css'); ?>" rel="stylesheet">
  <body>

    <div class="container">
		<form class="form-signin" action="<?php echo base_url()."login/user_login_process"; ?>" method="post">
        	<?php
    if (isset($error_message)) {
    echo "<div class='alert alert-danger'>";
    echo $error_message;
    echo "</div>";
    }
    ?>
    <?php
    if (isset($message_display)) {
    echo "<div class='alert alert-warning'>";
    echo $message_display;
    echo "</div>";
    }
	if($this->session->flashdata('message')){
	echo "<div class='alert alert-warning'>";
	echo $this->session->flashdata('message');
	echo "</div>";

	}

    ?>      

        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputUserName" class="sr-only">User Name</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="User Name" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <input type="submit" name="submit" class="btn btn-lg btn-primary btn-block" value="Sign in">
        <button onClick="window.location.href = '../../dtrs/user/registration_page'" type="button" class="btn btn-lg btn-success btn-block"> Register</button>
      </form>
      



</div> <!-- /container -->
</body>
</html>