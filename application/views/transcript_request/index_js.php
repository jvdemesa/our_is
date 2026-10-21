<script>
var baseurl = "<?= base_url(); ?>";
var cur_status, active_step, readonly_status, status_header, btn_text, file_id;
function refresh_table(){
  table.ajax.reload();
}

$(document).ready(function() {  
  $(".tooltip_action").tooltip('toggleEnabled');

  $("#refresh_requests").click(function(e){
    showLoader();
    e.preventDefault();
    $.ajax({
      url: baseurl+"Google_sheets/update_data",
      type: 'POST',
      dataType: "json",
      success: function(response) {
        if(response.with_data == true){
          if(response.error==0){
            toastr.info("Request data has been updated.");
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

  $("#refresh_payments").click(function(e){
    showLoader();
    e.preventDefault();
    $.ajax({
      url: baseurl+"Google_sheets/update_payments",
      type: 'POST',
      dataType: "json",
      success: function(response) {
        if(response.with_data == true){
          toastr.info("Payment data has been updated.");
          if(response.not_found!=0){
            if(response.error==0){
              alert(`${response.not_found} New payment/s with no matching transaction code has been detected. Please check and update manually.`);
            }else{
              toastr.info("Something went wrong. Try again later.");
            }
          }
          table.ajax.reload( null, false );
        }else{
          toastr.info("No new payment.");
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

  var table_lname = $('#table_lname').DataTable({ 
    "order": false, //Initial no order.
    responsive: true,
    "aLengthMenu": [[5, 10, 20, 50], [5, 10, 20, 50]],
    "iDisplayLength": 5,
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
      "url": "<?php echo site_url('Transcript_request/get_request_list')?>",
      "type": "POST",
      "data": function ( data ) {
        data.assigned_to = $("#assigned_to").val();
        data.status = $("#select_status").val();
        data.active_status = $("#active_status").val();
        data.is3days = $("#is3days").val();
      },
      "dataSrc": function(json){
        $("#box1").html(json.box1);
        $("#box2").html(json.box2);
        $("#request_last_update").html(json.request_last_update);
        $("#payment_last_update").html(json.payment_last_update);
        return json.data;
      }
    },
    //Set column definition initialisation properties.
   "ordering": false,
    createdRow: function( row, data, dataIndex ) {
      // Initialize tooltips on created rows
      $(row).find('.tool_tip').tooltip();
      // if(data[11]=='1'){
      //   $(row).addClass("text-danger");
      // }
      // if(data[11]=='2'){
      //   $(row).addClass("uplb-bg-red text-white");
      // }      
      if(data[13]=='1'){
        $(row).addClass("uplb-bg-yellow text-white");
      }
      if(data[13]=='2'){
        $(row).addClass("uplb-bg-red text-white");
      }
      if(data[14]){
        $('td', row).eq(3).addClass('text-danger');
      }
    }
  });

  $("#viewresult").on("click",".view",function(){
    var tr_id = this.getAttribute("tr_id");
    get_request_details(tr_id);
  })

  $("#details_modal").on("click","#check_dup",function(){
    var tr_id = this.getAttribute("tr_id");
    get_request_details(tr_id);
  })

  $("#requests_tbody").on("keyup change",".pages",function(){
    var price = parseInt($(this).closest("tr").find('.price').text());
    var quantity = parseInt($(this).closest("tr").find('.quantity').val());
    var pages = parseInt($(this).val());
    var total = quantity * pages * price;
    $(this).closest("tr").find('.total').val(total);
    var grandTotal = 0;
    $("#requests_tbody .total").each(function() {
        var rowTotal = parseInt($(this).val());
        if (!isNaN(rowTotal)) {
            grandTotal += rowTotal;
        }
    });
    $("#grand_total").val(grandTotal);
    $("#grand_total_display").html(grandTotal);
  })

  $("#requests_tbody").on("keyup change",".quantity",function(){
    var price = parseInt($(this).closest("tr").find('.price').text());
    var pages = parseInt($(this).closest("tr").find('.pages').val());
    var quantity = parseInt($(this).val());
    var total = quantity * pages * price;
    $(this).closest("tr").find('.total').val(total);
    var grandTotal = 0;
    $("#requests_tbody .total").each(function() {
        var rowTotal = parseInt($(this).val());
        if (!isNaN(rowTotal)) {
            grandTotal += rowTotal;
        }
    });
    $("#grand_total").val(grandTotal);
    $("#grand_total_display").html(grandTotal);
  })

  $("#details_modal").on("click","#confirm_btn",function(e){
    showLoader();
    if (!$('#form1')[0].checkValidity()) {
      $('#form1').find('input[type="submit"]').click();
      $("#form1")[0].reportValidity();
      $('#modal-overlay').hide()
      return false;
    }else{
      e.preventDefault();
      var status = $("#status").val();
      switch(status){
        case "0":
          var submit_url = baseurl+'Transcript_request/save_assessment';
          confirm_function(submit_url);
        break;
        case "1":
          var submit_url = baseurl+'Email/assessment';
          confirm_function(submit_url);
        break;
        case "3":
          $("#payment_modal").modal("show");
          hideLoader();
        break;
        case "4":
          if(confirm("Confirm email release schedule?")){
            var submit_url = baseurl+'Email/release_schedule';
            confirm_function(submit_url);
          }else{
            hideLoader();
          }
        break;
      }
    }
  })

  $("#details_modal").on("click","#update_btn",function(e){
    showLoader();
    if (!$('#form1')[0].checkValidity()) {
      $('#form1').find('input[type="submit"]').click();
      $("#form1")[0].reportValidity();
      $('#modal-overlay').hide()
      return false;
    }else{
      e.preventDefault();
      var status = $("#status").val();
      var submit_url = baseurl+'Transcript_request/update_assessment';
      confirm_function(submit_url);
    }
  })

  $("#confirm_payment_btn").click(function(e){
    if(confirm("Confirm verification of payment?")){
      showLoader();
      e.preventDefault();
      var submit_url = baseurl+'Email/payment_confirmation';
      confirm_function(submit_url);
      $("#payment_modal").modal("hide");
    }
  })

  function confirm_function(submit_url){
    var formData = new FormData($("#form1")[0]);
    $.ajax({
      type: "POST",
      url: submit_url,
      dataType: "JSON",
      data:formData,
      processData: false,
      contentType: false,
      success: function(data){
        if(data.error){
          toastr.warning(data.message);
        }else{
          toastr.success(data.message);
          table.ajax.reload( null, false );
        }
        get_request_details(data.trd_id);
      },
      error: function(xhr, status, error) {
          toastr.warning("Something Went Wrong. Please Try Again Later.");
          // Handle error
          console.error(xhr.responseText);
          hideLoader();
      }
    });
    
  }

  $('#requests_tbody').on('click', 'tr', function(event) {
    // Check if control (or command on Mac) key is pressed during the click
    if (event.ctrlKey || event.metaKey) {
          console.log("asd");

      // Toggle the "selected" class
      $(this).toggleClass('selected');
    }
  });

  $("#add_request").click(function(e){
    e.preventDefault();
    $("#request_quantity").val("1");
    $("#request_id").val("");
    $("#add_request_modal").modal("show");
  });

  $("#confirm_add_request").click(function(e){
    var request_id = $("#request_id").val();
    var request_quantity = $("#request_quantity").val();
    var trans_code = $("#trans_code").val();
    var trd_id = $("#trd_id").val();
    var status = $("#status").val();
    if(request_id!=null && request_quantity!=null){
      $.ajax({
        type: "POST",
        url: baseurl+'Transcript_request/add_request',
        dataType: "JSON",
        data:{request_id:request_id, request_quantity:request_quantity, trans_code:trans_code, status:status},
        success: function(data){
          $("#add_request_modal").modal("hide");
          get_request_details(trd_id, false);
        },
        error: function(xhr, status, error) {
            toastr.warning("Something Went Wrong. Please Try Again Later.");
            // Handle error
            console.error(xhr.responseText);
            hideLoader();
        }
      });
    }else{
      alert("Please select request type and input quantity.");
    }
  })

  $("#viewresult").on("click",".active_status",function(){
    showLoader();
    var trans_code = this.getAttribute("trans_code");
    $.ajax({
      type: "POST",
      url: baseurl+'Transcript_request/get_active_status_data',
      dataType: "JSON",
      data:{trans_code:trans_code },
      success: function(data){
        if(data){
          var tbody = '';
          $.each(data,function(key,val){
            switch(val.active_status){
              case "0":
                var active_status = "Active / Enabled";
              break;
              case "1":
                var active_status = "On-Hold";
              break;
              case "2":
                var active_status = "Inactive / Disabled";
              break;
            }
            tbody+=`<tr>
                      <td>${active_status}</td>
                      <td>${val.remarks}</td>
                      <td>${val.updated_by}</td>
                      <td>${val.datetime}</td>
                    </tr>`;
          })
        }else{
          toastr.warning("No update on active status.");
        }
        $("#hold_tbody").html(tbody);
        $("#hold_trans_code").val(trans_code);
        $("#hold_remarks").val("");
        $("#hold_modal").modal("show");
        hideLoader();
      },
      error: function(xhr, status, error) {
          toastr.warning("Something Went Wrong. Please Try Again Later.");
          // Handle error
          console.error(xhr.responseText);
          hideLoader();
      }
    });  

  });

  $("#viewresult").on("click",".look_up",function(e){
    e.preventDefault();
    showLoader();
    var fname = this.getAttribute("fname");
    var mname = this.getAttribute("mname");
    var lname = this.getAttribute("lname");
    $("#modal_title_lname").html(`${lname}, ${fname} ${mname}`);
    table_lname.clear().draw();
    $.ajax({
      type: "POST",
      url: baseurl+'Transcript_request/look_up',
      dataType: "JSON",
      data:{lname:lname, fname:fname},
      success: function(data){
        if(data){
          $.each(data,function(key,val){
            table_lname.row.add([ val.fullname, 
                            val.filename, 
                          ]).draw( false );
          })
          $("#lname_modal").modal("show");
        }else{
          toastr.info("No record found.");
        }
        hideLoader();
      },
      error: function(xhr, status, error) {
          toastr.warning("Something Went Wrong. Please Try Again Later.");
          // Handle error
          console.error(xhr.responseText);
          hideLoader();
      }
    });  

  });

  $("#confirm_active_status_btn").click(function(e){
    e.preventDefault();
    var trans_code = $("#hold_trans_code").val();
    var active_status = $("#active_status_option").val();
    var hold_remarks = $("#hold_remarks").val();
    $.ajax({
      type: "POST",
      url: baseurl+'Transcript_request/add_active_status',
      dataType: "JSON",
      data:{hold_remarks:hold_remarks, active_status,active_status,trans_code:trans_code },
      success: function(data){
        var tbody = "";
          $.each(data,function(key,val){
            switch(val.active_status){
              case "0":
                var active_status = "Active / Enabled";
              break;
              case "1":
                var active_status = "On-Hold";
              break;
              case "2":
                var active_status = "Inactive / Disabled";
              break;
            }
            tbody+=`<tr>
                      <td>${active_status}</td>
                      <td>${val.remarks}</td>
                      <td>${val.updated_by}</td>
                      <td>${val.datetime}</td>
                    </tr>`;
          })
        $("#hold_tbody").html(tbody);
        table.ajax.reload( null, false );
        toastr.warning("Active Status has been updated / added.");
      },
      error: function(xhr, status, error) {
          toastr.warning("Something Went Wrong. Please Try Again Later.");
          // Handle error
          console.error(xhr.responseText);
          hideLoader();
      }
    });  
  })

  $("#viewresult").on("click",".reassess",function(){
    if(confirm("Are you sure you want to return this request to assessment step?")){
      var tr_id = this.getAttribute("tr_id");
      $.ajax({
        type: "POST",
        url: baseurl+'Transcript_request/reassess',
        dataType: "JSON",
        data:{tr_id:tr_id},
        success: function(data){
          if(data){
            refresh_table();
            toastr.info("Request has been return to assessment step!");
          }else{
            toastr.warning("Return to assessment failed. Try again!");
          }
        },
        error: function(xhr, status, error) {
            toastr.warning("Something Went Wrong. Please Try Again Later.");
            // Handle error
            console.error(xhr.responseText);
            hideLoader();
        }
      });      
    }
  })
 $('#check_all').change(function() {
      // Check or uncheck all checkboxes with class 'check_request'
      $('.check_request').prop('checked', $(this).prop('checked'));
      is_checked();
  });

  // When any checkbox with class 'check_request' is changed
  $('#request_table').on("change",".check_request",function() {
      // Uncheck 'Check All' if any 'check_request' checkbox is unchecked
    updateCheckAllCheckbox();
    is_checked();
  });

  $('.nav-link').on('shown.bs.tab', function (e) {
      var activeTab = $(e.target); // newly activated tab
      active_step = activeTab.attr('id');
      toggle_button();
  });

  $('#print_btn').click(function(e){
    e.preventDefault();
    showLoader();
    var trans_code = $("#trans_code").val();
    $.ajax({
      type: "POST",
      url: baseurl+'Transcript_request/print',
      data:{trans_code:trans_code},
      success: function(data){
        table.ajax.reload( null, false );
        var printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write('</head><body >');
        printWindow.document.write(data);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        setTimeout(function() {
            printWindow.close();
        }, 500);

        printWindow.print();

        setTimeout(function() {
            printWindow.close();
        }, 500);

        hideLoader();
      },
      error: function(xhr, status, error) {
          toastr.warning("Could not load the content to print.");
      }
    });
  });

  $('#is3days').on('change', function() {
    if ($(this).is(':checked')) {
      $('#is3days').val(1);
    } else {
      $('#is3days').val(0);
    }
          table.ajax.reload();

  });

  $("#new_request_3_days").click(function(){
    $("#is3days").val(1);
    $("#is3days").prop("checked",true);
    $("#select_status").val("0").change();
  })

  $("#payment_3_days").click(function(){
    $("#is3days").val(1);
    $("#is3days").prop("checked",true);
    $("#select_status").val("3").change();
  })

  $("#clear_filters").click(function(){
    $("#assigned_to").val("").change();
    $("#select_status").val("").change();
    $("#active_status").val("").change();
    table.search( '' ).columns().search( '' ).draw();
    $("#is3days").prop("checked",false);
  })

  function get_request_details(tr_id, goto_step1 = true){
    $.ajax({
      url: baseurl+"Transcript_request/get_request_details",
      type: 'POST',
      data: {tr_id:tr_id},
      dataType: "json",
      success: function(response) {
        if(response){
          var iframe = document.getElementById('contentFrame');
          iframe.src =  ``;
          if(goto_step1){
            $('.nav-link.active').removeClass('active');
            $('.tab-pane.active').removeClass('active show');

            // Set Step 1 tab as active
            $('#step1-tab').addClass('active');
            $('#step1').addClass('active show');

            // Optionally, trigger tab change event (if needed)
            $('#step1-tab').tab('show');
          }
          var schools ="", requests;
          var cross_reg = response.details.is_cross_reg==1?"Yes":"No";
          var activeTab = $("#details_modal").find('.nav-link.active'); // newly activated tab
          active_step = activeTab.attr('id');
          cur_status = response.details.status;
          if(response.details.file_link!=null){
            var url = new URL(response.details.file_link);
            file_id = url.searchParams.get('id');
          }

          toggle_button();
          $("#tr_status_display").html(status_header);
          $("#staff_name").val(response.details.staff_name);
          $("#trd_id").val(response.details.id);
          $("#status").val(response.details.status);
          $("#email").val(response.details.email);
          $("#trans_code").val(response.details.trans_code);
          $("#fname").html(response.details.fname);
          $("#mname").html(response.details.mname);
          $("#lname").html(response.details.lname);
          $("#sex").html(response.details.sex);
          $("#student_no").html(response.details.student_no);
          $("#email_display").html(response.details.email);
          $("#birthday").html(response.details.birthday);
          $("#pob").html(response.details.pob);
          $("#fathers_name").html(response.details.fathers_name);
          $("#mothers_name").html(response.details.mothers_name);
          $("#cleared_date").html(response.details.date_approved);
          $("#sem_admitted").html(response.details.sem_admitted);
          $("#ay_admitted").html(response.details.ay_admitted);
          $("#admitted_as").html(response.details.admitted_as);
          $("#sem_last_enrolled").html(response.details.sem_last_enrolled);
          $("#ay_last_enrolled").html(response.details.ay_last_enrolled);
          $("#degree").html(response.details.degree);
          $("#student_status").html(response.details.student_status);
          $("#student_status").html(response.details.student_status);
          $("#cross_reg").html(cross_reg);
          $("#up_campus").html(response.details.up_campus);
          $("#trans_code_display").html(response.details.trans_code);
          $("#staff_name_display").html(response.details.staff_name);
          var dup_transcode = "";
          if(response.dup_stud_no || response.dup_name){
            if(response.dup_stud_no){
              $.each(response.dup_stud_no,function(key,val){
                if(key == 0){
                  dup_transcode += `<a href="#" id="check_dup" tr_id="${val.id}">${val.trans_code}</a>`;
                }else{
                  dup_transcode += `, <a href="#" id="check_dup" tr_id="${val.id}">${val.trans_code}</a>`;
                }
              })
            }else{
              $.each(response.dup_name,function(key,val){
                if(key == 0){
                  dup_transcode += `<a href="#" id="check_dup" tr_id="${val.id}">${val.trans_code}</a>`;
                }else{
                  dup_transcode += `, <a href="#" id="check_dup" tr_id="${val.id}">${val.trans_code}</a>`;
                }
              })
            }
            toastr.error("Possible duplicate request!");
            $("#dup_note").html(`<i class="fa fa-warning"></i>Warning: Possible duplicate with transcode ${dup_transcode}`);
          }else{
            $("#dup_note").html("");
          }

          if(response.details.purpose != null){
            $("#otr_purpose").html(`${response.details.purpose}`);
          }


          $.each(response.schools,function(key,val){
            switch(val.type){
              case "0":
                var type = "Associate Deg";
              break;
              case "1":
                var type = "College";
              break;
              case "2":
                var type = "Master's";
              break;
              case "3":
                var type = "PhD";
              break;
              default:
                var type = "";
              break;
            }
            schools +=`<tr>
                      <td>${type}</td>
                      <td>${val.name}</td>
                      <td>${val.location}</td>
                      <td>${val.course}</td>
                      <td>${val.year_month_graduated}</td>
                    </tr>
                  `;
          })

          var grand_total = 0;
          if(response.requests.length === 0){
            requests =`<tr><td colspan="6">No Request indicated</td></tr>`;
          }else{
            $.each(response.requests,function(key,val){
              var readonly = val.is_per_page == 1?``:"readonly";
              var pages = val.is_per_page == 1?val.pages==null?"":val.pages:"1";
              var total = pages!=""?parseInt(val.quantity) * parseInt(pages) * parseInt(val.price):"";
              // var request = val.purpose!=1
              grand_total += $.isNumeric(total)?total:0;
              if(val.is_per_page == 1 && response.details.status==0){
                var bg = "bg-warning";
              }else{
                var bg = "";
              }

              if(val.status == 1 && response.details.status>=4){
                var bg = "bg-info";
              }else{
                var bg = "";
              }

              if((response.details.status >= 2 && val.quantity!=0) || (response.details.status <= 1)){
                switch(val.status){
                  case "0":
                    var req_status = "On-Process";
                  break;
                  case "1":
                    var req_status = `For release<hr>${val.ready_date}<br>${val.ready_by}`;
                  break;
                  case "2":
                    var req_status = `<span class='text-danger'>Released<hr>${val.release_date}<br>${val.released_by}</span>`;
                  break;
                }
                if((val.status == "0") && cur_status == "4" && val.quantity !=0 && val.pages !=0){
                  if(response.others_title && val.rt_id=="25"){
                   var col1=`<div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-danger check_request" type="checkbox" id="customCheckbox${val.id}" value="${val.id}" name="cb_trq_id[]" checked>
                            <label for="customCheckbox${val.id}" class="custom-control-label">${val.request} ( ${response.others_title[0].title} )</label>
                          </div>`;
                  }else{
                   var col1=`<div class="custom-control custom-checkbox">
                            <input class="custom-control-input custom-control-input-danger check_request" type="checkbox" id="customCheckbox${val.id}" value="${val.id}" name="cb_trq_id[]" checked>
                            <label for="customCheckbox${val.id}" class="custom-control-label">${val.request}</label>
                          </div>`;
                  }
                $("#check_all").prop( "checked", true );
                }else{
                  if(response.others_title && val.rt_id=="25"){
                    var col1 = val.request+" "+response.others_title[0].title;
                  }else if(val.rt_id=="11" && val.is_for_visa == 1){
                    var col1 = val.request+"(For Visa)";
                  }else{
                    var col1 = val.request;
                  }
                }
                requests +=`<tr class="${bg}" id="${val.id}">
                            <td>
                              ${col1}
                            </td>
                            <td class="price">${val.price}</td>
                            <td>
                              <input type="text" class="form-control quantity numOnly" name="quantity[]" ${readonly_status} value="${val.quantity}" required>
                            </td>
                            <td>
                              <input type="hidden" name="trq_id[]" value="${val.id}">
                              <input type="text" class="form-control pages numOnly" name="pages[]" ${readonly} ${readonly_status} value="${pages}" required>
                            </td>
                            <td>
                              <input type="text" class="form-control total" name="total[]" readonly value="${total}" required>
                            </td>
                            <td>
                            ${req_status}
                            </td>
                          </tr>
                        `;
              }
            })
          }
          var uploads_html = "<ul>";
          $.each(response.uploads,function(key,val){
            // var url = new URL(val.link);
            var fileId = val.link;
            var link =  `https://drive.google.com/file/d/${fileId}/preview`;

            uploads_html +=`<li><a href="${link}" target="contentFrame">${val.type}</a></li>`;
          })
          uploads_html += "</ul>";
          $("#grand_total").val(grand_total);
          $("#uploads_list").html(uploads_html);
          $("#grand_total_display").html(`${grand_total}`);
          $("#schools_tbody").html(schools);
          $("#requests_tbody").html(requests);
          $("#details_modal").modal("show");
        }
        hideLoader();
      },
      error: function(xhr, status, error) {
          // Handle error
          console.error(xhr.responseText);
          hideLoader();
      }
    });
  }//end get_request_details function

  function toggle_button(){
    $("#update_btn").hide();
    switch(cur_status){
      case "0":
        readonly_status = "";
        btn_text = "<i class='fa fa-save'></i> Save Assessment";
        if (active_step == "step4-tab") {
          $("#confirm_btn").show();
        }else{
          $("#confirm_btn").hide();
        }
        $("#confirm_btn").html(btn_text);
        status_header = "New Request";
      break;
      case "1":
        readonly_status = "readonly";
        btn_text = "<i class='fa fa-send'></i> Email Assessment";
        $("#confirm_btn").show();
        $("#confirm_btn").html(btn_text);
        status_header = "Assessed";
      break;
      case "2":
        readonly_status = "readonly";
        status_header = "Assessment Emailed";
        $("#confirm_btn").hide();
      break;
      case "3":
        var iframe = document.getElementById('payment_iframe');
        iframe.src =  `https://drive.google.com/file/d/${file_id}/preview`;

        readonly_status = "readonly";
        btn_text = "<i class='fa fa-save'></i> View Payment";
        $("#confirm_btn").show();
        $("#confirm_btn").html(btn_text);
        status_header = "Proof of payment received";
      break;
      case "4":
        readonly_status = "";
        btn_text = "<i class='fa fa-send'></i> Email Release Schedule";
        if (active_step == "step4-tab") {
          $("#update_btn").show();
          $("#confirm_btn").show();
        }else{
          $("#confirm_btn").hide();
        }
        $("#confirm_btn").html(btn_text);
        status_header = "Payment Confirmed";
      break;
      case "5":
        readonly_status = "readonly";
        // var btn_text = "<i class='fa fa-send'></i> Email Release Schedule";
        $("#confirm_btn").hide();
        // $("#confirm_btn").html(btn_text);
        status_header = "For release";
      break;
      case "6":
        status_header = "Released";
        $("#confirm_btn").hide();
      break;
    }
  }
  // Function to update 'Check All' checkbox
  function updateCheckAllCheckbox() {
    if(cur_status == "4"){
      var allChecked = true;
      $('.check_request').each(function() {
          if (!$(this).prop('checked')) {
              allChecked = false;
              return false; // exit each loop early
          }
      });
      $('#check_all').prop('checked', allChecked);
    }
  }
  // Function to show button if any of the checkbox is checked
  function is_checked() {
    if(cur_status == "4"){
      var is_checked = false;
      $('.check_request').each(function() {
          if ($(this).prop('checked')) {
            is_checked = true;
            return true;
          }
      });
      if(is_checked == false){
        $("#confirm_btn").hide();
        return false;
      }else{
        $("#confirm_btn").show();
      }
    }
  }


  $("#viewresult").on("click",".update_otr_status ",function(e){
    if(confirm("Confirm updating of OTR status.")){
      showLoader();
      var otr_step = this.getAttribute("otr_step");
      var trq_id = this.getAttribute("trq_id");
      var step_done = this.getAttribute("title");
      var trans_code = this.getAttribute("trans_code");
      var trd_id = this.getAttribute("trd_id");
      $.ajax({
        type: "POST",
        url: baseurl+'email/update_otr_status',
        dataType: "json",
        data:{
          trans_code:trans_code,
          otr_step:otr_step, 
          trq_id:trq_id, 
          step_done:step_done, 
          trd_id:trd_id 
        },
        success: function(data){
          toastr.info(data.message);
          table.ajax.reload( null, false );
          hideLoader();
        },
        error: function(xhr, status, error) {
          toastr.info("Something went wrong.");
          hideLoader();
        }
      });
    }
  })

  $("#viewresult").on("click",".transfer ",function(e){
    var assigned_to_id = this.getAttribute("assigned_to_id");
    var assigned_to = this.getAttribute("assigned_to");
    var trans_code = this.getAttribute("trans_code");
    var tr_id = this.getAttribute("tr_id");
    $("#transfer_modal_title").html(`Transfer request ${trans_code}`);
    $("#transfer_from").html(assigned_to);
    $("#transfer_trans_code").val(trans_code);
    $("#transfer_tr_id").val(tr_id);
    $("#transfer_modal").modal("show");
  })

  $("#transfer_modal").on("click","#transfer_btn",function(e){
    if(confirm("Confirm transfer?")){
      showLoader();
      var trans_code = $("#transfer_trans_code").val();
      var assign_to_id = $("#assign_to_id").val();
      var tr_id = $("#transfer_tr_id").val();
      $.ajax({
        type: "POST",
        url: baseurl+'Transcript_request/transfer',
        dataType: "json",
        data:{
          trans_code:trans_code,
          assign_to_id:assign_to_id, 
          tr_id:tr_id, 
        },
        success: function(message){
          toastr.info(message);
          $("#transfer_modal").modal("hide");
          table.ajax.reload( null, false );
          hideLoader();
        },
        error: function(xhr, status, error) {
          toastr.info("Something went wrong.");
          hideLoader();
        }
      });
    }
  })

})//end document.ready
</script>