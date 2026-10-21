<script>
    var dataTable;
$(document).ready(function() {  

    dataTable = $('#document_list_table').DataTable({
        "processing": true,
        "serverSide": true,
        "mark": true,
        dom: 'Blfrtip',
        "ordering": true,
        "responsive": true,
        "searching": true,
        "aLengthMenu": [[5, 10, 20,30,50,100, -1], [5, 10, 20,30,50,100, "All"]],
        "iDisplayLength": 10,
        "columns":  [
            {
                "data": "fullname","searchable":true, "width": "100px" 
            },   
            {
                "data": "divunit","searchable":true, "width": "100px" 
            },         
            {
                "data": "travel_date","searchable":true, "width": "100px" 
            },         
            {
                "data": "dtsno","searchable":true, "width": "100px" 
            },         
            {
                "data": "approved_number","searchable":true, "width": "100px" 
            },         
            {
                "data": "doc_details","searchable":true, "width": "100px" 
            }   
        ],   
        "ajax":{
            url : base_url + "Reports/travel_order_list", 
            type: "post", 
            data:function ( d ) {
                d.div_id = $("#div_id").val();
                d.uid = $("#uid").val();
                d.status = $("#status").val();
                d.daterange = $("#travel_date").val();
              //  d.year = $("#select_year").val();
              //  d.project_code = $("#select_project").val();
              //  d.division_code = $("#select_division").val();
              //  d.ppmp_type = $("#select_type").val();
            },
            error: function() { 
                // $("#document_list_table").append('<tbody class="grid-error"><tr><th colspan="'+$("#viewresult thead tr th" ).length+'">No data found in the server</th></tr></tbody>');
            }
        },
        "aoColumnDefs": [
           // { "bSortable": false, "aTargets": [3,2 ] }, 
        ],
        createdRow: function( row, data, dataIndex ) {
            // Set the data-status attribute, and add a class
            if(data.is_approved=='1'){
              $(row).addClass("bg-info");
            }
            if(data.status=='1'){
              $(row).addClass("bg-danger");
            }
        }
    });
  
dataTable.on('click', 'tbody tr', function() {
window.location.href = `<?php echo base_url(); ?>Document/${dataTable.row(this).data().dtsno}`;
});

});

function filter() {
    dataTable.ajax.reload();
}

function tracker_time(data, type, row) {
    return '';
    // var td = '<table class="table table-striped table-hover"><tbody>';
    //  td += '<tr><th>Division unit</th><th>Date forwarded</th><th>Date received</th><th>Date actioned</th><th>Received process time</th><th>Action process time</th></tr>';
    // var result = row.time_analysis;
    // $.each(result, function( index, dt ) {
    //     td += '<tr><td>' + dt.to_unit + '</td>';
    //     td += '<td>' + format_date(dt.date_forwarded )+ '</td>';
    //     td += '<td>' + format_date(dt.received_time )+ '</td>';
    //     td += '<td>' + format_date(dt.action_time) + '</td>';
    //     td += '<td>' + dt.recieve_calculated_time + '</td>';
    //     td += '<td>' + dt.processed_calculated_time + '</td>';
    //     td += '<tr/>';
    // });

    // td += '</tbody></table>';

    // return td; 
}

function types(data, type, row) {
    return row.doc_t + (row.sub_type_name != null ? ' - ' +row.sub_type_name : ''); 
}


function processed_time(data, type, row) {


    // return '<center><label>Processing</label> <i class="fa fa-refresh fa-spin"></i></center>'; 

    var td = '<dl class="dl-horizontal">';
    //  $.post(base_url + "Reports/process_time_analysis_data", { dtsno : row.dtsno} , function(result) {

        var result = row.time_analysis;
        $.each(result, function( index, dt ) {
            td += '<dt>Division unit:</dt><dd>' + dt.to_unit + '</dd>';
            td += '<dt>Forwarded to Received:</dt><dd>' + dt.recieve_calculated_time + '</dd>';
            td += '<dt>Received to Actioned:</dt><dd>' + dt.processed_calculated_time + '</dd>';

            td += '<hr/>';
        });

        return td; 
    // }, 'json');

   
}
function format_date(datetime)  {

    if(datetime != '0000-00-00 00:00:00' && datetime != null) {
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
        var formattedDate = new Date(datetime);
        var d = formattedDate.getDate();
        var m =  formattedDate.getMonth();
        // m += 1;  // JavaScript months are 0-11
        var y = formattedDate.getFullYear();

        // $("#txtDate").val(d + "." + m + "." + y);

        return monthNames[m] + ' ' +d + ", "  + y + ' ' + formattedDate.getHours() + ":" + formattedDate.getMinutes() + ":" + formattedDate.getSeconds();
    } else {
        return 'N/A';
    }
}

</script>