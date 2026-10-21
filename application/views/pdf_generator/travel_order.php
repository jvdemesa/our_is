<style>
.table {
  border: 0.5px solid #000000;
}
.table-bordered > thead > tr > th,
.table-bordered > tbody > tr > th,
.table-bordered > tfoot > tr > th,
.table-bordered > thead > tr > td,
.table-bordered > tbody > tr > td,
.table-bordered > tfoot > tr > td {
   border: 0.5px solid #000000;
   border-collapse: collapse;
}
table#details {
    border-left: 0.01em solid #000000;
    border-right: 0.01em solid #000000;
    border-top: 0.01em solid #000000;
    border-bottom: 0.01em solid #000000;
    border-collapse: collapse;
}
table#details td,
table#details th {
    border-left: 0.01em solid #000000;
    border-right: 0.01em solid #000000;
    border-top: 0.01em solid #000000;;
    border-bottom: 0.01em solid #000000;
}
#initial{
	position: absolute;
	bottom: 250px;
	left: 350px;
}
.user_sig_span{
	position: absolute;
	top: 180px;
	left: -20px;
}
.oe{
	position: absolute;
	bottom: 15px;
	left: 500px;
	border-top: solid 1px black;
}
.oe_sig_span{
	position: absolute;
	bottom: 40px;
	left: 430px;
}
.sig_span {
  height: 40px;
  width: 280px;
  background-color: none;
  display: inline-block;
  text-align:center;
  padding-bottom: 5px;
  vertical-align: top;
}
.ra_sig_span {
  height: 40px;
  width: 280px;
  background-color: none;
  display: inline-block;
  text-align:center;
  padding-bottom: 5px;
  vertical-align: top;
}
.user_sig_span {
  height: 40px;
  width: 200px;
  background-color: none;
  display: inline-block;
  text-align:center;
  padding-bottom: 5px;
  vertical-align: top;
  left: 55px;
}
.oe_sig_span {
  height: 40px;
  width: 280px;
  background-color: none;
  display: inline-block;
  text-align:center;
  padding-bottom: 5px;
  vertical-align: top;
}

.sig_span img {
	margin-left: auto;
  margin-right: auto;
  height: 60px; /* Or whatever */
  width: auto;
  display: block;
}

.ra_sig_span img {
	margin-left: auto;
  margin-right: auto;
  height: 60px; /* Or whatever */
  width: auto;
  display: block;
}

.user_sig_span img {
	margin-left: auto;
  margin-right: auto;
  height: 60px; /* Or whatever */
  width: auto;
  display: block;
}
.oe_sig_span img {
	margin-left: auto;
  margin-right: auto;
  height: 60px; /* Or whatever */
  width: auto;
  display: block;
}

.img_sig{
	width: 100%;
}
.hideme{
	visibility: hidden !important;
}

</style>
<!-- TO Owner signature -->
<div class="user_sig_span"><img src="data:image/<?= $doc_details->user_img_type ?>;base64,<?= base64_encode($doc_details->user_sig) ?>" id="user_sig" width="120" height="90"></div>
<div class="oe_sig_span"><img src="data:image/<?= $doc_details->user_img_type ?>;base64,<?= base64_encode($doc_details->user_sig) ?>" id="user_sig_oe" width="120" height="90"></div>
<!-- signature of initial -->
<?php 
if($i_sig){//check if with initial
	if($i_sig->is_signed == '1'){ //check if initial is already signed / approved
	echo '<img src="data:image/'.$i_sig->i_img_type.';base64,'.base64_encode($i_sig->initial).'" id="initial" width="65" height="45">';
	}//end if $i_sig
}//end if $i_sig->is_signed == '1'
//signature of approval
if($approval_sig){//if with approval
	if($approval_sig->is_signed == '1'){//if already approved by the approver
	echo '<div class="sig_span"><img src="data:image/'.$approval_sig->img_type.';base64,'.base64_encode($approval_sig->signature).'" id="a_sig"></div>';
	}//end if $approval_sig->is_signed == '1'
}//end if $approval_sig
//check if reccomending approval eeds to be displayed - display only if ra and sig is diffrent users
if($ra_sig==false){
	$with_ra = false;
}else{
	if($ra_sig->sig_uid == $approval_sig->sig_uid){
		$with_ra = false;
	}else{
		$with_ra = true;
		//signature of recommending approval
		if($ra_sig){// if with recommending approval
			if($ra_sig->is_signed == '1'){//if already approved by recommending approval
			echo '<div class="ra_sig_span"><img src="data:image/'.$ra_sig->img_type.';base64,'.base64_encode($ra_sig->signature).'" id="ra_sig"></div>';
			}//end if $ra_sig->is_signed == '1'
		}// end if $ra_sig
	}
}
?>

