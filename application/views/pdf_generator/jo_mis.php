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
table {
    border-left: 0.01em solid #000000;
    border-right: 0.01em solid #000000;
    border-top: 0.01em solid #000000;
    border-bottom: 0.01em solid #000000;
    border-collapse: collapse;
}
table td,
table th {
    border-left: 0.01em solid #000000;
    border-right: 0.01em solid #000000;
    border-top: 0.01em solid #000000;;
    border-bottom: 0.01em solid #000000;
}
#certifier_sig{
	position: absolute;
	top: 420px;
	left: 280px;
}
#aa_sig{
	position: absolute;
	top: 710px;
	left: 165px;
}

#ad_sig{
	position: absolute;
	top: 710px;
	left: 490px;
}

.pull-right {
    float: right;
}
.small-font{
	font-size: 8pt;
}
.square {
  height: 12px;
  width: 12px;
  background-color: white;
  border: 1px solid;
  display: inline-block;
}
.rectangle {
  height: 12px;
  width: 22px;
  background-color: white;
  border: 1px solid;
  display: inline-block;
}
.space{
  height: 12px;
  width: 12px;	
  display: inline-block;
}
.ors_burs_no{
  height: 12px;
  width: 100px;	
  display: inline-block;
}
.pt11{
	font-size: 11pt;
}
.pt10{
	font-size: 10pt;
}
.pt9{
	font-size: 9pt;
}
.pt-20{
	font-size: 20pt;
}
.h130{
	height: 130px;
	text-align: center;
}
.certifier{
	height: 75px;
	text-align: center;
}
hr {
    margin-top: 5px;
    margin-bottom: 5px;
    border: 0;
    border-top: 1px solid #000000;
}
#barcode{
	position: absolute;
	top: 40px;
	right: 20px;
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

.sig_span img {
	margin-left: auto;
  margin-right: auto;
  height: 60px; /* Or whatever */
  width: auto;
  display: block;
}
.hideme{
	visibility: hidden !important;
}
.img_sig{
	width: 100%;
}
.nbb{
	border-bottom: none !important;
}
.nbt{
	border-top: none !important;
}
.nbl{
	border-left: none !important;
}
.nbr{
	border-right: none !important;
}
.no_border table, tr, td{
	border: none !important;
}
.underlined{
	border-bottom: 1px solid !important;
	min-width: 200px !important;
	display: inline-block;
}
hr{
	display: block; height: 1px;
    border: 0; border-top: 1px solid #ccc;
    margin: 1em 0; padding: 0;
}
</style>
<span id="barcode"><img src="<?= $img ?>"></span>
<table class="table nbb" width="100%">
	<tr>
		<td>
			<center>
				<small class="small-font">
					Department of Environment and Natural Resources<br>
					Ecosystems Research and Development Bureau<br>
					College, Laguna 4031<br>
				</small>
				<h3>PICTD JOB REQUEST</h3>
			</center>
		</td>
	</tr>
