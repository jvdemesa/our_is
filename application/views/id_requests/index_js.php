<script>
var baseurl = "<?= base_url(); ?>";
var cur_status, active_step, readonly_status, status_header, btn_text, file_id;
function refresh_table(){
  table.ajax.reload();
}

$(document).ready(function() {  
  $(".tooltip_action").tooltip('toggleEnabled');
  matched_email_table = $("#matched_email_table").DataTable();
  table = $('#viewresult').DataTable({ 
    "processing": true, //Feature control the processing indicator.
    "serverSide": true, //Feature control DataTables' server-side processing mode.
    "responsive": true,
    "aLengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "ALL"]],
    "iDisplayLength": 5,
    dom: 'Blfrtip',
    buttons: [
      {
          extend: 'excelHtml5',
          exportOptions: {
              columns: [0, 1, 2, 3, 4, 5, 8, 9]
          }
      }
    ],
    select: true,
    "ajax": {
      "url": "<?php echo site_url('ID_requests/get_list')?>",
      "type": "POST",
      "data": function ( data ) {
        data.request_type = $("#request_type").val();
        data.status = $("#status").val();
      },
      "dataSrc": function(json){
        return json.data;
      }
    },
    //Set column definition initialisation properties.
   "ordering": false,
    createdRow: function( row, data, dataIndex ) {
      // Initialize tooltips on created rows
      $(row).find('.tool_tip').tooltip();
    }
  });

  $("#viewresult").on("click",".view_files",function(){
    var id_picture_file = this.getAttribute('id_picture_file');
    var signature_file = this.getAttribute('signature_file');
    var proof_of_payment_file = this.getAttribute('proof_of_payment_file');
    var current_form5_file = (this.getAttribute('current_form5_file') || '').trim();
    var first_year_form5_file = (this.getAttribute('first_year_form5_file') || '').trim();
    // Form 5 falls back to current_form5 when form5 has no URL
    var form5_file = (this.getAttribute('form5_file') || '').trim() || current_form5_file;
    var student_number = this.getAttribute('student_number');
    var fname = this.getAttribute('fname');
    var lname = this.getAttribute('lname');
    var mname = this.getAttribute('mname');
    var college = this.getAttribute('college');
    var birthday = this.getAttribute('birthday');
    var address = this.getAttribute('address');
    var transaction_id = this.getAttribute('transaction_id');
    var rejection_count = this.getAttribute('rejection_count');
    var email = this.getAttribute('email');
    var id_request_id = this.getAttribute('id_request_id');
    var status = this.getAttribute('status');
    $('#id_request_id').val(id_request_id);
    $('#email').val(email);
    $('#fname').val(fname);
    $('#lname').val(lname);
    $('#mname').val(mname);
    $('#college').val(college);
    $('#birthday').val(birthday);
    $('#address').val(address);
    $('#transaction_id').val(transaction_id);
    $('#rejection_count').val(rejection_count);
    $('#student_number').val(student_number);
    $(".id_picture_file").html(`<center>
                                <button file_link='${id_picture_file}' class='btn btn-success btn-sm show_file'>
                                  View File
                                </button>
                                <button file_link='${id_picture_file}'
                                  student_number='${student_number}'
                                  file_type='id_pic'
                                  class="btn btn-primary btn-sm download_file">
                                    Download
                                </button>
                              </center>  
                                `);
    
    $(".signature_file").html(`<center>
                              <button file_link='${signature_file}' class='btn btn-success btn-sm show_file'>
                                View File
                                </button>
                                <button file_link='${signature_file}'
                                  student_number='${student_number}'
                                  file_type='id_pic'
                                  class="btn btn-primary btn-sm download_file">
                                    Download
                                </button>
                              </center>  
                                `);

    if(proof_of_payment_file!=""){
      $(".proof_of_payment_file").html(`<center>
                                <button file_link='${proof_of_payment_file}' class='btn btn-success btn-sm show_file'>
                                  View File
                                </button>
                                <button file_link='${proof_of_payment_file}'
                                  student_number='${student_number}'
                                  file_type='proof_of_payment'
                                  class="btn btn-primary btn-sm download_file">
                                    Download
                                </button>
                                </center>  
                                  `);
    }

    if(form5_file!=""){
      $(".form5_file").html(`<center>
                                <button file_link='${form5_file}' class='btn btn-success btn-sm show_file'>
                                  View File
                                </button>
                                <button file_link='${form5_file}'
                                  student_number='${student_number}'
                                  file_type='id_pic'
                                  class="btn btn-primary btn-sm download_file">
                                    Download
                                </button>
                                </center>  
                                  `);
    }else{
      $(".form5_file").html("");
    }

    if(first_year_form5_file!=""){
      $(".first_year_form5_file").html(`<center>
                                <button file_link='${first_year_form5_file}' class='btn btn-success btn-sm show_file'>
                                  View File
                                </button>
                                <button file_link='${first_year_form5_file}'
                                  student_number='${student_number}'
                                  file_type='first_year_form5'
                                  class="btn btn-primary btn-sm download_file">
                                    Download
                                </button>
                                </center>  
                                  `);
    }else{
      $(".first_year_form5_file").html("");
    }

    if(status==0 || status==1){
      $("#non_compliance").show();
    }else{
      $("#non_compliance").hide();
    }
    $("#file_list_modal").modal("show");
  })

  $("#file_table,#approval_file_table").on("click", ".show_file", function () {
    var iframe = document.getElementById('file_iframe');
    var fileLink = this.getAttribute('file_link');

    var fileId = "";

    $("#download_file").attr("file_link", fileLink);

    // reset iframe first (important)
    iframe.src = "about:blank";

    if (fileLink.includes("id=")) {
      fileId = fileLink.split("id=")[1];
    } else if (fileLink.includes("/d/")) {
      fileId = fileLink.split("/d/")[1].split("/")[0];
    }

    // small delay helps Google Drive re-render cleanly
    iframe.src = "";
    $("#modal-overlay").show();

    setTimeout(function () {
      iframe.src = "https://drive.google.com/file/d/" + fileId + "/preview";
      $("#modal-overlay").hide();
    }, 100);

    $("#show_file_modal").modal("show");
  });

  $("#file_table").on("click", ".download_file", function () {
    var fileLink = this.getAttribute('file_link');
    if (fileLink.includes("id=")) {
      fileId = fileLink.split("id=")[1];
    } else if (fileLink.includes("/d/")) {
      fileId = fileLink.split("/d/")[1].split("/")[0];
    }
    var downloadUrl = "https://drive.google.com/uc?export=download&id=" + fileId;

    window.open(downloadUrl, "_blank");
  });


  $("#show_file_modal").on("click", "#download_file", function () {
    var fileLink = this.getAttribute('file_link');
    if (fileLink.includes("id=")) {
      fileId = fileLink.split("id=")[1];
    } else if (fileLink.includes("/d/")) {
      fileId = fileLink.split("/d/")[1].split("/")[0];
    }
    var downloadUrl = "https://drive.google.com/uc?export=download&id=" + fileId;

    window.open(downloadUrl, "_blank");
  });

  $("#refresh_requests").click(function(e){
    showLoader();
    e.preventDefault();
    $.ajax({
      url: baseurl+"Google_sheets/ID_requests",
      type: 'POST',
      dataType: "json",
      success: function(response) {
        if(response.with_data == true){
          if(response.error==0){
            toastr.info("ID Request data has been updated.");
            table.ajax.reload( null, false );
          }else{
            toastr.info("Something went wrong. Try again later.");
          }
        }else{
          toastr.info("No new request.");
        }
        hideLoader();
      },
      error: function(xhr, status, error) {
        // Handle error
        console.error(xhr.responseText);
        hideLoader();
      }
    });
  })

  $("#viewresult").on("click",".approve_request",function(){
    var request_type_label= this.getAttribute('request_type');
    var requestor= this.getAttribute('requestor');
    var id_request_id= this.getAttribute('id_request_id');
    var student_number = this.getAttribute('student_number');
    var fname = this.getAttribute('fname');
    var email = this.getAttribute('email');
    var form_file = this.getAttribute('form_file');
    var noliabilities_file = this.getAttribute('noliabilities_file');
    var form5_file = this.getAttribute('form5_file');
    var student_number = this.getAttribute('student_number');
    if(form_file!=""){
      $(".form_file").html(`<center>
                                <button file_link='${form_file}' class='btn btn-success btn-sm show_file'>
                                  View File
                                </button>
                                <button file_link='${form_file}'
                                  student_number='${student_number}'
                                  file_type='id_pic'
                                  class="btn btn-primary btn-sm download_file">
                                    Download
                                </button>
                                </center>  
                                  `);
    }

    if(noliabilities_file!=""){
      $(".noliabilities_file").html(`<center>
                                <button file_link='${noliabilities_file}' class='btn btn-success btn-sm show_file'>
                                  View File
                                </button>
                                <button file_link='${noliabilities_file}'
                                  student_number='${student_number}'
                                  file_type='id_pic'
                                  class="btn btn-primary btn-sm download_file">
                                    Download
                                </button>
                                </center>  
                                  `);
    }

    if(form5_file!=""){
      $(".form5_file").html(`<center>
                                <button file_link='${form5_file}' class='btn btn-success btn-sm show_file'>
                                  View File
                                </button>
                                <button file_link='${form5_file}'
                                  student_number='${student_number}'
                                  file_type='id_pic'
                                  class="btn btn-primary btn-sm download_file">
                                    Download
                                </button>
                                </center>  
                                  `);
    }
    $('#id_request_id').val(id_request_id);
    $('#email').val(email);
    $('#fname').val(fname);
    $('#student_number').val(student_number);

    $("#request_type_label").val(request_type_label);
    $("#requestor").val(requestor);
    $("#approve_modal").modal("show");
  })

  $(".confirm_approval").click(function(){
    var id_request_id = $("#id_request_id").val();
    var status = this.getAttribute('status');
    var todo = this.getAttribute('todo');
    if(confirm(`Click OK to Confirm ${todo} of ID Request`)){
      showLoader();
      $.ajax({
        type: "POST",
        url: baseurl+'ID_requests/approve_id_request',
        dataType: "JSON",
        data:{id_request_id:id_request_id,status:status},
        success: function(data){
          hideLoader();
          if(data){
            if(status){
              toastr.info("Request has been APPROVED.");
            }else{
              toastr.info("Request has been DISAPPROVED.");
            }
            $("#approve_modal").modal("hide");
            table.ajax.reload();
          }else{
              toastr.info("Something went wrong.");
          }
        },
        error: function(xhr, status, error){
          hideLoader();
          toastr.error("Something went wrong.");
          console.log(error);
        }
     });
    }
  })

  $("#viewresult").on("click",".release_schedule",function(){
    var release_date = $("#release_date").val();
    console.log(release_date);
    if(release_date!=""){
      if(confirm(`Please click “OK” to proceed with sending of email for release schedule on ${release_date}.`)){
        showLoader();
        var request_type = this.getAttribute("request_type");
        var student_number = this.getAttribute("student_number");
        var fname = this.getAttribute("fname");
        var email = this.getAttribute("email");
        var id_request_id = this.getAttribute("id_request_id");
        $.ajax({
          type: "POST",
          url: baseurl+'Email/id_release_schedule',
          dataType: "JSON",
          data:{request_type:request_type,student_number:student_number,fname:fname,email:email,id_request_id:id_request_id,release_date:release_date},
          success: function(data){
            hideLoader();
            if(data.error){
              toastr.warning(data.message);
            }else{
              toastr.success(data.message);
              table.ajax.reload( null, false );
            }
            check_release_count(release_date);
          },
          error: function(xhr, status, error) {
              toastr.warning("Something Went Wrong. Please Try Again Later.");
              // Handle error
              console.error(xhr.responseText);
              hideLoader();
          }
        });
      }
    }else{
      toastr.info("Please select date of release first");
    }
  })

  $('#check_all').on('change', function () {
    $('.file_checkbox').prop('checked', $(this).prop('checked'));
  });

  $('#non_compliance').on('click', function () {

    let checked_ids = [];
    let id_request_id = $('#id_request_id').val();
    let student_number = $('#student_number').val();
    let fname = $('#fname').val();
    let lname = $('#lname').val();
    let mname = $('#mname').val();
    let college = $('#college').val();
    let birthday = $('#birthday').val();
    let address = $('#address').val();
    let transaction_id = $('#transaction_id').val();
    let rejection_count = $('#rejection_count').val();
    let email = $('#email').val();
    $('.file_checkbox:checked').each(function () {
      checked_ids.push($(this).val());
    });
    if(confirm(`Click OK to Confirm sending of email for non-compliance of all checked file`)){
      showLoader();
      $.ajax({
        type: "POST",
        url: baseurl+'Email/id_non_compliance',
        dataType: "JSON",
        data:{checked_ids:checked_ids,
              id_request_id:id_request_id,
              student_number:student_number,
              fname:fname,
              lname:lname,
              mname:mname,
              college:college,
              birthday:birthday,
              address:address,
              transaction_id:transaction_id,
              rejection_count:rejection_count,
              email:email,
              },
        success: function(data){
          hideLoader();
          $("#file_list_modal").modal("hide");
          if(data.error){
            toastr.warning(data.message);
          }else{
            $.ajax({
              type: "POST",
              url: baseurl+'Google_sheets/id_masterlist_add_new_record',
              dataType: "JSON",
              data:{
                student_number:student_number,
                fname:fname,
                lname:lname,
                mname:mname,
                college:college,
                birthday:birthday,
                address:address,
                transaction_id:transaction_id,
                email:email
              },
              success: function(data){
              }
            });
            toastr.success(data.message);
            table.ajax.reload( null, false );
          }
        },
        error: function(xhr, status, error){
          hideLoader();
          toastr.error("Something went wrong.");
          console.log(error);
        }
     });
    }
  });


  $('#confirm_disapproval').on('click', function () {

    let id_request_id = $('#id_request_id').val();
    let student_number = $('#student_number').val();
    let fname = $('#fname').val();
    let email = $('#email').val();

    if(confirm(`Click OK to Confirm sending of email for disapproval of ID request`)){
      showLoader();
      $.ajax({
        type: "POST",
        url: baseurl+'Email/id_disapprove',
        dataType: "JSON",
        data:{id_request_id:id_request_id,student_number:student_number,fname:fname,email:email},
        success: function(data){
          hideLoader();
          $("#approve_modal").modal("hide");
          if(data.error){
            toastr.warning(data.message);
          }else{
            toastr.success(data.message);
            table.ajax.reload( null, false );
          }
        },
        error: function(xhr, status, error){
          hideLoader();
          toastr.error("Something went wrong.");
          console.log(error);
        }
     });
    }
  });

  $('#release_date').on('apply.daterangepicker', function(ev, picker) {

      showLoader();

      var release_date = picker.startDate.format('DD MMMM YYYY');

      check_release_count(release_date);
  });

  function check_release_count(release_date){
    $.ajax({
      type: "POST",
      url: baseurl + 'ID_requests/count_release_sched',
      dataType: "JSON",
      data: { release_date: release_date },

      success: function(data){
          hideLoader();

          if(data){
              $("#total_release").html(data);
          } else {
              $("#total_release").html(0);
          }
      },

      error: function(xhr, status, error){
          hideLoader();
          toastr.error("Something went wrong.");
          console.log(error);
      }
    });
  }

})//end document.ready
</script>