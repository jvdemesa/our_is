<style>
table, th, td {
  border: #dfdfe2 1px solid;
  border-collapse: collapse;
}
h4{
  margin-block-start: 1em !Important;
    margin-block-end: 1em !Important;
}
</style>
<h4>Client details (<?= $details->trans_code ?>)</h4>
<table id="table" class="table table-bordered" width="100%">
  <tr>
    <td>First Name</td>
    <td>Middle Name</td>
    <td>Last Name</td>
    <td>Sex at birth</td>
  </tr>
  <tr style="border: 0px !important">
    <td><b><?= $details->fname ?></b></td>
    <td><b><?= $details->mname ?></b></td>
    <td><b><?= $details->lname ?></b></td>
    <td><b><?= $details->sex ?></b></td>
  </tr>
  <tr>
    <td>Student Number</td>
    <td>Email</td>
    <td>Date of birth</td>
    <td>Place of Birth</td>
  </tr>
  <tr>
    <td><b><?= $details->student_no ?></b></td>
    <td><b><?= $details->email ?></b></td>
    <td><b><?= $details->birthday ?></b></td>
    <td><b><?= $details->pob ?></b></td>
  </tr>
  <tr>
    <td>Sem Admitted</td>
    <td>Admitted as</td>
    <td>Last semester admitted</td>
    <td>Degree(s)</td>
  </tr>
  <tr>
    <td><b><?= $details->sem_admitted." ".$details->ay_admitted ?></b></td>
    <td><b><?= $details->admitted_as ?></b></td>
    <td><b><?= $details->sem_last_enrolled." ".$details->ay_last_enrolled ?></b></td>
    <td><b><?= $details->degree ?></b></td>
  </tr>
  <tr>
    <td>Student Status</td>
    <td>UP Campus</td>
    <td>Father's Name</td>
    <td>Mothers's Maiden Name</td>
  </tr>
  <tr>
    <td><b><?= $details->student_status ?></b></td>
    <td><b><?= $details->up_campus ?></b></td>
    <td><b><?= $details->fathers_name ?></b></td>
    <td><b><?= $details->mothers_name ?></b></td>
  </tr>
</table>
<hr>
<h4>Schoold details</h4>
<table width="100%">
  <thead>
    <tr>
      <th></th>
      <th>School Name</th>
      <th>Location</th>
      <th>Course</th>
      <th>Date of Graduation</th>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach($schools as $val){
        switch($val->type){
          case "0":
            $type = "Associate Deg";
          break;
          case "1":
            $type = "College";
          break;
          case "2":
            $type = "Master's";
          break;
          case "3":
            $type = "PhD";
          break;
          default:
            $type = "";
          break;
        }
    ?>
    <tr>
      <td><?= $type ?></td>
      <td><?= $val->name ?></td>
      <td><?= $val->location ?></td>
      <td><?= $val->course ?></td>
      <td><?= $val->year_month_graduated ?></td>
    </tr>
    <?php
      }
    ?>
  </tbody>
</table>
<hr>
<h4>Request details</h4>
<table width="100%">
  <thead>
    <tr>
      <th width="55%">Request</th>
      <th width="10%">Price</th>
      <th width="10%">Quantity</th>
    </tr>
  </thead>
  <tbody id="requests_tbody">
  <?php
    foreach($requests as $val){
  ?>
    <tr>
      <td><?= $val->request ?></td>
      <td><?= $val->price ?></td>
      <td><?= $val->quantity ?></td>
    </tr>
  <?php 
    }
  ?>

  </tbody>
</table>
