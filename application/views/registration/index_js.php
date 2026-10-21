<script type="text/javascript">
var baseurl = "<?= base_url(); ?>";
$(document).ready(function() {
    $("#terms").click(function(){
        $("#terms_modal").modal("show");
    })

    $("#register").click(function(){
        if (!$('#registration_form')[0].checkValidity()) {
          $('form').find('input[type="submit"]').click();
          $("form")[0].reportValidity();
          return false;
        }else{
            if($("#password").val() != $("#cpassword").val()){
              toastr.warning("Password did not match!");
            }else{
            var form_data = new FormData($("form")[0]);
              $.ajax({
                type: "POST",
                url: baseurl+"Registration/new",
                dataType: "JSON",
                data:form_data,
                contentType: false, 
                processData: false, 
                beforeSend: function(){
                     $("#loader").show();
                },
                success: function(data){
                    if(data.error==0){
                        $("#code_id").val(data.code_id);
                        $("#uid").val(data.uid);
                        $("#validation_modal").modal("show");
                        $('#registration_form')[0].reset();
                    }else{
                        toastr.warning(data.msg);
                    }
                },
                complete : function(){
                    $("#loader").hide();
                }              
            })
            }
        }
    })

    $("#submit_code").click(function(){
      $.ajax({
        type: "POST",
        url: baseurl+"Verify/user",
        dataType: "JSON",
        data:{code:$("#code").val(), code_id:$("#code_id").val(), uid:$("#uid").val()},
        success: function(data){
            if(data == 0){
                alert("Account has been verified. You will be redirected to login page!");
                window.location = baseurl+"Authentication";
            }else{
                alert("Incorrect Code! Try Again!");
            }
        }
      })
    })
});
</script>