</table>
<table class="nbt nbb" width="100%">
	<tr class="">
		<td width="15%">Requested By:</td>
		<td width="35%" style="border-bottom: 0.01em solid #000000;"><span class="underlined"><?= $doc_details->payee ?></span></td>
		<td width="20%">Date:</td>
		<td width="30%"><span class="underlined"><?= $doc_details->date ?></span></td>
	</tr>
	<tr class="">
		<td>Position:</td>
		<td style="height: 40px !impotant"><span class="underlined"><?= $doc_details->position ?></span></td>
		<td>Div / Unit / Section</td>
		<td><span class="underlined"><?= $doc_details->div_abv ." / " .$doc_details->unit_abv  ?></span></td>
	</tr>
	<tr class="">
		<td></td>
		<td colspan="3"><br><b>Assistance needed / Problem encountered:</b></td>
	</tr>
	<tr class="">
		<td></td>
		<td colspan="3">
			<div style="height: 180px; overflow:hidden; border-collapse: collapse; font-size:10.5pt">
				<?= $doc_details->assistance_needed ?>
			</div>
		</td>
	</tr>
	<tr class="no_border">
		<td colspan="2">
			<div style="min-height: 50px; overflow:hidden; border-collapse: collapse;">
				<center>
				<?php
					if(isset($doc_details->noted_by)){//if with approval
						if($doc_details->noted_by->is_signed == '1'){//if already approved by the approver
						echo '<div class="sig_span"><img src="data:image/'.$doc_details->noted_by->img_type.';base64,'.base64_encode($doc_details->noted_by->signature).'" id="a_sig"></div>';
						}//end if $approval_sig->is_signed == '1'
					}//end if $approval_sig
				?>
				</center>
			</div>
		</td>
		<td colspan="2">
			<div style="min-height: 50px; overflow:hidden; border-collapse: collapse;">
				<center>
				<?php
					if(isset($doc_details->approved_by)){//if with approval
						if($doc_details->approved_by->is_signed == '1'){//if already approved by the approver
						echo '<div class="sig_span"><img src="data:image/'.$doc_details->approved_by->img_type.';base64,'.base64_encode($doc_details->approved_by->signature).'" id="a_sig"></div>';
						}//end if $approval_sig->is_signed == '1'
					}//end if $approval_sig
				?>
				</center>
			</div>
		</td>
	</tr>
	<tr class="no_border">
		<td colspan="2"><center><span class="underlined"><?= (isset($doc_details->noted_by))?$doc_details->noted_by->fullname:"" ?></span></center></td>
		<td colspan="2"><center><span class="underlined"><?= (isset($doc_details->approved_by))?$doc_details->approved_by->fullname:"" ?></span></center></td>
	</tr>
	<tr class="no_border">
		<td colspan="2">
			<center>
				<?php 
				if(isset($doc_details->noted_by)){
					if($doc_details->noted_by->signatory_status=="PERMANENT"){
						echo strtoupper($doc_details->noted_by->designation)." ";
					}else{
						echo strtoupper($doc_details->noted_by->signatory_status." - ".$doc_details->noted_by->designation. " ");
					}
					if($doc_details->noted_by->designation=="DIVISION CHIEF" || $doc_details->noted_by->designation=="CENTER HEAD" ){
						echo strtoupper($doc_details->noted_by->divunit_abv);
					}
				}
				?>
			</center>
		</td>
		<td colspan="2">
			<center>
				<?php 
				if(isset($doc_details->approved_by)){
					if($doc_details->approved_by->signatory_status=="PERMANENT"){
						echo strtoupper($doc_details->approved_by->designation)." ";
					}else{
						echo strtoupper($doc_details->approved_by->signatory_status." - ".$doc_details->approved_by->designation. " ");
					}
					if($doc_details->approved_by->designation=="DIVISION CHIEF" || $doc_details->approved_by->designation=="CENTER HEAD" ){
						echo strtoupper($doc_details->approved_by->divunit_abv);
					}
				}
				?>
			</center>
		</td>
	</tr>
</table>
<table class="table nbt nbb" width="100%">
	<tr>
		<td colspan="2"><hr>To be filled-up by MIS Staff</td>
	</tr>
	<tr>
		<td colspan="2"><center><b>Service Report</b></center></td>
	</tr>
	<tr>
		<td width="20%">Diagnosis:</td>
		<td style="border-bottom: 1px solid black !important"><?= $doc_details->diagnosis ?></td>
	</tr>
	<tr>
		<td width="20%">Recommendation:</td>
		<td style="border-bottom: 1px solid black !important"><?= $doc_details->recommendation ?></td>
	</tr>
	<tr>
		<td width="20%">Service Performed:</td>
		<td style="border-bottom: 1px solid black !important"><?= $doc_details->service_performed ?></td>
	</tr>
	<tr>
		<td width="20%">Remarks:</td>
		<td style="border-bottom: 1px solid black !important"><?= $doc_details->remarks ?></td>
	</tr>
</table>
<table class="nbt" width="100%">
	<tr>
		<td width="20%">Service Rendered By:</td>
		<td width="40%" style="border-bottom: 1px solid black !important">
			<?php 
			if($doc_details->service_by != 0 || $doc_details->service_by != ""){
				echo '<div class="sig_span"><img src="data:image/'.$doc_details->service_by_img_type.';base64,'.base64_encode($doc_details->service_by_sig).'" id="a_sig"></div>';

				echo "<br><center>".$doc_details->service_by."</center>";
			}
			?>
			</td>
		<td width="10%">Date:</td>
		<td width="30%" style="border-bottom: 1px solid black !important"><?= $doc_details->service_date ?></td>
	</tr>
	<tr>
		<td width="20%">Noted By:</td>
		<td width="40%" style="border-bottom: 1px solid black !important">
			<?php 
			if($doc_details->noted_by_mis != 0 || $doc_details->noted_by_mis != ""){
				echo '<div class="sig_span"><img src="data:image/'.$doc_details->noted_by_img_type.';base64,'.base64_encode($doc_details->noted_by_sig).'" id="a_sig"></div>';

				echo "<br><center>".$doc_details->noted_by_mis."</center>";
			}
			?>
		</td>
		<td width="10%">Date:</td>
		<td width="30%" style="border-bottom: 1px solid black !important"><?= $doc_details->noted_date ?></td>
	</tr>
	<tr>
		<td colspan="4"><br></td>
	</tr>
</table>
