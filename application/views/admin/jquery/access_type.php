<?php
$id_name = "at_id";
$table = "access_type";
$labels = "Access Type";
?>
<script>
$(document).ready(function() {
  <?php
    if($this->session->flashdata('message')){
  ?>
       toastr.<?= $this->session->flashdata('todo') ?>('<?= $this->session->flashdata('message') ?>')
  <?php
    }
  ?>
    $('#viewresult').on('click','.links', function(){
      var at_id = this.getAttribute("at_id");
      var access_type = this.getAttribute("access_type");
      $('#exemption_title').html('Add links for '+access_type);
      $('#at_id').val(at_id);
      view_exempted(at_id);
      $('#exemption_modal').modal('show');
    })

  $("#submit_add").click(function (e) {
    $('input[type=text]').val (function () {
      return this.value.toUpperCase();
    })
    $('input[type=text], textarea').val (function () {
      return this.value.toUpperCase();
    })   
    $("#form1").submit();
  });

  $("#submit_edit").click(function (e) {
    $('input[type=text]').val (function () {
      return this.value.toUpperCase();
    })
    $('input[type=text], textarea').val (function () {
      return this.value.toUpperCase();
    })   
    $("#edit_form").submit();
  });

    $("#add").click(function() {
      $('#form1')[0].reset();
      $("#id").val('');
      //set selected value for select2
      $('#status').val('0');
      //trigger change for select2
      $('#status').select2().trigger('status');
      document.getElementById('form1').action =  '<?php echo base_url(); ?>admin/add';
      $("#modal_title").text('Add <?= $labels ?>');
      $("#submit_add").text('Save');
      $('#modal').modal('show');
});

  $(".edit_modal").click(function() {
      var id = this.getAttribute("id");
      var dbval = this.getAttribute("dbval");
      var status = this.getAttribute("status");
      $("#modal_title").text('Edit <?= $labels ?>');
      $("#submit_add").text('Edit');
      document.getElementById('form1').action =  '<?php echo base_url(); ?>admin/add';
      //set selected value for select2
      $('#status').val(status);
      //trigger change for select2
      $('#status').select2().trigger('status');
      $("#id").val(id);
      $("#dbval").val(dbval);
      $('#modal').modal('show');
  });

  $(".delete_modal").click(function() {
      var id = this.getAttribute("id");
      var dbval = this.getAttribute("dbval");
      var status = this.getAttribute("status");
      $("#modal_title").text('Delete <?= $labels ?>');
      $("#submit_add").text('Delete');
      document.getElementById('form1').action =  '<?php echo base_url(); ?>admin/delete';
      //set selected value for select2      //set selected value for select2
      $('#status').val(status);
      //trigger change for select2
      $('#status').select2().trigger('status');
      $("#id").val(id);
      $("#dbval").val(dbval);
      $('#modal').modal('show');
  });

    $('#viewresult').DataTable({
            responsive: true,
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    "iDisplayLength": 10
    });

  $('#links_table').on('click','#delete_link', function(){
    var a_id = this.getAttribute("a_id");
    var at_id = $('#at_id').val();
      $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>Admin/delete_link",
        dataType: "JSON",
        data:{a_id:a_id},
        success: function(data){
          view_exempted(at_id);
           toastr.info('Link has been deleted!');
          $('#exemp_client').val('').change();
        }
      });
  })

function view_exempted(at_id){
  $.ajax({
  type: "POST",
  url: "<?php echo base_url(); ?>Admin/get_access",
  dataType: "JSON",
  data:{at_id:at_id },
  success: function(data){ 
  table.clear().draw();
  $.each(data, function(key, val){
      var rowNode = table.row.add([`${val.link}`, 
                    `<a id="delete_link" href="#" title="Remove Link"
                      a_id="${val.a_id}">
                      <i class="fa fa-trash"></i> 
                    </a>`
                    ]).draw().node();
        })
      }
    });
}

var table =  $('#links_table').DataTable({
    responsive: true,
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    "iDisplayLength": 10,
     "ordering": false,
     'sorting': false,
      "aaSorting": []
    });

 $("#links").select2({
  ajax: { 
   url: "<?php echo base_url(); ?>Admin/get_links",
   type: "post",
   dataType: 'json',
   delay: 250,
   data: function (params) {
    return {
      search: params.term, // search term
      at_id: $('#at_id').val()
    };
   },
processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.link,
                        id: item.link_id
                    }
                })
            };
        }
    }
 });


$('#add_link').click(function(e){
  e.preventDefault();
  var link_id = $('#links').val();
  var at_id = $('#at_id').val();
  if(link_id!=null){
    $.ajax({
      type: "POST",
      url: "<?php echo base_url(); ?>Admin/add_access",
      dataType: "JSON",
      data:{link_id:link_id,at_id:at_id },
      success: function(data){
        view_exempted(data);
       toastr.success('Link has been added!');
        $('#links').val('').change();
      }
    });
  }else{
    alert('Please select link');
  }
});

});

</script>