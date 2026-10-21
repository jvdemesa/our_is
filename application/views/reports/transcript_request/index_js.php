<script>
$(document).ready(function() {  
    //remove initial value of date range
    $('.daterange').val('');

    const paidvsunpaid = document.getElementById('paidvsunpaid');
    const data_pie = {
     datasets: [{
       hoverOffset: 4
     }]
    };
    const config_pie = {
     type: 'pie',
     data: data_pie,
    responsice: true,
    maintainAspectRatio: false,
    };
    const pie_chart = new Chart(paidvsunpaid, config_pie);

    // Set up the chart
    // Initialize the chart first with empty data
    const daily_graph = document.getElementById('daily_graph');
    let line_chart = new Chart(daily_graph, {
      type: 'line',
      data: {
        labels: [], // Initially empty labels for dates
        datasets: [{
          label: "Daily Total",
          data: [], // Initially empty data for total_today
          borderColor: "#8A1538",
          backgroundColor: "#8A1538",
          fill: false,
          tension: 0.1
        }]
      },
      options: {
        responsive: true,
        scales: {
          x: {
            type: 'category',
            display: true
          },
          y: {
            beginAtZero: true,
            display: true
          }
        }
      }
    });

    // Function to update the chart with new data
    function updateChart(dailyClientCount) {
      // Convert date strings to Date objects and sort the data by date
      dailyClientCount.sort((a, b) => new Date(a.date) - new Date(b.date));

      // Extract labels (dates) and data (totals) from the sorted response
      const labels = dailyClientCount.map(entry => entry.date);
      const data = dailyClientCount.map(entry => Number(entry.total_today));

      // Update the chart's labels and data
      line_chart.data.labels = labels;
      line_chart.data.datasets[0].data = data;

      // Redraw the chart with the updated data
      line_chart.update();
    }

    $("#request_type, #request_date, #uid, #status, #degree ").change(function(e){
        get_data();
    });

var table = $('#viewresult').DataTable({
    responsive: true,
    ordering: false,
    dom: 'Blfrtip',
    buttons: [
        {
            extend: 'excelHtml5',
            text: 'Export Excel',
            title: 'Report',
            exportOptions: {
                columns: ':visible',
                modifier: {
                    page: 'all'
                }
            }
        },
        {
            extend: 'pdfHtml5',
            text: 'Export PDF',
            title: function () {
              return prompt('Enter report title:', 'Report') || 'Report';
            },
            orientation: 'landscape',
            pageSize: 'A4', // or 'LEGAL' if you have many columns
            exportOptions: {
              columns: function (idx, data, node) {
                return idx !== 1 && idx !== 2 && idx !== 3;
              },
              modifier: {
                  page: 'all'
              }
            },
            customize: function (doc) {
                // Reduce font size
                doc.defaultStyle.fontSize = 8;
                doc.styles.tableHeader.fontSize = 9;

                // Reduce page margins
                doc.pageMargins = [20, 20, 20, 20];
                var table = doc.content[1].table;
                table.widths = Array(table.body[0].length + 1).join('*').split('');

                // Change the 7th column header
                table.body[0][3].text = 'Total Quantity';
                // Make table fill the page width
                var objLayout = {};
                objLayout.hLineWidth = function() { return 0.5; };
                objLayout.vLineWidth = function() { return 0.5; };
                objLayout.hLineColor = function() { return '#aaa'; };
                objLayout.vLineColor = function() { return '#aaa'; };
                objLayout.paddingLeft = function() { return 4; };
                objLayout.paddingRight = function() { return 4; };

                doc.content[1].layout = objLayout;

                // Auto-size all columns equally
                var table = doc.content[1].table;
                table.widths = Array(table.body[0].length + 1).join('*').split('');
            }
        }
    ],
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    "iDisplayLength": 5
});

    function get_data(){
        var request_type = $("#request_type").val();
        var uid = $("#uid").val();
        var request_date = $("#request_date").val();
        var status = $("#status").val();
        var degree = $("#degree").val();
        table.clear().draw();
        $.ajax({
          type: "POST",
          dataType: "JSON",
          data: {request_type:request_type, uid:uid, request_date:request_date, status:status,degree:degree},
          url: baseurl+'Reports/transcript_request_list',
          beforeSend: function(){
               $("#loader").show();
          },
          success: function(response) {
            table.clear();
            table.rows.add(response.data);
            table.draw(false);

            updateChart(response.daily_client_count);

            // Assessment
            if(response.assessment_time){
                $("#assessment_min_waiting_time").html(response.assessment_time.assessment_min);
                $("#assessment_max_waiting_time").html(response.assessment_time.assessment_max);
                $("#assessment_avg_waiting_time").html(response.assessment_time.assessment_avg);
            }else{
                $("#assessment_min_waiting_time, #assessment_max_waiting_time, #assessment_avg_waiting_time").html("N/A");
            }

            // Payment
            if(response.pay_time){
                $("#pay_min").html(response.pay_time.min);
                $("#pay_max").html(response.pay_time.max);
                $("#pay_avg").html(response.pay_time.avg);
            }else{
                $("#pay_min, #pay_max, #pay_avg").html("N/A");
            }

            // Pie chart
            pie_chart.data.labels = ["Paid","Unpaid"];
            pie_chart.data.datasets[0].backgroundColor = ["#00573F","#8A1538"];
            pie_chart.data.datasets[0].data = [response.paid,response.unpaid];
            pie_chart.update();
              // groupByDate(response);
          },
          complete : function(){
              $("#loader").hide();
          }              
        });
    }
    // Add download functionality with a white background
    $("#download_chart").click(function () {
        // Create a temporary canvas to add a white background
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');

        // Set canvas width and height to match the original chart
        canvas.width = line_chart.width;
        canvas.height = line_chart.height;

        // Fill the canvas with white background
        context.fillStyle = "white";
        context.fillRect(0, 0, canvas.width, canvas.height);

        // Draw the chart on top of the white background
        context.drawImage(line_chart.canvas, 0, 0);

        // Create a link and download the image with the white background
        var link = document.createElement('a');
        link.href = canvas.toDataURL(); // Get the image as a Base64 string
        link.download = 'Daily_total_request.png'; // Set the download file name

        // Trigger the download
        link.click();
    });
})
</script>
