<style>
body{
	font-size: 11pt;
	font-family: "Times New Roman", Times, serif;
}
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
	top: 640px;
	left: 130px;
}
#ab_sig{
	position: absolute;
	top: 650px;
	left: 480px;
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
.width200px {
  height: 12px;
  width: 200px;
  background-color: white;
  border-bottom: 1px solid;
  display: inline-block;
}
.width250px {
  height: 16px;
  width: 250px;
  background-color: none;
  border-bottom: 1px solid;
  display: inline-block;
}
.div_sig_display {
  height: 65px;
  width: 250px;
  background-color: none;
  border-bottom: 1px solid;
  display: inline-block;
  text-align:center;
}
.div_sig_display2 {
  height: 60px;
  width: 180px;
  background-color: none;
  display: inline-block;
  text-align:center;
}

.div_sig_display2 img {
	margin-left: auto;
  margin-right: auto;
  display: block;
  align-content: center;
}
.ab_sig_display {
  height: 65px;
  width: 180px;
  background-color: none;
  display: inline-block;
  text-align:center;
}
.ab_sig_display img {
	margin-left: auto;
  margin-right: auto;
  display: block;
  align-content: center;
}
.printedname_display {
  height: 16px;
  width: 233px;
  background-color: none;
  border-bottom: 1px solid;
  display: inline-block;
  text-align: center;
}
.position_display{
  height: 16px;
  width: 258px;
  background-color: none;
  border-bottom: 1px solid;
  display: inline-block;
  text-align: center;
}
.date_sig {
  height: 16px;
  width: 277px;
  background-color: none;
  border-bottom: 1px solid;
  display: inline-block;
  text-align: center;
}

.space{
  height: 12px;
  width: 12px;	
  display: inline-block;

}
.pt20{
	font-size: 20pt;
}
.pt10{
	font-size: 10pt;
}
.pt9{
	font-size: 9pt;
}
.h150{
	height: 150px;
	text-align: center;
}
.text_v_align_top{
	vertical-align: top !important;
}
.certifier{
	height: 75px;
	text-align: center;
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
.no_border{
	border: none !important;
}
.height20px{
	min-height: 20px !important;
}
hr {
    margin-top: 5px;
    margin-bottom: 5px;
    border: 0;
    border-top: 1px solid #000000;
}
#barcode{
	position: absolute;
	top: -35px;
	right: 70px;
}
.img_sig{
	width: 100%;
}
.hideme{
	visibility: hidden !important;
}

</style>
<span id="barcode"><img src="<?= $img ?>"></span>
<div class="pull-right">
	<i>Appendix 14</i>
</div>
<br><br>
<table class="table" width="100%">
	<tr>
		<td rowspan="3" align="center" class="no_bot_border no_top_border nbt">
			<b>BUDGET UTILIZATION REQUEST AND STATUS</b><br><br>
			<b><u>Ecosystems Research and Development Bureau</u></b>
			<br>
			<br>
		</td>
		<td  width="12%" class="nbb nbr"><b>Serial No.:</b></td>
		<td  width="25%" class="nbl" align="center"><?= ($ors!=false)?$ors->ors_burs_no:""; ?></td>
	</tr>
	<tr>
		<td class="nbt nbr nbb" ><b>Date: </b></td>
		<td class="nbl nbb" align="center"><?= ($ors!=false)?date("F d, Y",strtotime($ors->date)):""; ?></td>
	</tr>
	<tr>
		<td class="nbt nbr" style="vertical-align: top !important;"><b>Fund Cluster:</td>
		<td class="nbl" align="center"><?= $doc_details->fund_cluster ?></td>
	</tr>
</table>
<table class="table" width="100%">
	<tr>
		<td width="20%" align="center">Payee</td>
		<td width="80%"><b><?= $doc_details->payee ?></b></td>
	</tr>
	<tr style="border-bottom: 2px solid black !important;">
		<td width="20%" align="center">Office</td>
		<td width="80%"></td>
	</tr>
	<tr>
		<td width="20%" align="center">Particulars</td>
		<td width="80%"><?= $doc_details->particulars ?></td>
	</tr>
