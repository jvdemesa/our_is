<script>
$(document).ready(function() {  
    //remove initial value of date range
    $('.daterange').val('');

    $("#request_id, #request_date ").change(function(e){
        get_data();
    });

    var table = $('#viewresult').DataTable({
        responsive: true,
        ordering: false,
        "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
        "iDisplayLength": 5,
	    dom: 'Blfrtip',
     	buttons: [
            {
                extend: 'excelHtml5',
                title: function() {
                    return 'OUR Transcript Income for the period of ' + $("#request_date").val();
                },
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    // Add custom code to modify the Excel file here
                },
                footer: true
            },
            {
                extend: 'pdfHtml5',
                title: function() {
                    return 'OUR Transcript Income for the period of ' + $("#request_date").val();
                },
                customize: function(doc) {
                    // Add custom code to modify the PDF file here
                },
                footer: true
            },
            {
                extend: 'print',
                title: function() {
                    return 'OUR Transcript Income for the period of ' + $("#request_date").val();
                },
                customize: function(doc) {
                    // Add custom code to modify the PDF file here
                },
                footer: true
            },
        ]
    });

    function get_data(){
        var request_id = $("#request_id").val();
        var request_date = $("#request_date").val();
        table.clear().draw();
        var loadingRow = '<tr id="loading-row"><td colspan="8"><div class="spinner"></div></td></tr>';    
        $("#viewresult tbody").html(loadingRow);
        $.ajax({
            type: "POST",
            dataType: "JSON",
            data: {request_id:request_id, request_date:request_date},
            url: baseurl+'Reports_transcript/get_income_list',
            beforeSend: function(){
               $("#loader").show();
            },
            success: function(response) {
                if(response){
                    let rows = [];
                    let total_income = 0;

                    $.each(response, function(i, val) {
                        let total_price = parseInt(val.quantity) * parseInt(val.pages) * parseInt(val.price);
                        total_income += total_price;

                        rows.push([
                            val.trans_code,
                            val.requestor,
                            val.request,
                            val.release_date,
                            val.released_to,
                            val.released_by,
                            val.quantity,
                            val.pages,
                            formatCurrency(parseInt(val.price)),
                            formatCurrency(total_price),
                        ]);
                    });

                    // ✅ one-time render
                    table.clear();
                    table.rows.add(rows);
                    table.draw(false);

                    // update total
                    $("#total_income").html(formatCurrency(total_income));
                }
            },
            complete: function() {
                // Remove loading indicator if there's an error
                $("#loader").hide();
            }
        });
    }

    $("#filter_btn").click(function(e){
        e.preventDefault();
        get_data();
    })

})
</script>
