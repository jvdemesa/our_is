<script>
$(document).ready(function() {
    $(".tooltip_btn").tooltip();
  $('#userfile').change(function(e) {
    var fileName = e.target.files[0].name;
    $('#file_label').html(fileName);
  });


  $("#submit_add").click(function (e) {
    e.preventDefault();
    var formData = new FormData($("#uploadForm")[0]);
    $.ajax({
      type: "POST",
      url: baseurl+'Admin_subjects/save',
      dataType: "JSON",
      data:formData,
      processData: false,
      contentType: false,
      success: function(data){
        $("#modal").modal("hide");
        table.ajax.reload( null, false );
        toastr.success(data.message);
      }
   });

  });

  $("#submit_edit").click(function (e) {
    $("#edit_form").submit();
  });

  $("#add").click(function() {
    $('#uploadForm')[0].reset();
    $("#id").val('');
    //set selected value for select2
    $('#status').val('0');
    //trigger change for select2
    $('#status').select2().trigger('status');
    $("#modal_title").text('Upload Media');
    $('#file_label').html("");

    $('#modal').modal('show');
  });


  $("#viewresult").on("click",".delete",function() {
      var id = this.getAttribute("id");
      var subject = this.getAttribute("subject");
      var status = this.getAttribute("status");
      if(confirm(`Are you sure you want to delete ${subject} in the subject list?`)){
        $.ajax({
          type: "POST",
          url: baseurl+'Admin_subjects/delete',
          dataType: "JSON",
          data:{id:id,id_name:"<?=$id_name?>",table:"<?=$table?>"},
          success: function(data){
            table.ajax.reload( null, false );
            toastr.success(data.message);
          }
       });
      }
  });

  $("#viewresult").on("click",".edit",function() {
    var id = this.getAttribute("id");
    var subject = this.getAttribute("subject");
    var code = this.getAttribute("code");
    var status = this.getAttribute("status");
    $("#id").val(id);
    $("#subject").val(subject);
    $("#code").val(code);
    $("#status").val(status).trigger('change');
    $("#modal").modal("show");
  });


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
        "url": "<?php echo site_url('Admin_subjects/get_list')?>",
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
       "createdRow": function (row, data, index) {
        // Initialize tooltips on created rows
        $(row).find('.tooltip_btn').tooltip();
        if(data[4]=="DISABLED"){
          $(row).addClass("uplb-red");
        }
    }
    });
});
</script>