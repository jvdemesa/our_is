<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/croppie/croppie.css">
<script src="<?php echo base_url(); ?>assets/js/croppie/croppie.min.js"></script>

<script type="text/javascript">

  
function show_user_profile() {
    $('#up-current_dp').show();
    var uid = <?php echo $userdata->userid; ?>;

    $.ajax({
      type: "POST",
      url: "<?php echo base_url(); ?>Users/users_details_json",
      dataType: "JSON",
      data:{uid:uid},
      success: function(data){
        $('#user_profile_croppie_div').hide();
        var dt = new Date();
        // $('#user_profile_form')[0].reset();
        var uid = data.uid;
        var fname = data.fname;
        var lname = data.lname;
        var mname = data.mname;
        var username = data.username;
        var home_page = data.home_page;
        var access = data.access;
        var privilege = data.privilege;
        var accessarray = access.split(',');
        var privilege_array = privilege.split(',');

        $("#user_profile_form").find("#up-uid").val(uid);
        $("#user_profile_form").find("#up-fname").val(fname);
        $("#user_profile_form").find("#up-lname").val(lname);
        $("#user_profile_form").find("#up-mname").val(mname);

          var dp = '<?php echo base_url(); ?>assets/images/user_photo/user'+uid+'.jpg';
          check(dp)
          .on("error", function(e) {
            $("#up-current_dp").html('<img src="<?php echo base_url(); ?>assets/dist/img/portrait_placeholder.png" width="300" height="300" class="user-image">');
          })
          .on("load", function(e) {
            $("#up-current_dp").html('<img src="'+dp+'"  class="user-image">');
          })
        $('#user_profile_modal').modal('show');
      }
    });

    $('#user_profile_modal').modal('show');
  return false;
}
function check(src) {
  return $("<img>").attr('src', src);
}
$(document).ready(function() {

  $('#user_profile_croppie_div').hide();
   var $image_crop = $('#up-upload-image').croppie({
    enableExif: true,
    viewport: {
        width: 200,
        height: 200,
        type: 'circle',
    },
    boundary: {
        width: 300,
        height: 300
    },
    showZoomer: true,
    enableOrientation: true
});
$('#up-images').on('change', function () { 
    $('#user_profile_croppie_div').show();
    $('#up-current_dp').hide();
    var reader = new FileReader();
    reader.onload = function (e) {
        $image_crop.croppie('bind', {
            url: e.target.result
        }).then(function(){
          
        });         
    }
    reader.readAsDataURL(this.files[0]);
});

  
  $( "#up-rotateLeft" ).click(function(e) {
    e.preventDefault();
            $image_crop.croppie('rotate', parseInt($(this).data('rotate')));
        });
       
        $( "#up-rotateRight" ).click(function() {
            $image_crop.croppie('rotate',parseInt($(this).data('rotate')));
        });

  $( "#user_profile_form" ).on( "click", "#up-submit_add", function(e) {
    e.preventDefault();
    if (!$('form#user_profile_form')[0].checkValidity()) {
      $('form#user_profile_form').find('input[type="submit"]').click();
      $("form#user_profile_form")[0].reportValidity();
      return false;
    } else {
      $image_crop.croppie('result', {
          type: 'canvas',
          size: 'viewport'
        }).then(function (response) {
          //add data to display_pic input
          $('#up-display_pic').val(response);
        }).then(function(){
          //send user_profile_form
          var form_data = new FormData($("form#user_profile_form")[0]);
          $.ajax({
            type: "POST",
            url: $('#user_profile_form').attr('action'),
            dataType: "JSON",
            data:form_data,
            contentType: false, 
            processData: false, 
            success: function(msg){
              toastr.success(msg);
              $('#user_profile_modal').modal('hide');
            }
          })
        });
    }
  });


});

</script>
