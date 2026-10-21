<script>
$(document).ready(function() {
  <?php
    if($this->session->flashdata('message')){
  ?>
       toastr.<?= $this->session->flashdata('todo') ?>('<?= $this->session->flashdata('message') ?>')
  <?php
    }
  ?>


  $("#submit_add").click(function (e) {
    $("#form1").submit();
  });

  $("#submit_edit").click(function (e) {
    $("#edit_form").submit();
  });

    $("#add").click(function() {
      $('#form1')[0].reset();
      $("#id").val('');
      //set selected value for select2
      $('#status').val('0');
      //trigger change for select2
      $('#status').select2().trigger('status');
      document.getElementById('form1').action =  '<?php echo base_url(); ?>admin/add_all';
      $("#modal_title").text('Add');
      $("#submit_add").text('Add');
      $('#modal').modal('show');
});

  $(".edit_modal").click(function() {
      var id = this.getAttribute("id");
      var dbval = this.getAttribute("dbval");
      var status = this.getAttribute("status");
      var code = this.getAttribute("code");
      $("#modal_title").text('Edit');
      $("#submit_add").text('Edit');
      document.getElementById('form1').action =  '<?php echo base_url(); ?>admin/add_all';
      //set selected value for select2
      $('#code').val(code);
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
      var code = this.getAttribute("code");
      $("#modal_title").text('Delete');
      $("#submit_add").text('Delete');
      document.getElementById('form1').action =  '<?php echo base_url(); ?>admin/delete';
      //set selected value for select2      //set selected value for select2
      $('#status').val(status);
      //trigger change for select2
      $('#status').select2().trigger('status');
      $("#id").val(id);
      $("#dbval").val(dbval);
      $("#code").val(code);
      $('#modal').modal('show');
  });
  
  $('#viewresult').DataTable({
          responsive: true,
  "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
  "iDisplayLength": 10
  });

});

</script>