</table>
<table class="table" width="100%">
	<tr style="font-size:10pt !important; border-bottom: 2px solid black !important;">
		<td align="center" width="25%"><b>Responsibility Center Code</b></td>
		<td align="center" width="30%"><b>Responsibility Center Description</b></td>
		<td align="center" width="10%"><b>Year of Implementation</b></td>
		<td align="center" width="12%"><b>UACS Object Code</b></td>
		<td align="center" width="13%"><b>UACS Object description</b></td>
		<td align="center" width="10%"><b>Amount</b></td>
	</tr>
	<?php 
	$total = 0;
	foreach($obr_details as $val){ 
		$total +=$val->amount;
		?>
		<tr style="font-size:9pt !important;">
			<td align="center" width="25%">
				<div class="height20px"><?= $val->rc_code ?></div>
			</td>
			<td align="center" width="30%">
				<div class="height20px"><?= $val->rc_desc ?></div>
			</td>
			<td align="center" width="10%">
				<div class="height20px"><?= ($ors!=false)?$ors->yoi:""; ?></div>
			</td>
			<td align="center" width="12%">
				<div class="height20px"><?= $val->uacs_code ?></div>
			</td>
			<td align="center" width="13%">
				<div class="height20px"><?= $val->uacs_desc ?></div>
			</td>
			<td align="center" width="10%">
				<div class="height20px"><?= number_format($val->amount, 2) ?></div>
			</td>
		</tr>
	<?php } 
	for($i =count($obr_details); $i<5; $i++){
	?>
		<tr>
			<td align="center" width="25%">&nbsp;</td>
			<td align="center" width="30%"></td>
			<td align="center" width="10%"></td>
			<td align="center" width="10%"></td>
			<td align="center" width="15%"></td>
			<td align="center" width="10%"></td>
		</tr>
	<?php
	}
	?>
	<tr style="font-size:9pt !important; border-bottom: 2px solid black !important;">
		<td align="center" width="25%">&nbsp;</td>
		<td align="center" width="30%" style="text-align: right;">Total</td>
		<td align="center" width="10%"></td>
		<td align="center" width="10%"></td>
		<td align="center" width="15%"></td>
		<td align="center" width="10%"><b><?= number_format($total, 2) ?></b></td>
	</tr>
