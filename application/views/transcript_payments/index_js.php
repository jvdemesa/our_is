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
    "aLengthMenu": [[5, 10, 20, 50], [5, 10, 20, 50]],
    "iDisplayLength": 5,
    // dom: 'Blfrtip',
    select: true,
    "ajax": {
      "url": "<?php echo site_url('Transcript_payments/get_list')?>",
      "type": "POST",
      "data": function ( data ) {
        // data.assigned_to = $("#assigned_to").val();
        data.status = $("#select_status").val();
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

  $("#viewresult").on("click",".view_file",function(){
    var iframe = document.getElementById('payment_iframe');
    var url = new URL(this.getAttribute('link'));
    var file_id = url.searchParams.get('id');
    iframe.src =  `https://drive.google.com/file/d/${file_id}/preview`;
    $("#payment_modal").modal("show");
  })

  $("#viewresult").on("click",".match",function(){
    var payment_id = this.getAttribute('payment_id');
    $("#payment_id").val(payment_id);
    $("#match_modal").modal("show");
  })

  $("#confirm_btn").click(function(e){
    $('#modal-overlay').show()
    var formData = new FormData($("#form1")[0]);
    var trans_code = $("#trans_code").val();
    if (!$('#form1')[0].checkValidity()) {
      $('#form1').find('input[type="submit"]').click();
      $("#form1")[0].reportValidity();
      $('#modal-overlay').hide()
      return false;
    }else{
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: baseurl+'Transcript_payments/match',
        dataType: "JSON",
        data:formData,
        processData: false,
        contentType: false,
        success: function(data){
          if(data.error == 0){
            var message = '';
            if(data.tr_details == 1){
              message += 'Request status has been updated to payment received.';
            }
            if(data.tr_payments == 1){
              message += 'Payment has been tagged as matched.';
            }
            toastr.info(message);
            table.ajax.reload( null, false );
          }else{
            toastr.info('Something Went Wrong. Please try again');
          }
          $('#modal-overlay').hide()
          $("#match_modal").modal("hide");
        },
        error: function(xhr, status, error) {
            toastr.warning("Could not load the content to print.");
        }
      });
    }
  })

  $('#trans_code').select2({
    maximumSelectionLength: 2,
    ajax: {
      method:"POST",
      url: baseurl+'Transcript_payments/get_trans_code_list',
      dataType: 'json',
      delay: 250, // Delay in milliseconds before making the request
      data: function (params) {
        return {
          q: params.term // The user's input search term
        };
      },      
      processResults: function(data) {
      var options = [];
        $.each(data, function(index, item) {
          options.push({
            id: item.trans_code,
            text: `${item.trans_code} (${item.fname} ${item.lname})` 
          });
        });
        return {
          results: options
        };
      },
      cache: true
    }
  });

  $("#viewresult").on("click",".compare_email",function(e){
    e.preventDefault();
    showLoader();
    var email = this.getAttribute("email");
    var payment_id = this.getAttribute("payment_id");
    $("#epayment_id").val(payment_id);
    matched_email_table.clear().draw();
    $.ajax({
      url: baseurl+"Transcript_payments/compare_email",
      type: 'POST',
      dataType: "json",
      data:{email:email},
      success: function(response) {
        if(response){
          $.each(response,function(key,val){
            switch(val.status){
              case "0":
                  var status = "New Request";
              break;
              case "1":
                  var status = "Assessed";
              break;
              case "2":
                  var status = "Assessment Sent";
              break;
              case "3":
                  var status = "Proof of payment received";
              break;
              case "4":
                  var status = "Payment verified - On Process";
              break;
              case "5":
                  var status = "For release";
              break;
              case "6":
                  var status = "Released";
              break;
              default:
                  var status = "No matching request";
              break;
            }
            var action = `<button class="btn btn-success btn-sm match_payment" trans_code="${val.trans_code}"><i class="fa fa-handshake-o"></i></button>`;
            matched_email_table.row.add([ val.trans_code, 
                `${val.lname}, ${val.fname} ${val.mname}`, 
                status, 
                val.assigned_to, 
                action,
              ]).draw( false );
          })
          $("#compare_email_modal").modal("show");
        }else{
          toastr.info("No same email found.");
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

  $("#matched_email_table").on("click",".match_payment",function(){
    showLoader();
    var payment_id = $("#epayment_id").val();
    var trans_code =this.getAttribute("trans_code");
    if(confirm("Confirm match payment?")){
      hideLoader();
      $.ajax({
        url: baseurl+"Transcript_payments/match_payment",
        type: 'POST',
        dataType: "json",
        data:{payment_id:payment_id, trans_code:trans_code},
        success: function(response) {
          hideLoader();
          toastr.info("payment has been matched!");
          $("#compare_email_modal").modal("hide");
          table.ajax.reload( null, false );
        }
      });
    }
  })
})//end document.ready
</script>