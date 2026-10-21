<script>
var baseurl = "<?= base_url(); ?>";

$(document).ready(function() {  
  $(".tooltip_action").tooltip('toggleEnabled');
   $('#userfile').change(function(e) {
    var fileName = e.target.files[0].name;
    $('#file_label').html(fileName);
   });

   $('#upload').click(function(e) {
      var fileInput = $('#userfile')[0];
      var file = fileInput.files[0];

      if (!file) {
        alert('Please select a file.');
        return;
      }

      if (file.type !== 'text/csv' && file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type !== 'application/vnd.ms-excel') {
        alert('Please select a CSV file.');
        return;
      }
      $('#modal-overlay').show()

      var formData = new FormData($('#uploadForm')[0]);

      $.ajax({
        url: baseurl+"Transcript_clearance/upload",
        type: 'POST',
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function(response) {
            $('#file_label').html("");
            $("#uploadForm").trigger("reset");
            // Handle success response
          if(response == "0"){
            toastr.info('No added data.');
          }else{
            table.ajax.reload( null, false );
            toastr.info(`${response} cleared student/s added.`);
          }
          $('#modal-overlay').hide()
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error(xhr.responseText);
        }
      });
   });

  $("#viewresult").on("click", ".download", function(e) {
      e.preventDefault();
      var batch_id = $(this).attr("batch_id");
      
      // Create a form dynamically
      var form = $('<form action="' + baseurl + 'Excel_export/download_excel" method="post"></form>');
      form.append('<input type="hidden" name="batch_id" value="' + batch_id + '">');
      
      // Submit the form
      $('body').append(form);
      form.submit();
  });

  $("#viewresult").on("click", ".view", function(e) {
    var batch_id = this.getAttribute("batch_id");
    // Create a new form element
    var form = $('<form>', {
        'action': baseurl+'Student', // URL of the destination page
        'method': 'post', // POST method
        'target': '_blank' // Open in a new tab or window
    });

    // Add hidden input fields for the POST data
    form.append($('<input>', {
        'type': 'hidden',
        'name': 'batch_id',
        'value': batch_id
    }));

    // Append the form to the body and submit it
    form.appendTo('body').submit();
  });

  $("#viewresult").on("click", ".delete", function(e) {
    if(confirm("Confirm delete batch?")){
      var batch_id = this.getAttribute("batch_id");
      $.ajax({
        url: baseurl+"Reclassification/delete_batch",
        type: 'POST',
        data: {batch_id:batch_id},
        dataType: "json",
        success: function(response) {
          if(response){
          toastr.info("The batch has been deleted.");
          table.ajax.reload( null, false );

          }
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error(xhr.responseText);
        }
      });
    }
  });

  table = $('#viewresult').DataTable({ 
    "processing": true, //Feature control the processing indicator.
    "serverSide": true, //Feature control DataTables' server-side processing mode.
    "responsive": true,
    "aLengthMenu": [[5, 10, 20, 50], [5, 10, 20, 50]],
    "iDisplayLength": 5,
    dom: 'Blfrtip',
    select: true,
    "ajax": {
      "url": "<?php echo site_url('Transcript_email_logs/get_list')?>",
      "type": "POST",
      "data": function ( data ) {
        // data.privilege = $("#privilege_filter").val();
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
})
</script>