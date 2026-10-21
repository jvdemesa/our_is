<script>
    function document_counter(){
        var report_level = $("#report_level").val();
        var doc_type_id = $("#doc_type_id").val();
        var date_created = $("#date_created").val();
        var todo = $("#todo").val();
    $("#loader").show();
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>"+'Reports/document_counter_json',
            dataType: "JSON",
            data:{report_level:report_level, doc_type_id:doc_type_id, date_created:date_created, todo:todo},
            success: function(data){
                if(data.created){
                    $("#created_docs").html(data.created);
                    $("#created_docs").effect("highlight", {color: '#ADD8E6'}, 3000);
                }
                if(data.received){
                    $("#received_docs").html(data.received);
                    $("#received_docs").effect("highlight", {color: '#ADD8E6'}, 3000);
                }
                if(data.received){
                    $("#forwarded_docs").html(data.forwarded);
                    $("#forwarded_docs").effect("highlight", {color: '#ADD8E6'}, 3000);
                }
                if(data.acted){
                    $("#acted_docs").html(data.acted);
                    $("#acted_docs").effect("highlight", {color: '#ADD8E6'}, 3000);
                }
            }
      }).done(function() {
            $("#loader").hide();
        });
    }
    document_counter();
function secondsToDhms(seconds) {
seconds = Number(seconds);
var d = Math.floor(seconds / (3600*24));
var h = Math.floor(seconds % (3600*24) / 3600);
var m = Math.floor(seconds % 3600 / 60);
var s = Math.floor(seconds % 60);

var dDisplay = d > 0 ? d + (d == 1 ? " day, " : " days, ") : "";
var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : "";
var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes, ") : "";
var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
return dDisplay + hDisplay + mDisplay;
}
$(document).ready(function () {
    var table = $('#ratings_table').DataTable({ 
      "order": false, //Initial no order.
      responsive: true,
      "aLengthMenu": [[5, 10, 20, 50], [5, 10, 20, 50]],
      "iDisplayLength": 10,
      dom: 'Blfrtip',
    });

    $("#filter").click(function(e){
    e.preventDefault();
      table.clear().draw();
        $("#loader").show();
        var date_created = $("#date_created").val();
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>"+'Reports/ratings_ajax',
            dataType: "JSON",
            data:{date_created:date_created},
            success: function(data){
                if(data){
                    var rate1_total = 0,rate2_total = 0,rate3_total = 0,rate4_total = 0,rate5_total = 0,rate6_total = 0,rate7_total = 0,average_total=0,  total = 0, days_total= 0, hours_total=0, total_time=0;
                  $.each(data.result, function(key, val) {
                    total++;
                    rate1_total += parseFloat(val.rate1);
                    rate2_total += parseFloat(val.rate2);
                    rate3_total += parseFloat(val.rate3);
                    rate4_total += parseFloat(val.rate4);
                    rate5_total += parseFloat(val.rate5);
                    rate6_total += parseFloat(val.rate6);
                    rate7_total += parseFloat(val.rate7);
                    average_total += parseFloat(val.average);
                    total_time += parseFloat(val.total_seconds);
                    table.row.add([ val.dtsno, 
                                    val.date_created,
                                    val.division+" - "+val.unit, 
                                    val.rate1, 
                                    val.rate2, 
                                    val.rate3, 
                                    val.rate4, 
                                    val.rate5, 
                                    val.rate6, 
                                    `<span class="red">${val.average}</span>`,
                                    `<span class="red">${val.date_diff} </span>`
                                  ]).draw( false );
                  });
                  var rate1_avg = rate1_total / total;
                  var rate2_avg = rate2_total / total;
                  var rate3_avg = rate3_total / total;
                  var rate4_avg = rate4_total / total;
                  var rate5_avg = rate5_total / total;
                  var rate6_avg = rate6_total / total;
                  var average_avg = average_total / total;
                  var total_time = total_time / total;
                  console.log(total_time);
                  var average_day = secondsToDhms(total_time);
                  var tfoot = `<tr>
                                    <td colspan="3">AVERAGE:</td>
                                    <td><span class="red">${rate1_avg.toFixed(2)}</span></td>
                                    <td><span class="red">${rate2_avg.toFixed(2)}</span></td>
                                    <td><span class="red">${rate3_avg.toFixed(2)}</span></td>
                                    <td><span class="red">${rate4_avg.toFixed(2)}</span></td>
                                    <td><span class="red">${rate5_avg.toFixed(2)}</span></td>
                                    <td><span class="red">${rate6_avg.toFixed(2)}</span></td>
                                    <td><u><b><span class="red">${average_avg.toFixed(2)}</span><b></u></td>
                                    <td><u><b><span class="red">${average_day}</span><b></u></td>
                                </tr>`;
                    $("#tfoot").html(tfoot);
                }
            }
        }).done(function() {
                $("#loader").hide();
            });
    })

    $("#received_list").click(function(e){
    e.preventDefault();
    table.clear().draw();
    $("#loader").show();

        var report_level = $("#report_level").val();
        var doc_type_id = $("#doc_type_id").val();
        var date_created = $("#date_created").val();
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>"+'Reports/document_counter_list',
            dataType: "JSON",
            data:{report_level:report_level, doc_type_id:doc_type_id, date_created:date_created, todo:2},
            success: function(data){
                if(data.received){
                  $('#update_row_date').html("Date Received");
                  $('#update_row_name').html("Received By");
                  $('#document_list-title').html("Documents Received");
                  $('#details_box').removeAttr('class');
                  $("#details_box").attr('class', 'box box-danger');
                  let tbody = "", current_loc;
                  $.each(data.received, function(key, val) {
                    var type = val.sub_type != null?val.doc_t+" ( "+val.sub_type+" )":val.doc_t;
                    table.row.add([ val.dtsno, 
                                    val.doc_type, 
                                    // val.doc_details, 
                                    val.fullname, 
                                    val.received_time
                                  ]).draw( false );
                  });
                }
            }
        }).done(function() {
                $("#loader").hide();
            });
    })

    $("#forwarded_list").click(function(e){
    e.preventDefault();
    table.clear().draw();
    $("#loader").show();
        var report_level = $("#report_level").val();
        var doc_type_id = $("#doc_type_id").val();
        var date_created = $("#date_created").val();
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>"+'Reports/document_counter_list',
            dataType: "JSON",
            data:{report_level:report_level, doc_type_id:doc_type_id, date_created:date_created, todo:3},
            success: function(data){
                if(data.forwarded){
                  $('#update_row_date').html("Date Forwarded");
                  $('#update_row_name').html("Forwarded By");
                  $('#document_list-title').html("Forwarded Documents");
                  $('#details_box').removeAttr('class');
                  $("#details_box").attr('class', 'box box-danger');
                  let tbody = "", current_loc;
                  $.each(data.forwarded, function(key, val) {
                    var type = val.sub_type != null?val.doc_t+" ( "+val.sub_type+" )":val.doc_t;
                    table.row.add([ val.dtsno, 
                                    val.doc_type, 
                                    // val.doc_details, 
                                    val.fullname, 
                                    val.date_forwarded
                                  ]).draw( false );
                  });
                }
            }
        }).done(function() {
                $("#loader").hide();
            });
    })

    $("#acted_list").click(function(e){
        e.preventDefault();
            var report_level = $("#report_level").val();
            var doc_type_id = $("#doc_type_id").val();
            var date_created = $("#date_created").val();
        table.clear().draw();
            $("#loader").show();
        $.ajax({
            type: "POST",
            url: "<?php echo base_url(); ?>"+'Reports/document_counter_list',
            dataType: "JSON",
            data:{report_level:report_level, doc_type_id:doc_type_id, date_created:date_created, todo:4},
            success: function(data){
                table.clear().draw();
                if(data.acted){
                  $('#update_row_date').html("Date Acted");
                  $('#update_row_name').html("Acted By");
                  $('#document_list-title').html("Acted Documents");
                  $('#details_box').removeAttr('class');
                  $("#details_box").attr('class', 'box box-danger');
                  let tbody = "", current_loc;
                  $.each(data.acted, function(key, val) {
                    var type = val.sub_type != null?val.doc_t+" ( "+val.sub_type+" )":val.doc_t;
                    table.row.add([ val.dtsno, 
                                    val.doc_type, 
                                    // val.doc_details, 
                                    val.fullname, 
                                    val.action_time
                                  ]).draw( false );
                  });
                }
            }
        }).done(function() {
                $("#loader").hide();
            });
    })

    table.on('click', 'tbody tr', function() {
    window.location.href = `<?php echo base_url(); ?>Document/${table.row(this).data()[0]}`;
  });
})
</script>