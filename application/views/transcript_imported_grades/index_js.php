<script>
var baseurl = "<?= base_url(); ?>";

$(document).ready(function() {  
  $(".tooltip_action").tooltip('toggleEnabled');
   $('#userfile').change(function(e) {
    var fileName = e.target.files[0].name;
    $('#file_label').html(fileName);
   });

   $('#upload').click(function(e) {
      var file_from = $('#file_from').val();
      var fileInput = $('#userfile')[0];
      var file = fileInput.files[0];

      if(file_from == 'amis'){
        var upload_to = 'upload_amis_grades';
      }else{
        var upload_to = 'upload_sais_grades';
      }

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
        url: baseurl+"Transcript_imported_grades/"+upload_to,
        type: 'POST',
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function(response) {
          $('#file_label').html("");
          $("#uploadForm").trigger("reset");
          toastr.info(`Student grades added.`);
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
    // dom: 'Blfrtip',
    select: true,
    "ajax": {
      "url": "<?php echo site_url('Transcript_imported_grades/get_imported_grades')?>",
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


  // Shared download helper. The export endpoints reply with a
  // "Content-Disposition: attachment" file, and browsers never fire an iframe
  // 'load' event for attachment responses, so the file is fetched as a Blob and
  // saved from JS; this makes the response arrival/failure detectable.
  var downloadInProgress = false;

  function startDownload($btn, url, student_no, fallbackName) {
      if (downloadInProgress) {
        toastr.info('A download is already in progress. Please wait.');
        return;
      }
      downloadInProgress = true;
      $btn.prop('disabled', true);
      showLoader();

      var finished = false;
      var controller = new AbortController();
      var timeoutMs = 300000; // 5 minutes
      var timer = setTimeout(function() { controller.abort(); }, timeoutMs);

      // Single exit point: always restores the UI exactly once
      function finish(errorMessage) {
        if (finished) return;
        finished = true;
        clearTimeout(timer);
        hideLoader();
        downloadInProgress = false;
        $btn.prop('disabled', false);
        if (errorMessage) toastr.error(errorMessage);
      }

      var body = new URLSearchParams();
      body.append('student_no', student_no);

      fetch(url, {
        method: 'POST',
        body: body,
        credentials: 'same-origin',
        signal: controller.signal,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      }).then(function(response) {
        var type = (response.headers.get('Content-Type') || '').toLowerCase();
        if (!response.ok) {
          throw new Error('Server error (' + response.status + ').');
        }
        // An HTML/JSON body means the server returned a page/error, not a file
        if (type.indexOf('text/html') !== -1 || type.indexOf('json') !== -1) {
          throw new Error('The server did not return a file.');
        }
        var disposition = response.headers.get('Content-Disposition') || '';
        var match = /filename\*?=(?:UTF-8'')?"?([^";]+)"?/i.exec(disposition);
        var filename = match ? decodeURIComponent(match[1]) : fallbackName;
        return response.blob().then(function(blob) {
          if (!blob.size) throw new Error('The downloaded file is empty.');
          return { blob: blob, filename: filename };
        });
      }).then(function(result) {
        var objectUrl = URL.createObjectURL(result.blob);
        var a = document.createElement('a');
        a.href = objectUrl;
        a.download = result.filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(function() { URL.revokeObjectURL(objectUrl); }, 10000);
        finish();
      }).catch(function(err) {
        if (err && err.name === 'AbortError') {
          finish('Download timed out. Please try again.');
        } else {
          finish((err && err.message ? err.message : 'Download failed.') + ' Please try again.');
        }
      });
  }

  $("#viewresult").on("click", ".download_excel", function(e) {
      e.preventDefault();
      startDownload($(this), baseurl + 'Transcript_export/Regular', $(this).attr("student_no"), 'transcript.xlsx');
  });

  $("#viewresult").on("click", ".download_grades", function(e) {
      e.preventDefault();
      startDownload($(this), baseurl + 'Transcript_export/download_grades', $(this).attr("student_no"), 'grades.xlsx');
  });


  // New handlers for Student Transcript Information Import
  $('#userfile_admission').change(function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : '';
    $('#file_label_admission').html(fileName);
  });

  $('#upload_admission').click(function(e) {
      var fileInput = $('#userfile_admission')[0];
      var file = fileInput.files[0];

      if (!file) {
        alert('Please select a Student Info Excel file.');
        return;
      }

      // Accept xlsx and xls
      var validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel'];
      if (validTypes.indexOf(file.type) === -1 && !/\.(xls|xlsx)$/.test(file.name.toLowerCase())) {
        alert('Please select a valid Excel file (.xls or .xlsx).');
        return;
      }

      // prevent repeated submit
      $('#upload_admission').prop('disabled', true);
      $('#modal-overlay').show();

      var formData = new FormData($('#uploadAdmissionForm')[0]);

      $.ajax({
        url: baseurl+"Import_data/upload_student_admission_info",
        type: 'POST',
        data: formData,
        dataType: "json",
        processData: false,
        contentType: false,
        success: function(response) {
          $('#file_label_admission').html("");
          $("#uploadAdmissionForm").trigger("reset");
          if(response && response.status == 'success'){
            var msg = 'Import completed: ' + (response.total_rows||0) + ' rows — ' + (response.imported_count||0) + ' imported, ' + (response.duplicates_skipped||0) + ' duplicates skipped, ' + (response.invalid_rows_count||0) + ' invalid.';
            toastr.success(msg);
            if(response.invalid_rows && response.invalid_rows.length){
              // show concise error list
              var html = '<ul>';
              response.invalid_rows.slice(0,10).forEach(function(r){ html += '<li>Row '+r.row+': '+r.error+'</li>'; });
              html += '</ul>';
              toastr.info(html);
            }
          }else{
            var err = (response && response.message) ? response.message : 'Import failed. Check file and headers.';
            toastr.error(err);
          }
          $('#modal-overlay').hide();
          $('#upload_admission').prop('disabled', false);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            toastr.error('Server error during import.');
            $('#modal-overlay').hide();
            $('#upload_admission').prop('disabled', false);
        }
      });
  });

});
</script>