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
  height: 60px; /* Or whatever */
  width: auto;
  display: block;
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
  height: 60px; /* Or whatever */
  width: auto;
  display: block;
}
.printedname_display {
  height: 16px;
  width: 240px;
  background-color: none;
  border-bottom: 1px solid;
  display: inline-block;
  text-align: center;
}
.position_display{
  height: 40px;
  width: 257px;
  background-color: none;
  display: inline-block;
  text-align: center;
}
.date_sig {
  height: 16px;
  width: 275px;
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
	<i>Appendix B</i>
</div>
<br><br>
<table class="table" width="100%">
	<tr>
		<td align="center" class="no_bot_border" width="65%"><b>OBLIGATION REQUEST</b></td>
		<td  width="15%"><b>Date:</b></td>
		<td  width="30%" align="center"><b><?= date("F d, Y",strtotime($doc_details->date)) ?></b></td>
	</tr>
	<tr>
		<td rowspan="2" align="center" class="no_bot_border no_top_border">
			<b><u><?= $doc_details->div_name ?></u></b>
			<br>
			<b><small>Division / Center</small></b>
		</td>
		<td><b>Funding Source: </b></td>
		<td align="center"><?= $doc_details->funding_source ?></td>
	</tr>
	<tr>
		<td><b>Funding Source Code:</td>
		<td class="pt20" align="center"><?= $doc_details->funding_source_code ?></td>
	</tr>
</table>
<table class="table" width="100%">
	<tr>
		<td width="20%" align="center">Payee</td>
		<td width="80%"><b><?= $doc_details->payee ?></b></td>
	</tr>
	<tr>
		<td width="20%" align="center">Address</td>
		<td width="80%"><?= $doc_details->address ?></td>
	</tr>
	<tr style="border-bottom: 2px solid black !important;">
		<td width="20%" align="center">Particulars</td>
		<td width="80%"><?= $doc_details->particulars ?></td>
	</tr>
</table>
<table class="table" width="100%">
	<tr style="font-size:10pt !important; border-bottom: 2px solid black !important;">
		<td align="center" width="20%">Responsibility Center Code</td>
		<td align="center" width="30%">Responsibility Center Description</td>
		<td align="center" width="10%">PREXC</td>
		<td align="center" width="10%">UACS Object Code</td>
		<td align="center" width="20%">UACS Object Description</td>
		<td align="center" width="10%">Amount</td>
	</tr>
	<?php 
	$total = 0;
	foreach($obr_details as $val){ 
		$total +=$val->amount;
		?>
		<tr style="font-size:9pt !important;">
			<td align="center" width="20%">
				<div class="height20px"><?= $val->rc_code ?></div>
			</td>
			<td align="center" width="30%">
				<div class="height20px"><?= $val->rc_desc ?></div>
			</td>
			<td align="center" width="10%">
				<div class="height20px"><?= $val->prexc ?></div>
			</td>
			<td align="center" width="10%">
				<div class="height20px"><?= $val->uacs_code ?></div>
			</td>
			<td align="center" width="20%">
				<div class="height20px"><?= $val->uacs_desc ?></div>
			</td>
			<td align="center" width="10%">
				<div class="height20px"><?= number_format($val->amount,2,'.',','); ?></div>
			</td>
		</tr>
	<?php } 
	for($i =count($obr_details); $i<5; $i++){
	?>
		<tr>
			<td align="center" width="20%">&nbsp;</td>
			<td align="center" width="30%"></td>
			<td align="center" width="10%"></td>
			<td align="center" width="10%"></td>
			<td align="center" width="20%"></td>
			<td align="center" width="10%"></td>
		</tr>
	<?php
	}
	?>
	<tr style="font-size:9pt !important; border-bottom: 2px solid black !important;">
		<td align="center" width="20%">&nbsp;</td>
		<td align="center" width="30%" style="text-align: right;">Total</td>
		<td align="center" width="10%"></td>
		<td align="center" width="10%"></td>
		<td align="center" width="20%"></td>
		<td align="center" width="10%"><b><?= number_format($total,2,'.',','); ?></b></td>
	</tr>
</table>
<table class="table pt9" width="100%">
	<tr>
		<td width="5%">A</td>
		<td class="no_border" width="5%"></td>
		<td class="nbb nbl" width="40%">
			Certified: Charges to appropriation/alloment are
		</td>
		<td width="5%">B</td>
		<td class="no_border" width="9%"></td>
		<td class="nbb nbt nbl" width="36%"></td>
	</tr>
	<tr>
		<td class="no_border"></td>
		<td class="nbb nbt nbl" colspan="2">
			necessary, lawful and under my direct supervision;and
		</td>
		<td class="no_border "></td>
		<td class="nbb nbt nbl" colspan="2">
			O.R.S. No.:
			<span class="width200px"><span class="space"></span><?= $ors?$ors[0]->ors_burs_no:""; ?></span>
		</td>
	</tr>
	<tr>
		<td class="no_border"></td>
		<td class="nbb nbt nbl" colspan="2">
			supporting documents valid, proper and legal
		</td>
		<td class="no_border"></td>
		<td class="nbb nbt nbl" colspan="2">
			O.R.S. Date:<span class="width200px"><span class="space"></span><?= $ors?date("F d, Y",strtotime($ors[0]->date)):""; ?></span>
		</td>
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
				<?= $doc_details->certifier->fullname ?>
			</span>
			<br>
			<br>
			<span style="height:40px; vertical-align: text-top;">Position :</span>
			<span class="position_display">
				<?php
					if($doc_details->certifier->orig_uid != 0 && $doc_details->certifier->is_signed == 1){
						if(!in_array($doc_details->certifier->designation_id,[2])){
							echo "In-Charge - ".$doc_details->certifier->orig_unitabv;
						}else{
							echo "In-Charge - ".$doc_details->certifier->orig_divabv;
						}
					}else{
						echo $doc_details->certifier->designation;
						if(!in_array($doc_details->certifier->designation_id,[1,2])){
							echo ", ";
							echo $doc_details->certifier->designation_id==6?$doc_details->certifier->unit_abv:$doc_details->certifier->divunit_abv;
						}
					}
				?>
				<br>
			Head, Requesting Office/Authorized<br>
			Representative
			</span>
			<br>
			<br>
			Date: 			
			<span class="date_sig">
				<?php 
				echo ($doc_details->certifier->is_signed)?date("F d, Y",strtotime($doc_details->certifier->date_signed)):"";
				?>
			</span>
			<br><br>
		</td>
		<td class="no_border" align="left" colspan="2">
			Data Provided by : 
		</td>
		<td class="no_border">
			<span class="ab_sig_display">
			<?php
				$ab_sig_class = ($doc_details->ab->is_signed)?"":"hideme";
			echo '<img style="width: 100%;" src="data:image/'.$doc_details->ab->img_type.';base64,'.base64_encode($doc_details->ab->signature).'" class="'.$ab_sig_class.'">';
			?>
			</span><br>
			<b><?= $doc_details->ab->fullname ?></b>
			<br>
			Signature Over Printed Name<br>
			<?php 
				if($doc_details->ab->oic_uid!=0){
					echo "In-charge, Budget Section";
				}else{
					echo "Designated Budget Office Personnel";
				}
			?>	
		</td>
	</tr>
</table>	
