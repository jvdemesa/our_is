<script>
var baseurl = "<?= base_url(); ?>";

$(document).ready(function() {  
  table = $('#viewresult').DataTable({ 
    "processing": true, //Feature control the processing indicator.
    "serverSide": true, //Feature control DataTables' server-side processing mode.
    "responsive": true,
    "aLengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "ALL"]],
    "iDisplayLength": 5,
    dom: 'Blfrtip',
    select: true,
    "ajax": {
      "url": "<?php echo site_url('Student/list')?>",
      "type": "POST",
      "data": function ( data ) {
        data.batch_id = "<?= $batch_id ?>";
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