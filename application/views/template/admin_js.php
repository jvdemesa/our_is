<script>
var curr_pass_status = 0, confirm_pass_status = 0;
$(document).ready(function () {
    $(".tool_tip").tooltip();
    $("#update_pw").click(function(){
        $("#curr_error").hide();
        $("#curr_pass").removeClass('is-invalid');
        $("#curr_pass").val('');
        $("#confirm_error").hide();
        $("#new_pass").removeClass('is-invalid');
        $("#confirm_pass").removeClass('is-invalid');
        $("#new_pass").val('');
        $("#confirm_pass").val('');
        $("#pw_modal").modal("show");
    })

    $("#curr_pass").blur(function(){
        var curr_pass = $(this).val();
        $.ajax({
            type: "POST",
            dataType: "JSON",
            data: {curr_pass:curr_pass},
            url: baseurl+'Admin/check_pass',
            success: function(data){
                if(!data){
                    curr_pass_status = 0;
                    $("#curr_error").show();
                    $("#curr_pass").addClass('is-invalid');
                }else{
                    curr_pass_status = 1;
                    $("#curr_error").hide();
                    $("#curr_pass").removeClass('is-invalid');
                }
            }
        });
    })

    $("#confirm_pass").keyup(function(){
        var new_pass = $("#new_pass").val();
        var confirm_pass = $(this).val();
        if(new_pass != confirm_pass){
            confirm_pass_status = 0;
            $("#confirm_error").show();
            $("#new_pass").addClass('is-invalid');
            $("#confirm_pass").addClass('is-invalid');
        }else{
            confirm_pass_status = 1;
            $("#confirm_error").hide();
            $("#new_pass").removeClass('is-invalid');
            $("#confirm_pass").removeClass('is-invalid');
        }
    })

    $("#save_pass").click(function(){
        if(confirm_pass_status == 1 && curr_pass_status == 1){
            var new_pass = $("#new_pass").val();
            $.ajax({
                type: "POST",
                dataType: "JSON",
                data: {new_pass:new_pass},
                url: baseurl+'Admin/change_password',
                success: function(data){
                    $("#pw_modal").modal("hide");
                    toastr.info("Password has been updated.");
                }
            });
        }else{
            toastr.warning("Something is wrong.");
        }
    })


    $("#curr_pass_view").click(function(){
      var type = $('#curr_pass').attr('type');
      if(type === "password"){
        $("#curr_pass").attr('type', 'text');
        $(this).removeClass('fa-eye');
        $(this).addClass('fa-eye-slash');
      }else{
        $("#curr_pass").attr('type', 'password');
        $(this).removeClass('fa-eye-slash');
        $(this).addClass('fa-eye');
      }
    })

    $("#new_pass_view").click(function(){
      var type = $('#new_pass').attr('type');
      if(type === "password"){
        $("#new_pass").attr('type', 'text');
        $(this).removeClass('fa-eye');
        $(this).addClass('fa-eye-slash');
      }else{
        $("#new_pass").attr('type', 'password');
        $(this).removeClass('fa-eye-slash');
        $(this).addClass('fa-eye');
      }
    })

    $("#confirm_pass_view").click(function(){
      var type = $('#confirm_pass').attr('type');
      if(type === "password"){
        $("#confirm_pass").attr('type', 'text');
        $(this).attr("title","Hide")
        $(this).removeClass('fa-eye');
        $(this).addClass('fa-eye-slash');
      }else{
        $("#confirm_pass").attr('type', 'password');
        $(this).attr("title","Show")
        $(this).removeClass('fa-eye-slash');
        $(this).addClass('fa-eye');
      }
    })
});
</script>