<table class="" width="100%">
	<tr>
		<td colspan="4">
			<center>
			Republic of the Philippines<br>
			DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES<br>
			ECOSYSTEMS RESEARCH AND DEVELOPMENT BUREAU<br>
			College, Laguna<br>
			</center>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<p style="text-align:right;"><img src="<?= $img ?>"></p>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<center>
			TRAVEL ORDER<br>
			NO. : <u><?= ($to_number)?$to_number[0]->approved_number:""; ?></u>
			</center>
		</td>
	</tr>
	<tr>
		<td width="10%">Name:</td>
		<td width="40%" style="border-bottom: 0.01em solid #000000; font-size: 10pt;"><?= $doc_details->name; ?></td>
		<td width="18%">Official Station:</td>
		<td width="32%" style="border-bottom: 0.01em solid #000000;"><?= $doc_details->official_station; ?></td>
	</tr>
	<tr>
		<td width="10%">Position:</td>
		<td width="40%" style="border-bottom: 0.01em solid #000000; height: 35px !impotant"><?= $doc_details->position; ?></td>
		<td width="10%">Div/Sec/Unit:</td>
		<td width="40%" style="border-bottom: 0.01em solid #000000;"><?= $doc_details->div; ?></td>
	</tr>
</table>
<br>
<table style="border:1px;" class="table table-bordered" width="100%" id="details">
	<tr>
		<td width="15%">Duration</td>
		<td width="25%">Destination</td>
		<td width="60%">Purpose of Travel</td>
	</tr>
	<tr>
		<td width="15%">
			<div style="height: 180px; overflow:hidden; border-collapse: collapse; font-size:10.5pt">
				<?= $doc_details->duration; ?>
			</div>
		</td>
		<td width="25%">
			<div style="height: 180px; overflow:hidden; border-collapse: collapse; font-size:10.5pt">
				<?= $doc_details->destination; ?>
			</div>
		</td>
		<td width="60%">
			<div style="height: 180px; overflow:hidden; border-collapse: collapse; font-size:10.5pt">
				<?= $doc_details->purpose; ?>
			</div>
		</td>
	</tr>
</table>
<table>
	<tr>
		<td width="30%">Per Diems/Expenses Allowed:</td>
		<td width="70%" style="border-bottom: 0.01em solid #000000;">
			<div style="width: 490px; overflow:hidden;">
				<?= $doc_details->per_diems; ?>
			</div>
		</td>
	</tr>
	<tr>
		<td width="30%">Assitants or Laborer Allowed:</td>
		<td width="70%" style="border-bottom: 0.01em solid #000000;">
			<div style="width: 490px; overflow:hidden;">
				<?= $doc_details->assistants; ?>
			</div>
		</td>
	</tr>
	<tr>
		<td width="30%">Remarks or Special Instruction:</td>
		<td width="70%" style="border-bottom: 0.01em solid #000000;">
			<div style="width: 490px; overflow:hidden;">
				<?= $doc_details->remarks; ?>
			</div>
		</td>
	</tr>
	<tr>
		<td width="30%">Responsibility Center:</td>
		<td width="70%" style="border-bottom: 0.01em solid #000000;">
			<div style="width: 490px; height: 36px; overflow:hidden; font-size:10.5pt">
				<?= $doc_details->rc; ?>
			</div>
		</td>
	</tr>
</table>
<br>
<b>Certification</b><br>
This is to certify that the travel is necessary and is connected with the function of the official/ employee of this Div/Sec/unit.
<br>
<br>
<?php 
//if no reccomending approval

