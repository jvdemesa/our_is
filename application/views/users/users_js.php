<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/croppie/croppie.css">
<script src="<?php echo base_url(); ?>assets/js/croppie/croppie.min.js"></script>

<script>
var unit_id = "", selected_provCode="", selected_citymunCode="", selected_brgyCode="", selected_citymun="",selected_brgy="";


function check(src) {
  return $("<img>").attr('src', src);
}
$('#croppie_div').hide();
  $image_crop = $('#upload-image').croppie({
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

$('#images').on('change', function () { 
    $('#croppie_div').show();
    $('#current_dp').hide();
    var reader = new FileReader();
    reader.onload = function (e) {
        $image_crop.croppie('bind', {
            url: e.target.result
        }).then(function(){
            console.log('jQuery bind complete');
        });         
    }
    reader.readAsDataURL(this.files[0]);
});

$(document).ready(function() {
  $("#filter").click(function(e){
    table.ajax.reload( null, false );
  }) 
  
  $( "#rotateLeft" ).click(function(e) {
    e.preventDefault();
            $image_crop.croppie('rotate', parseInt($(this).data('rotate')));
        });
       
        $( "#rotateRight" ).click(function() {
            $image_crop.croppie('rotate',parseInt($(this).data('rotate')));
        });

  $( "#form1" ).on( "click", "#submit_add", function(e) {
    e.preventDefault();
    if (!$('form')[0].checkValidity()) {
      $('form').find('input[type="submit"]').click();
      $("form")[0].reportValidity();
      return false;
    }else{
      var form_data = new FormData($("form")[0]);
      $.ajax({
        type: "POST",
        url: $('#form1').attr('action'),
        dataType: "JSON",
        data:form_data,
        contentType: false, 
        processData: false, 
        success: function(msg){
          toastr.success(msg);
          table.ajax.reload( null, false );
          $('#modal').modal('hide');
        }
      })
    }
  });

  $("#add").click(function() {
      $('#form1')[0].reset();
      $("#modal_title").text('Add User');
      $("#home_page").val('').change();
      $("#access").val('').change();
      document.getElementById('form1').action =  '<?php echo base_url()."users/add_users"; ?>';
      $('#modal').modal('show');
});

  $( "#viewresult" ).on( "click", ".edit_info", function(e) {
    e.preventDefault();
    var uid = this.getAttribute("uid");
    $.ajax({
      type: "POST",
      url: "<?php echo base_url(); ?>Users/users_details_json",
      dataType: "JSON",
      data:{uid:uid},
      success: function(data){
        $('#croppie_div').hide();
        var dt = new Date();
        $('#form1')[0].reset();
        var uid = data.uid;
        var section_id = data.section_id;
        var on_leave = data.on_leave;
        var first_name = data.first_name;
        var last_name = data.last_name;
        var middle_name = data.middle_name;
        var email = data.email;
        var username = data.username;
        var mobile_number = data.mobile_number;
        var home_page = data.home_page;
        var access = data.access;
        var accessarray = access.split(',');
        $("#username").val(username);
        $("#home_page").val(home_page).change();
        $("#access").val(accessarray).change();

        $("#uid").val(uid);
        $("#first_name").val(first_name);
        $("#last_name").val(last_name);
        $("#middle_name").val(middle_name);
        $("#username").val(username);
        $("#mobile_number").val(mobile_number);
        $("#email").val(email);
        $("#on_leave").val(on_leave).change();
        $("#section_id").val(section_id).change();
        $("#modal_title_add").text('Update User Information');
        document.getElementById('form1').action =  '<?php echo base_url()."users/edit_info"; ?>';
        $('#modal').modal('show');
      }
    });
  });

  $( "#viewresult" ).on( "click", ".reset_password", function(e) {
    e.preventDefault();
    $('#current_dp').show();
    var uid = this.getAttribute("uid");
    var uname = this.getAttribute("uname");
    if ( confirm('Are you sure you want to reset the password of ' + uname + ' ?') ) {
      $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>Users/reset_password",
        dataType: "JSON",
        data:{uid:uid},
        success: function(data){
          $("#pw_body").html('The new password is <strong id="new_pw">' + data.new_password + '</strong>');
          $("#reset_pw_modal").modal("show");
        }
      });
    } 
  });

  $( "#viewresult" ).on( "click", ".disable_user", function(e) {
    e.preventDefault();
    $('#current_dp').show();
    var uid = this.getAttribute("uid");
    var uname = this.getAttribute("uname");
    if ( confirm('Are you sure you want to DISABLE user ' + uname + ' ?') ) {
      $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>Users/disable_user",
        dataType: "JSON",
        data:{uid:uid,is_disable:"1"},
        success: function(data){
          toastr.warning("User has been DISABLED!");
          table.ajax.reload( null, false );
        }
      });
    } 
  });

  $( "#viewresult" ).on( "click", ".enable_user", function(e) {
    e.preventDefault();
    $('#current_dp').show();
    var uid = this.getAttribute("uid");
    var uname = this.getAttribute("uname");
    if ( confirm('Are you sure you want to ENABLE user ' + uname + ' ?') ) {
      $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>Users/disable_user",
        dataType: "JSON",
        data:{uid:uid,is_disable:"0"},
        success: function(data){
          toastr.success("User has been ENABLED!");
          table.ajax.reload( null, false );
        }
      });
    } 
  });

  $("#copy_pw").click(function(){
    var new_pw = $("#new_pw").html();
     if (window.isSecureContext && navigator.clipboard) {
        navigator.clipboard.writeText(new_pw);
      } else {
        unsecuredCopyToClipboard(new_pw);
      }
    toastr.success("New Password "+new_pw+" has been Copied to clipboard");
    $("#reset_pw_modal").modal("hide");
  })

    table = $('#viewresult').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      responsive: true,
      "aLengthMenu": [[5, 10, 20, 50], [5, 10, 20, 50]],
      "iDisplayLength": 10,
      // dom: 'Blfrtip',
      select: true,
      "ajax": {
        "url": "<?php echo site_url('Users/get_users_json')?>",
        "type": "POST",
        "data": function ( data ) {
          // data.privilege = $("#privilege_filter").val();
        },
        "dataSrc": function(json){
          return json.data;
        }
      },
      //Set column definition initialisation properties.
      "columnDefs": [
        { 
          "targets": [ 0 ], //first column / numbering column
          "orderable": false, //set not orderable
        },
      ],
    createdRow: function( row, data, dataIndex ) {
        // Set the data-status attribute, and add a class
        if(data[6]==1){
          $(row).addClass("label-danger");
        }
    }
    });

    $( "#datepicker" ).daterangepicker({
    changeYear:true,
    changeMonth: true,
    yearRange: "1980:2015",
    dateFormat: 'yy-mm-dd'
    });
    $( "#bday" ).daterangepicker({
    changeYear:true,
    changeMonth: true,
    yearRange: "1980:2015",
    dateFormat: 'yy-mm-dd'
    });

    $("#provCode").change(function(){
      var provCode = $(this).val();
      var province = $('option:selected', this).attr('province');
      $("#province").val(province);
      $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>General/get_citymun_list",
        dataType: "JSON",
        data:{provCode:provCode},
        success: function(data){
          var options = "<option value='' selected disabled>Select City / Municipality</option>";
          $.each(data, function(i, val) {
            if(selected_citymunCode == val.citymunCode){
              options +=`<option selected value="${val.citymunCode}" citymun="${val.citymunDesc}">${val.citymunDesc}</option>`;
            }else{
              options +=`<option value="${val.citymunCode}" citymun="${val.citymunDesc}">${val.citymunDesc}</option>`;
            }
          })
          $("#citymunCode").html(options);
          $("#citymunCode").val(selected_citymunCode).change();
        }
      })
    })

    $("#citymunCode").change(function(){
      var citymunCode = $("#citymunCode").val();
      var citymun = $('option:selected', this).attr('citymun');
      get_brgy_list(citymunCode,citymun);
    })

    $("#brgyCode").change(function(){
      var brgy = $('option:selected', this).attr('brgy');
      $("#brgy").val(brgy);
    })
});
</script>
