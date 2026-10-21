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
      url: baseurl+'Admin_curriculum/save',
      dataType: "JSON",
      data:formData,
      processData: false,
      contentType: false,
      success: function(data){
        $("#modal").modal("hide");
        if(data.error == 0){
          table.ajax.reload( null, false );
          toastr.success(data.message);
        }else{
          toastr.warning(data.message);
        }
      }
   });

  });

  $("#submit_edit").click(function (e) {
    $("#edit_form").submit();
  });

  $("#add").click(function() {
    $('#uploadForm')[0].reset();
    $("#id").val('');
    $('#status').val('0').trigger('change');
    $('#ap_id').val('').trigger('change');
    $('#subjects').val('').trigger('change');
    $('#year').val('').trigger('change');
    $("#modal_title").text('Upload Media');
    $('#file_label').html("");

    $('#modal').modal('show');
  });


  $("#viewresult").on("click",".delete",function() {
      var cur_id = this.getAttribute("cur_id");
      var ap_id = this.getAttribute("ap_id ");
      var academic_program = this.getAttribute("academic_program");
      var year = this.getAttribute("year");
      if(confirm(`Are you sure you want to delete curriculum ${academic_program} ${year} in the curriculum list?`)){
        $.ajax({
          type: "POST",
          url: baseurl+'Admin_curriculum/delete',
          dataType: "JSON",
          data:{id:cur_id,id_name:"<?=$id_name?>",table:"<?=$table?>"},
          success: function(data){
            table.ajax.reload( null, false );
            toastr.success(data.message);
          }
       });
      }
  });

  $("#viewresult").on("click",".edit",function() {
    var cur_id = this.getAttribute("cur_id");
    var college_id = this.getAttribute("college_id");
    var academic_program = this.getAttribute("academic_program");
    var ap_id = this.getAttribute("ap_id");
    var subject_ids = this.getAttribute("subject_ids");
    var code = this.getAttribute("code");
    var year = this.getAttribute("year");
    var status = this.getAttribute("status");
    var subjectsArray = subject_ids.split(', ');
    $("#id").val(cur_id);
    $("#status").val(status).trigger('change');
    $("#ap_id").val(ap_id).trigger("change");
    $("#subjects").val(subjectsArray).trigger("change");
    $("#year").val(year).trigger('change');
    $("#modal_title").html(`Update ${academic_program} ${year}`);
    $("#modal").modal("show");
  });


    table = $('#viewresult').DataTable({ 
      "processing": true, //Feature control the processing indicator.
      "serverSide": true, //Feature control DataTables' server-side processing mode.
      "order": [], //Initial no order.
      responsive: true,
      "aLengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "ALL"]],
      "iDisplayLength": 10,
      dom: 'Blfrtip',
      select: true,
      "ajax": {
        "url": "<?php echo site_url('Admin_curriculum/get_list')?>",
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

    $('#multiple_subject').on('input', function() {
      var inputText = $(this).val().trim(); // Get the input text
            var subjectsDescription = inputText.split('\n'); // Split input into lines
            
            // Remove leading and trailing spaces from each line
            subjectsDescription = subjectsDescription.map(function(subjectDescription) {
                return subjectDescription.trim();
            });

            // Find corresponding values for descriptions
            var subjectsValues = subjectsDescription.map(function(subjectDescription) {
                return $('#subjects option').filter(function() {
                    return $(this).text() === subjectDescription;
                }).val();
            });

            // Clear current selection
            $('#subjects').val(null).trigger('change');

            // Select corresponding options in Select2
            $('#subjects').val(subjectsValues).trigger('change');
    });
});
</script>