</table>
<table class="table pt9" width="100%">
	<tr>
		<td width="5%">A</td>
		<td class="no_border" width="10%"><b>Certified:</b></td>
		<td class="nbb nbl" width="35%" rowspan="2">
			 Charges to appropriation / budget necessary, lawful and under my direct supervision; and supporting documents valid, proper and legal
		</td>
		<td width="5%">B</td>
		<td class="no_border" width="9%"><b>Certified:</b></td>
		<td class="nbb nbt nbl" width="36%" rowspan="2">Budget available and utilized for the purpose/adjustment necessary as indicated above</td>
	</tr>
	<tr>
		<td class="nbb nbt nbl" colspan="3">&nbsp;</td>
		<td class="nbb nbt nbl" colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td class="nbt" colspan="3">
			Signature : 
			<span class="div_sig_display">
				<div class="div_sig_display2">
				<?php
				$div_sig_class = ($doc_details->certifier->is_signed)?"":"hideme";
				echo '<img src="data:image/'.$doc_details->certifier->img_type.';base64,'.base64_encode($doc_details->certifier->signature).'" class="img_sig '.$div_sig_class.'">';
				?>
				</div>			
			</span>
			<br>
			Printed Name: 
			<span class="printedname_display">
				<?= $doc_details->certifier->fullname_f ?>
			</span>
			<br>
			<br>
			<span style="height:40px; vertical-align: text-top;">Position :</span>
			<span class="position_display">
				<?php 
				$status =  $doc_details->certifier->signatory_status == "PERMANENT"?"":$doc_details->certifier->signatory_status." - ";
				echo $status.$doc_details->certifier->designation.", "				
				?>
				<?= $doc_details->certifier->designation_id==6?$doc_details->certifier->unit_abv:$doc_details->certifier->divunit_abv; ?>
			</span>
			<br>
			<br>
			Date: 			
			<span class="date_sig">
				<?= $doc_details->certifier->date_signed!=null?date("F d, Y",strtotime($doc_details->certifier->date_signed)):""; ?>
			</span>
			<br><br>
		</td>
		<td class="nbt" colspan="3">
			Signature : 
			<span class="div_sig_display">
				<div class="div_sig_display2">
				<?php
				$div_ab_class = ($doc_details->ab->is_signed)?"":"hideme";
				echo '<img src="data:image/'.$doc_details->ab->img_type.';base64,'.base64_encode($doc_details->ab->signature).'" class="img_sig '.$div_ab_class.'">';
				?>
				</div>			
			</span>
			<br>
			Printed Name: 
			<span class="printedname_display">
				<?= $doc_details->ab->fullname_f ?>
			</span>
			<br>
			<br>
			<span style="height:40px; vertical-align: text-top;">Position :</span>
			<span class="position_display">
				<?php 
				if($doc_details->ab->oic_uid!=0){
					echo "In-charge, Budget Section";
				}else{
					$status =  $doc_details->ab->signatory_status == "PERMANENT"?"":$doc_details->ab->signatory_status." - ";
					echo $status.$doc_details->ab->designation.", ";				
					echo $doc_details->ab->designation_id==6?$doc_details->ab->unit_abv:$doc_details->ab->divunit_abv;
				}
				?>	


			</span>
			<br>
			<br>
			Date: 			
			<span class="date_sig">
				<?= $doc_details->ab->date_signed!=null?date("F d, Y",strtotime($doc_details->ab->date_signed)):""; ?>
			</span>
			<br><br>
		</td>
	</tr>
</table>	
<table class="table pt9" width="100%" align="center">
	<tr>
		<td>C</td>
		<td colspan="7"></td>
	</tr>
	<tr>
		<td colspan="3">Reference</td>
		<td colspan="5">Amount</td>
	</tr>
	<tr>
		<td width="10%" align="center" rowspan="2">Date</td>
		<td width="20%" align="center" rowspan="2">Particulars</td>
		<td width="15%" align="center" rowspan="2">BURS/ JEV/ RCI/ RADAI/ RTRAI No.</td>
		<td width="10%" align="center" rowspan="2">Utilization<br>(a)</td>
		<td width="10%" align="center" rowspan="2">Payable<br>(b)</td>
		<td width="10%" align="center" rowspan="2">Payment<br>(c)</td>
		<td width="15%" align="center" colspan="2">Balance</td>
	</tr>
	<tr>
		<td width="15%" align="center">Not Yet Due<br>(a-b)</td>
		<td width="10%" align="center">Due and Demandable</td>
	</tr>
	<tr>
		<td width="10%" align="center">
			<?= ($ors!=false)?date("F d, Y",strtotime($ors->date)):""; ?>
		</td>
		<td width="20%" align="center"><?= $doc_details->particulars ?></td>
		<td width="15%" align="center"><?= ($ors!=false)?$ors->ors_burs_no:""; ?></td>
		<td width="10%" align="center"><?= number_format($total, 2) ?></td>
		<td width="10%" align="center"><?= ($ors!=false)?$ors->payable:"-"; ?></td>
		<td width="10%" align="center"><?= ($ors!=false)?$ors->payment:""; ?></td>
		<td width="15%" align="center"><?= ($ors!=false)?$ors->nyd:"-"; ?></td>
		<td width="10%" align="center"><?= number_format($total, 2) ?></td>
	</tr>
</table>
