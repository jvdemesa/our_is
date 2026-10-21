<script>
localStorage.setItem('AdminLTE:IFrame:Options',JSON.stringify({autoIframeMode:false,autoItemActive:false}))
var iframe = document.createElement("iframe");
iframe.style.display = "none";//change to block for troubleshooting purpose
// Append the iframe to the document
document.body.appendChild(iframe);

function print_receipt(data){
  // Create a form element
  var form = document.createElement("form");
  form.action = baseurl+'Clients/print_ticket';
  form.method = "post";

  // Convert the data object into a JSON string
  var jsonData = data;

  // Create a hidden input field to hold the JSON data
  var input = document.createElement("input");
  input.type = "hidden";
  input.name = "jsonData";
  input.value = jsonData;

  // Append the input field to the form
  form.appendChild(input);

  // Append the form to the iframe
  iframe.contentDocument.body.appendChild(form);
  form.submit();
}

  $(document).ready(function() {  
  $(".get_service").click(function(){
    let service_id = this.getAttribute('service_id');
    let service = this.getAttribute('service');
    let default_number = this.getAttribute('default_number');
    $('#priority').prop('checked', false);
    $("#service_id").val(service_id);
    $("#default_number").val(default_number);
    $("#modal_title").html(`Confirm service<span class="uplb-yellow"><strong> ${service}</strong></span>`);
    $('#modal').modal('show');
  })

  $("#confirm").click(function(e){
    e.preventDefault();
    var form_data = new FormData($("form")[0]);
    $.ajax({
      type: "POST",
      url: baseurl+'Clients/add_queue',
      dataType: "JSON",
      data:form_data,
      contentType: false, 
      processData: false, 
      success: function(data){
        //window.open('print_ticket?queue_id='+data.queue_id, '_blank',"height=200,width=200");
        print_receipt(JSON.stringify(data));//convert data to json
        toastr.success(`Your queue number is <strong>${data.ticket_number}</strong>. Please get your ticket number.`);
        $('#modal').modal('hide');
      }
    })
  })
})
</script>