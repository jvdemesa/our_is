<script>
$(document).ready(function() {
  $("#nf_div").hide();
  function get_list(){
    table.clear().draw();
    $('#loader').show();
    var formData = new FormData($("#form_filter")[0]);
    $.ajax({
      type: "POST",
      url: baseurl+'Equota/get_list',
      dataType: "JSON",
      data:formData,
      processData: false,
      contentType: false,
      success: function(data){
        $('#loader').hide();
        if(data){
          $("#generate").prop("disabled",true);
          if(data[0].is_finalized == 0){
            $("#draft").prop("disabled",false);
            $("#finalize").prop("disabled",false);
            $("#download").prop("disabled",true);
            $("#nf_list").prop("disabled",true);
            $("#userfile").prop("disabled",true);
            $("#nf_div").hide();
          }else{
            $("#draft").prop("disabled",true);
            $("#finalize").prop("disabled",true);
            $("#download").prop("disabled",false);
            $("#nf_list").prop("disabled",false);
            $("#userfile").prop("disabled",false);
            $("#nf_div").show();
          }
          $.each(data, function(key, val) {
            if(val.is_finalized == 0){
              var equota = `<input type="hidden" name="id[]" value="${val.id}">
                            <input type="number" name="equota[]" value="${val.equota}" class="form-control">
                            `;
            var action = `<center>
                            <button class="btn btn-danger btn-sm delete tooltip_btn" id="${val.id}" ap="${val.ap_code}" title="DELETE">
                              <i class="fa fa-trash"></i>
                            </button>
                          </center>`;
            }else{
              var equota = val.equota;
              var action = "";
            }

            table.row.add([ val.id, 
                            val.college, 
                            val.college_code, 
                            val.academic_program, 
                            val.ap_code,
                            val.cy_year,
                            equota,
                            action,
                          ]).draw( false );
          });
          $('.tooltip_btn').tooltip();
        }else{
          var year = $("#year").val();
          var sem = $("#sem").val();
          if(year != "" && sem != "" && year != null && sem != null){
            $("#generate").prop("disabled",false);
            $("#userfile").prop("disabled",true);
            $("#download").prop("disabled",true);
            $("#nf_list").prop("disabled",true);
            $("#nf_div").hide();
          }
        }
      }
    });
  }

  function get_all_input(formData,table_name) {
  // Loop through each page of the DataTable
  $(`#${table_name}`).DataTable().$('tr', {"filter":"applied"}).each(function(){
    // Access input elements within each row and append their data to formData
    $(this).find('input[type="text"], input[type="checkbox"], input[type="radio"], select, textarea').each(function(){
      var fieldName = $(this).attr('name');
      var fieldValue = $(this).val();
      formData.append(fieldName, fieldValue);
    });
  });
}

  $(".tooltip_btn").tooltip();
  var table = $('#viewresult').DataTable({ 
    "order": false, //Initial no order.
    responsive: true,
    "aLengthMenu": [[5, 10, 20, 50], [5, 10, 20, 50]],
    "iDisplayLength": 5,
    dom: 'Blfrtip',
    buttons: [{ extend: 'excel',
                title:"Equota",
                exportOptions: {
                    columns: [1,2,3,4,6],
                }
              },
              {
              text: 'View Upload NF Template',
              className: 'btn btn-primary',
              action: function () {
                window.location.href = 'download_nf_template';
              }
            }],
  });

  var nf_table = $('#nf_table').DataTable({ 
    "order": false, //Initial no order.
    responsive: true,
    "aLengthMenu": [[5, 10, 20, 50], [5, 10, 20, 50]],
    "iDisplayLength": 5,
    dom: 'Blfrtip',
    buttons: [{ extend: 'excel',
                title:"Equota",
                exportOptions: {
                    columns: [1,2,3,4,6],
                }
         }],
  });

  $("#year").change(function (e) {
    e.preventDefault();
    get_list();
  });

  $("#sem").change(function (e) {
    e.preventDefault();
    get_list();
  });

  $("#generate").click(function(e){
    e.preventDefault();
    var formData = new FormData($("#form_filter")[0]);
    $.ajax({
      type: "POST",
      url: baseurl+'Equota/generate',
      dataType: "JSON",
      data:formData,
      processData: false,
      contentType: false,
      success: function(data){
        if(data){
          toastr.success("Equota data has been generated. You may update the Equota in table below.");
          get_list();
        }
      }
    })
  })

  $("#draft").click(function(e){
    e.preventDefault();
    var formData = table.$('input,select,textarea').serialize();
    $.ajax({
      type: "POST",
      url: baseurl+'Equota/save_draft',
      dataType: "JSON",
      data:formData,
      success: function(data){
        if(data){
          toastr.success("Data has been saved as DRAFT");
          get_list();
        }
      }
    })
  })

  $("#finalize").click(function(e){
    e.preventDefault();
    var formData = table.$('input,select,textarea').serialize();
    $.ajax({
      type: "POST",
      url: baseurl+'Equota/finalize',
      dataType: "JSON",
      data:formData,
      success: function(data){
        if(data){
          toastr.success("Data has been FINALIZED");
          get_list();
        }
      }
    })
  })

  $("#download").click(function(e) {
    e.preventDefault();
    var formData = new FormData($("#form_filter")[0]);
    $.ajax({
      url: baseurl+'Excel_export/equota',
      type: 'POST',
      data:formData,
      processData: false,
      contentType: false,
      xhrFields: {responseType: 'blob' },// Set response type to blob (binary data)
      success: function(response, status, xhr) {
         // Check if the request was successful
        if (xhr.status === 200) {
          // Create a blob object from the response
          var blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
          
          // Create a temporary anchor element
          var link = document.createElement('a');
          link.href = window.URL.createObjectURL(blob);
          var year = $("#year").val();
          var sem = $("#sem").val()==1?"1st_Sem":"2nd_Sem";
          var filename=`${year}_${sem}_EQUOTA`;
          link.download = `${filename}.xlsx`; // Set the filename
          document.body.appendChild(link);
          
          // Trigger the download
          link.click();
          
          // Cleanup
          document.body.removeChild(link);
          window.URL.revokeObjectURL(link.href);
          toastr.success("File downloaded successfully!");
        }
      },
      error: function(xhr, status, error) {
          // Handle any errors
          toastr.warning("Error occurred:", error);
      }
    });

    $.ajax({
      url: baseurl+'Excel_export/dummy_students',
      type: 'POST',
      data:formData,
      processData: false,
      contentType: false,
      xhrFields: {responseType: 'blob' },// Set response type to blob (binary data)
      success: function(response, status, xhr) {
         // Check if the request was successful
        if (xhr.status === 200) {
          // Create a blob object from the response
          var blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
          
          // Create a temporary anchor element
          var link = document.createElement('a');
          link.href = window.URL.createObjectURL(blob);
          var year = $("#year").val();
          var sem = $("#sem").val()==1?"1st_Sem":"2nd_Sem";
          var filename=`${year}_${sem}_Students`;
          link.download = `${filename}.xlsx`; // Set the filename
          document.body.appendChild(link);
          
          // Trigger the download
          link.click();
          
          // Cleanup
          document.body.removeChild(link);
          window.URL.revokeObjectURL(link.href);
          toastr.success("File downloaded successfully!");
        }
      },
      error: function(xhr, status, error) {
          // Handle any errors
          toastr.warning("Error occurred:", error);
      }
    });
  });

  $("#viewresult").on("click",".delete",function(e) {
    e.preventDefault();
    var id = this.getAttribute("id");
    var ap = this.getAttribute("ap");
    if(confirm(`Are you sure you want to delete ${ap} in the list?`)){
      $.ajax({
        type: "POST",
        url: baseurl+'Equota/delete',
        dataType: "JSON",
        data:{id:id},
        success: function(data){
          get_list();
          toastr.success(`${ap} has been deleted`);
        }
      });
    }
  });

  $(document).on('keydown', 'input[name="equota[]"]', function(e) {
      if (e.keyCode === 9) { // Check if the pressed key is the tab key
          e.preventDefault(); // Prevent the default tab behavior

          var currentIndex = $('input[name="equota[]"]').index(this); // Get the index of the currently focused input field

          if (currentIndex !== -1) { // If the currently focused input field exists
              var nextIndex = currentIndex + 1; // Calculate the index of the next input field
              var inputs = $('input[name="equota[]"]'); // Get all input fields with name "id[]"

              // Loop through the input fields to find the next one
              for (var i = nextIndex; i < inputs.length; i++) {
                  var input = inputs.eq(i);
                  if (!input.is(':disabled') && !input.is(':hidden')) { // Check if the input field is not disabled or hidden
                      input.focus(); // Focus on the next input field
                      input.select(); // Select the text in the input field
                      break; // Exit the loop
                  }
              }
          }
      }
  });

  $("#nf_sched").click(function(e){
    e.preventDefault();
    $("#uploadForm").trigger("reset");
    $('#file_label').html("");
    $("#modal").modal("show");
  })

   $('#userfile').change(function(e) {
    var fileName = e.target.files[0].name;
    $('#file_label').html(fileName);
   });

  $('#upload_nf').click(function(e) {
      var fileInput = $('#userfile')[0];
      var file = fileInput.files[0];

      if (!file) {
          alert('Please select a file.');
          return;
      }

      if (file.type !== 'text/csv' && file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' && file.type !== 'application/vnd.ms-excel') {
          // File type is not CSV or Excel
          alert('Please upload a CSV or Excel file.');
          return;
      }

      var formData = new FormData($('#form_filter')[0]);
      showLoader();
      $.ajax({
          url: baseurl + "Excel_export/check_nf_sched",
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: "json", // lowercase "json"
          success: function(response, status, xhr) {
              // Check if the request was successful
              if (xhr.status === 200) {
                  if (response.error === 1) { // Use triple equals to compare
                    var count = response.data.length;
                      toastr.error(`${count} underload has been detected! you can check it in the NF Modal.`)
                  } else {
                      toastr.success("Success! You can download the file in the NF Modal.");
                  }
              }
          },
          error: function(xhr, status, error) {
              // Handle any errors
              toastr.error("Error occurred: " + error);
          },
          complete: function(xhr, status) { // Adjusted complete callback
              hideLoader();
          }
      });
      $("#file_label").html("");
      $("#userfile").val("");
  });

   $('#nf_table').on("click",".download_nf",function(e) {
      var sem = this.getAttribute("sem");
      var year = this.getAttribute("year");
      var date_uploaded = this.getAttribute("date_uploaded");
      $(this).tooltip('hide');
      showLoader();
      $.ajax({
        type: 'POST',
        url: baseurl+"Excel_export/download_nf_sched",
        data: {sem:sem,year:year,date_uploaded:date_uploaded},
        xhrFields: {responseType: 'blob' },// Set response type to blob (binary data)
        success: function(response, status, xhr) {
            toastr.success("File downloaded successfully!");
            // Create a blob object from the response
            var blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
            
            // Create a temporary anchor element
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            var filename=`${year}_${sem}_NF_Sched`;
            link.download = `${filename}.xlsx`; // Set the filename
            document.body.appendChild(link);
            
            // Trigger the download
            link.click();
            
            // Cleanup
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);
        },
        error: function(xhr, status, error) {
            // Handle any errors
            toastr.warning("Error occurred:", error);
        },
        complete: function(){
          hideLoader();
        }
      });
   });


   $('#nf_table').on("click",".download_nf_subj",function(e) {
      var sem = this.getAttribute("sem");
      var year = this.getAttribute("year");
      var date_uploaded = this.getAttribute("date_uploaded");
      $(this).tooltip('hide');
      showLoader();
      $.ajax({
        type: 'POST',
        url: baseurl+"Excel_export/download_nf_subj",
        data: {sem:sem,year:year,date_uploaded:date_uploaded},
        xhrFields: {responseType: 'blob' },// Set response type to blob (binary data)
        success: function(response, status, xhr) {
            toastr.success("File downloaded successfully!");
            // Create a blob object from the response
            var blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
            
            // Create a temporary anchor element
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            var filename=`${year}_${sem}_NF_Difference`;
            link.download = `${filename}.xlsx`; // Set the filename
            document.body.appendChild(link);
            
            // Trigger the download
            link.click();
            
            // Cleanup
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);
        },
        error: function(xhr, status, error) {
            // Handle any errors
            toastr.warning("Error occurred:", error);
        },
        complete: function(){
          hideLoader();
        }
      });
   });

   document.getElementById("userfile").addEventListener("change", function() {
    if (this.disabled) {
        this.nextElementSibling.classList.remove("hoverable");
    } else {
        this.nextElementSibling.classList.add("hoverable");
    }
  });

  $("#nf_list").click(function(e){
    e.preventDefault();
    var sem = $("#sem").val();
    var year = $("#year").val();
    nf_table.clear().draw();
    showLoader();
    $.ajax({
      url: baseurl+"equota/get_nf_list",
      type: 'POST',
      data: {sem:sem,year:year},
      dataType: "JSON",
      // processData: false,
      // contentType: false,
      success: function(data) {
        $.each(data, function(key, val) {
          var action = `<center>
                        <div class="btn btn-group">
                          <button class="btn btn-info btn-sm download_nf tooltip_btn" sem="${val.sem}" year="${val.year}" date_uploaded="${val.date_uploaded}" title="Download NF Schedule">
                            <i class="fa fa-download"></i>
                          </button>`;
          if(val.underload != 0){
          action += `
                          <button class="btn btn-danger btn-sm download_nf_subj tooltip_btn" sem="${val.sem}" year="${val.year}" date_uploaded="${val.date_uploaded}" title="Download Underload Subject">
                            <i class="fa fa-download"></i>
                          </button>
                        `;
          }
          if(val.regist_status == 1 || val.regist_status == 0){
            action += `
                            <button class="btn btn-warning btn-sm generate_sections tooltip_btn" sem="${val.sem}" year="${val.year}" date_uploaded="${val.date_uploaded}" title="Generate Sections">
                              <i class="fa fa-refresh"></i>
                            </button>
                          `;
          }
          action += `</div></center>`;
          nf_table.row.add([ val.year, 
                          val.sem, 
                          val.date_uploaded, 
                          val.underload, 
                          action,
                        ]).draw( false );
        });
        $("#nf_modal").modal("show");
        $('.tooltip_btn').tooltip();

      },
      complete: function(){
        hideLoader();
      }
    });
  })

  $('#nf_table').on("click",".generate_sections",function(e) {
    showLoader();
    var sem = this.getAttribute("sem");
    var year = this.getAttribute("year");
    var date_uploaded = this.getAttribute("date_uploaded");
    $(this).tooltip('hide');
    showLoader();
    $.ajax({
      type: 'POST',
      url: baseurl+"Equota/generate_regist_sections",
      data: {sem:sem,year:year,date_uploaded:date_uploaded},
      // dataType: "json", // lowercase "json"
      success: function(data) {
        if(data){
          toastr.success("Section Generated.");
        }else{
          toastr.success("Something went wrong.");
        }
      },
      error: function(xhr, status, error) {
          // Handle any errors
          toastr.warning("Error occurred:", error);
      },
      complete: function(){
        hideLoader();
        $("#nf_modal").modal("hide");
      }
    });
  });
});
</script>