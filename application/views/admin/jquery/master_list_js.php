<script>
var baseurl = "<?= base_url(); ?>";

$(document).ready(function() {  
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

      var validTypes = [
          'text/csv',
          // 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
          // 'application/vnd.ms-excel'
      ];

      if (!validTypes.includes(file.type)) {
        alert('Please select a CSV');
        return;
      }

      var formData = new FormData($('#uploadForm')[0]);

      $.ajax({
        url: baseurl+"Admin/upload_csv",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            table.ajax.reload( null, false );
            // Handle success response
            console.log(response);
            if(response.status == "success"){
              toastr.info(`Updated students: ${response.update}, New student: ${response.new}`);
            }
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error(xhr.responseText);
        }
      });
   });

  table = $('#viewresult').DataTable({ 
    "processing": true, //Feature control the processing indicator.
    "serverSide": true, //Feature control DataTables' server-side processing mode.
    "responsive": true,
    "aLengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "ALL"]],
    "iDisplayLength": 5,
    dom: 'Blfrtip',
    select: true,
    "ajax": {
      "url": "<?php echo site_url('Admin/get_students')?>",
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

  }
  });
})
</script>