<script>
var baseurl = "<?= base_url(); ?>";
function refresh_table(){
  table.ajax.reload();
}

$(document).ready(function() {  
  $(".tooltip_action").tooltip('toggleEnabled');

  table = $('#viewresult').DataTable({ 
    "processing": true, //Feature control the processing indicator.
    "serverSide": true, //Feature control DataTables' server-side processing mode.
    "responsive": true,
    "aLengthMenu": [[5, 10, 20, 50], [5, 10, 20, 50]],
    "iDisplayLength": 5,
    // dom: 'Blfrtip',
    select: true,
    "ajax": {
      "url": "<?php echo site_url('Transcript_records/get_list')?>",
      "type": "POST",
      "data": function ( data ) {
        // data.assigned_to = $("#assigned_to").val();
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

  $("#viewresult").on("click",".view",function(){
    var tr_id = this.getAttribute("tr_id");
    get_request_details(tr_id);
  })

  $("#viewresult").on("click",".download",function(){
$.ajax({
          url: baseurl+"Transcript_records/download",
                method: 'GET',
                success: function(response) {
                    // Create a blob from the response
                    var blob = new Blob([response], { type: 'application/octet-stream' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'test.xlsx'; // Update with the desired file name
                    link.click();
                },
                error: function(xhr) {
                    console.error('Download failed:', xhr.status, xhr.statusText);
                },
                xhrFields: {
                    responseType: 'blob' // Set the response type to blob
                }
            });
  });

})//end document.ready
</script>