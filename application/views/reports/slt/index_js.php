<script>
$(document).ready(function() {  

    $("#doc_type_id").change(function() {
        var doc_type_id = $(this).val();
        options = "";
        options +=`<option value='' selected disabled>Select DOC SUB TYPE</option>`;
        options +=`<option value='0' >All</option>`;
        if(doc_type_id != 0 && doc_type_id != '') {
            $.ajax({
            type: "POST",
            dataType: "JSON",
            data: {doc_type_id:doc_type_id},
            url: base_url+'General/get_sub_type',
                success: function(data) {
                    if(data){
                        $.each(data, function(i, val) {
                        options +="<option value='"+val.id+"' title='"+val.doc_t+"'>"+val.doc_t+"</option>";
                        });
                        $("#sub_type").html(options);
                    }
                }
            });
        } else {
            $("#sub_type").html(options);
        }
    });

    $("#filter").click( 
        function(e) 
        {
            dataTable.ajax.reload();
        }
    );

    var dataTable = $('#document_list_table').DataTable({
        "processing": true,
        "serverSide": true,
        "mark": true,
        dom: 'Blfrtip',
        "ordering": true,
        "responsive": true,
        "searching": false,
        "aLengthMenu": [[5, 10, 20,30,50,100, -1], [5, 10, 20,30,50,100, "All"]],
        "iDisplayLength": 10,
        "columns":  [    
            {
                "data": "name_of_office","searchable":true, "width": "100px" 
            },         
            {
                "data": "center","searchable":true, "width": "100px" 
            },         
            {
                "data": "client_name","searchable":true, "width": "100px" 
            },         
            {
                "data": "application_no","searchable":true, "width": "100px" 
            },    
            {
                "data": "dtsno","searchable":true, "width": "100px" 
            },    
            {
                "defaultContent": "Complex","searchable":true, "width": "100px" 
            },    
            {
                "data": "application_date_approved","searchable":true, "width": "100px" 
            },    
            {
                "defaultContent": "","searchable":true, "width": "100px" 
            },  
            {
                "data": "ada_no","searchable":true, "width": "100px" 
            },  
            {
                "data": "date_added","searchable":true, "width": "100px" 
            },  
            {
                "data": "action_time","searchable":true, "width": "100px" 
            },  
            {
                "data": "number_of_days","searchable":true, "width": "100px" 
            },  
            {
                "data": "number_of_hm","searchable":true, "width": "100px" 
            },  
            {
                "data": "remarks","searchable":true, "width": "100px" 
            }
           
        ],   
        "ajax": {
            url : base_url + "Reports/stream_lining_for_tev_list", 
            type: "post", 
            data:function ( d ) {
                d.dtsno = $("#select_dtsno").val();
                d.doc_type_id = '';
                d.sub_type ='';
            },
            error: function() { 
                // $("#document_list_table").append('<tbody class="grid-error"><tr><th colspan="'+$("#viewresult thead tr th" ).length+'">No data found in the server</th></tr></tbody>');
            }
        },
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [3,2 ] }, 
        ],
    });
  
})



function tracker_time(data, type, row) {

    var td = '<table class="table table-striped table-hover"><tbody>';
     td += '<tr><th>Division unit</th><th>Date forwarded</th><th>Date received</th><th>Date actioned</th><th>Received process time</th><th>Action process time</th></tr>';
    var result = row.time_analysis;
    $.each(result, function( index, dt ) {
        td += '<tr><td>' + dt.to_unit + '</td>';
        td += '<td>' + format_date(dt.date_forwarded )+ '</td>';
        td += '<td>' + format_date(dt.received_time )+ '</td>';
        td += '<td>' + format_date(dt.action_time) + '</td>';
        td += '<td>' + dt.recieve_calculated_time + '</td>';
        td += '<td>' + dt.processed_calculated_time + '</td>';
        td += '<tr/>';
    });

    td += '</tbody></table>';

    return td; 
}

function types(data, type, row) {
    return row.doc_t + (row.sub_type_name != null ? ' - ' +row.sub_type_name : ''); 
}


function processed_time(data, type, row) {


    var td = '<dl class="dl-horizontal">';


        var result = row.time_analysis;
        $.each(result, function( index, dt ) {
            td += '<dt>Division unit:</dt><dd>' + dt.to_unit + '</dd>';
            td += '<dt>Forwarded to Received:</dt><dd>' + dt.recieve_calculated_time + '</dd>';
            td += '<dt>Received to Actioned:</dt><dd>' + dt.processed_calculated_time + '</dd>';

            td += '<hr/>';
        });

        return td; 


   
}
function format_date(datetime)  {

    if(datetime != '0000-00-00 00:00:00' && datetime != null) {

    
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"
            ];
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