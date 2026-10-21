<script type="text/javascript">
toastr.options = {
  "closeButton": false,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-top-center",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}

var baseurl = "<?php echo base_url();?>"; 
function cpass(){
  var new_password = $("#new_password").val();
  var cpassword = $("#cpassword").val();
  if(new_password!=cpassword){
    $(".pass").addClass("red");
    $("#cpass_label").html("Password and Confirm password did not match!")
  }else{
    $("#cpass_label").html("")
    $(".pass").removeClass("red");
  }
}

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
    var remember_me = $('#remember_me').is(":checked")?"1":"0";        
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
        remember_me:remember_me,
      },
      success: function(e){       
        $('#btnlogin').removeClass("disabled");
        if(e.status != 0){ 
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

function timer(start_time){  
   var currentTime = new Date(start_time);
    
    // Set the target time (5 minutes from the current time)
    var targetTime = new Date(currentTime.getTime() + (5 * 60 * 1000)); // 5 minutes in milliseconds
    
    // Update the countdown every second
    var interval = setInterval(function() {
      // Get the current time
      var currentTime = new Date().getTime();
      
      // Calculate the remaining time in seconds
      var remainingSeconds = Math.floor((targetTime - currentTime) / 1000);
      
      // If the countdown has finished, stop the timer
      if (remainingSeconds < 0) {
        clearInterval(interval);
        $('.countdown').html('Expired! (<small>click <a href="#" id="resend_code">here</a> to resend Code</small>');
        return;
      }
      
      // Format the remaining time as MM:SS
      var minutes = Math.floor(remainingSeconds / 60);
      var seconds = remainingSeconds % 60;
      var formattedTime = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
      
      // Update the countdown display
      $('.countdown').html('Expires in <span class="red_text">' + formattedTime + '</span>');
    }, 1000); // 1000ms = 1 second
}

function generate_code(){
    $.ajax({
      type: "POST",
      url: baseurl+"Authentication/validate_email",
      dataType: "JSON",
      data:{email:$("#email").val()},
      beforeSend: function(){
           $("#loader").show();
      },
      success: function(data){
          if(data.uid==0){
            toastr.error("Email not found!");
          }else{
            toastr.success("Email found! Validation code has been sent to your email.");
            $("#uid").val(data.uid);
            $("#code_id").val(data.code_id);
            $("#change_password_modal").modal("show");
            timer(data.start_time);
          }
      },
      complete : function(){
          $("#loader").hide();
      }              
    })
}

$(document).ready(function(){
  $(".countdown").on("click","#resend_code", function(){
    generate_code();
  }); 

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

  $("#forgot").click(function(){
    $("#forgot_modal").modal("show");
  })

  $("#check_email").click(function(){
    generate_code();
  })

  $("#update_password").click(function(){
    if($("#new_password").val()==$("#cpassword").val()){
      $.ajax({
        type: "POST",
        url: baseurl+"Authentication/update_password",
        dataType: "JSON",
        data:{uid:$("#uid").val(), password:$("#new_password").val(), code:$("#vcode").val(),code_id:$("#code_id").val()},
        beforeSend: function(){
             $("#loader").show();
        },
        success: function(data){
            if(data==0){
              toastr.error("Invalid Authentication Code!");
            }                
            if(data==2){
              toastr.error("Code is not valid anymore. Please request a new code to reset your password.");
              // $("#change_password_modal").modal("hide");
              // $("#forgot_modal").modal("hide");
            }
            if(data==1){
              toastr.success("Congratulation! You may now login using your new password.");
              $("#uid").val("");
              $("#change_password_modal").modal("hide");
              $("#forgot_modal").modal("hide");
            }
        },
        complete : function(){
            $("#loader").hide();
        }              
      })
    }else{
      cpass();
    }
  })


}); 
</script>