if($with_ra==false){
?>
	<style>
	#initial{
		position: absolute;
		bottom: 170px;
		left: 520px;
		transform: rotate(-40deg);
	}
	.sig_span{
		position: absolute;
		bottom: 205px;
		left: 300px;
	}
	</style>
	<table width="100%">
		<tr>
			<td width="50%">
				<div style="height: 75px; width: 50%; overflow:hidden; border-collapse: collapse;">
					
				</div>
			</td>
			<td>
				<div style="height: 75px; overflow:hidden; border-collapse: collapse;">
					<b>Approved</b>
				</div>
			</td>
		</tr>
		<tr style="font-size: 10pt;">
			<td>
				
			</td>
			<td>
				<b style="font-size: 10pt;"><?= $approval_sig->fullname ?></b><br>
					<?php 
					if($approval_sig->signatory_status=="PERMANENT"){
						if($approval_sig->sig_designation!=null){
						echo strtoupper($approval_sig->sig_designation)." ";
						}
					}else{
						if($approval_sig->sig_designation!=null){
							echo strtoupper($approval_sig->signatory_status." - ".$approval_sig->designation. " ");
						}
					}
					if($approval_sig->sig_designation=="DIVISION CHIEF" || $approval_sig->sig_designation=="CENTER HEAD" ){
						echo strtoupper($approval_sig->divunit_abv);
					}
					if($approval_sig->orig_uid != 0 && $approval_sig->is_signed == 1){
						if($approval_sig->sig_designation!=null){
						echo "<br>";
						}
						echo "In-Charge - ".$approval_sig->div_name;
					}
				?>
			</td>
		</tr>
	</table>
	<?php
	//if with reccomending approval
}else{
	?>
	<style>
	#initial{
		position: absolute;
		bottom: 170px;
		left: 130px;
		transform: rotate(-40deg);
	}
	.ra_sig_span{
		position: absolute;
		bottom: 200px;
		left: -60px;
	}
	.sig_span{
		position: absolute;
		bottom: 205px;
		left: 300px;
	}
	</style>
	<table width="100%">
		<tr>
			<td>
				<div style="height: 75px; width: 50%; overflow:hidden; border-collapse: collapse;">
					<b>Recommending Approval:</b>
				</div>
			</td>
			<td>
				<div style="height: 75px; width: 50%; overflow:hidden; border-collapse: collapse;">
					<b>Approved</b>
				</div>
			</td>
		</tr>
		<tr style="font-size: 10pt;">
			<td>
				<b style="font-size: 10pt;width: 50%;"><?= $ra_sig->fullname ?></b><br>
				<?php 
					//add designation status if not permanent
					if($ra_sig->signatory_status=="PERMANENT"){
						echo strtoupper($ra_sig->sig_designation)." ";
					}else{
						echo strtoupper($ra_sig->signatory_status." - ".$ra_sig->designation);
					}
					if($ra_sig->orig_uid != 0 && $ra_sig->is_signed == 1){
						if($approval_sig->sig_designation!=null){
						echo "<br>";
						}
						echo "In-Charge - ".$ra_sig->unit_name;
					}
				?>
			</td>
			<td>
				<b style="width: 50%;"><?= $approval_sig->fullname ?></b><br>
				<?php 
					//add designation status if not permanent
					if($approval_sig->signatory_status=="PERMANENT"){
						if($approval_sig->sig_designation!=null){
							echo strtoupper($approval_sig->sig_designation)." ";
						}
					}else{
						if($approval_sig->sig_designation!=null){
							echo strtoupper($approval_sig->signatory_status." - ".$approval_sig->sig_designation)." ";
						}
					}
					//if division chief add division abv
					if($approval_sig->designation=="DIVISION CHIEF" || $approval_sig->designation=="CENTER HEAD" ){
						echo strtoupper($approval_sig->divunit_abv);
					}
					if($approval_sig->orig_uid != 0 && $approval_sig->is_signed == 1){
						if($approval_sig->sig_designation!=null){
						echo "<br>";
						}
						echo "In-Charge - ".$approval_sig->div_name;
					}

				?>
			</td>
		</tr>
	</table>
<?php
}
?>
<hr style="margin-top: 5px;margin-bottom: 0px;">
<center>AUTHORIZATION</center>
<span style="font-size:9.6pt">If I will file a cash advance for this travel then I authorize the ERDB Chief Accountant or his/her authorized representative to deduct from my succeeding salaries and benefits the amount which remains unliquidated beyond twenty days after return to my official station.</span>
<div class="oe">Official Employee</div>