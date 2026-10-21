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
	right: 180px;
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

</style>
<?php
$div_sig_class = "";
$aa_sig_class = "";
$ad_sig_class = "";
foreach($signature_status as $val){
	//division signatory
	if($val->signatory_type == 3 && $val->is_signed == 0){
		$div_sig_class = "hideme";
	}
	//Accounting signatory
	if($val->signatory_type == 5 && $val->is_signed == 0){
		$aa_sig_class = "hideme";
	}
	//ad signatory
	if($val->signatory_type == 2 && $val->is_signed == 0){
		$ad_sig_class = "hideme";
	}
}

?>
<span id="barcode"><img src="<?= $img ?>"></span>
<div class="pull-right">
	<i>Appendix 32</i>
</div>
<br><br>
<table class="table" width="100%">
	<tr>
		<td rowspan="2" width="75%">
			<center>
				<small class="small-font">
					Department of Environment and Natural Resources<br>
					Ecosystems Research and Development Bureau<br>
					College, Laguna 4031<br>
				</small>
				<h3>DISBURSEMENT VOUCHER</h3>
			</center>
		</td>
		<td width="25%">
			<div class="col-sm-12 col-md-12">
				<div class="col-sm-6 col-md-6">
					Fund Cluster: <br>
				</div>
				<div class="col-sm-6 col-md-6">
					<center class="pt-20"><?= $obr_main?$obr_main[0]->fund_cluster:""; ?></center>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td width="25%">
			Date:<U><?= ($dv_no)?date("F d, Y",strtotime($dv_no[0]->date_approved)):""; ?></U><br>
			DV No.:<u class="pt11"><?= ($dv_no)?$dv_no[0]->approved_number:""; ?></u>
		</td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td width="13%">Mode of<br>payment</td>
		<td width="92%" colspan="3">
			<div class="space"></div>
			<div class="space"></div>
			<div class="square"></div><span> MDS Check</span>
			<div class="space"></div>
			<div class="square"></div><span> Commercial Check</span>
			<div class="space"></div>
			<div class="square"></div><span> ADA</span>
			<div class="space"></div>
			<div class="square"></div><span> Other (Please Specify)</span>
		</td>
	</tr>
	<tr>
		<td width="13%">Payee</td>
		<td width="40%" class="pt10"><U><?= $doc_details->payee ?></U></td>
		<td>TIN/Employee No.:<br><div class="space"></div></td>
		<td>ORS/BURS No.:<br><div class="ors_burs_no"><?= $ors_burs?$ors_burs[0]->ors_burs_no:""; ?></div><br></td>
	</tr>
	<tr>
		<td width="13%">Address</td>
		<td colspan="3" class="pt10"><U><?= $doc_details->address ?></U></td>
	</tr>
</table>
<table width="100%">
	<tr align="center">
		<td width="50%">Particulars</td>
		<td>Responsibility<br>Center</td>
		<td>MFO/PAP</td>
		<td>Amount</td>
	</tr>
	<tr align="center">
		<td width="50%" align="center" class="h130">
			<?= $doc_details->details ?>
		</td>
		<td><?= $doc_details->charged_to ?></td>
		<td></td>
		<td><?= number_format(str_replace(',','',$doc_details->amount),2,'.',','); ?></td>
	</tr>
	<tr align="center">
		<td align="center">Amount Due</td>
		<td></td>
		<td></td>
		<td><?= number_format(str_replace(',','',$doc_details->amount),2,'.',','); ?></td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td width="2%"><b>A.</b></td>
		<td width="98%"> Certified:  Expenses/Cash Advance necessary,  lawful and  incurred under my direct supervision.</td>
	</tr>
	<tr>
		<td colspan="2" class="certifier">
			<span class="sig_span">
			<?php
			if($doc_details->certifier->is_signed==1){
				echo '<img src="data:image/'.$doc_details->certifier->img_type.';base64,'.base64_encode($doc_details->certifier->signature).'" class="img_sig">';
			}
			?>
			</span>
			<br>
			<U><?= $doc_details->certifier->fullname ?></U><br>
			<?php
			$status = $doc_details->certifier->signatory_status == "PERMANENT"?"":$doc_details->certifier->signatory_status." - ";
			if($doc_details->certifier->orig_uid != 0 && $doc_details->certifier->is_signed == 1){
				if(!in_array($doc_details->certifier->designation_id,[1,2])){
					echo "In-Charge - ".$doc_details->certifier->orig_unitabv;
				}else{
					echo "In-Charge - ".$doc_details->certifier->orig_divabv;
				}
			}else{
				echo $status.$doc_details->certifier->designation;
				if(!in_array($doc_details->certifier->designation_id,[1,2])){
					echo ", " .$doc_details->certifier->divunit_abv;
				}
			}
			?>
		</td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td width="2%"><b>B.</b></td>
		<td width="98%" colspan="8"> <b>Accounting Entry:</b></td>
	</tr>
	<tr align="center">
		<td colspan="6" width="50.9%">Account Title</td>
		<td width="19.1%">UACS Code</td>
		<td width="15%">Debit</td>
		<td width="15%">Credit</td>
	</tr>
	<?php 
	if($ae){
	foreach($ae as $val){ 
	$height = 50/count($ae);
	?>
	<tr align="center" class="pt9">
		<td colspan="6" width="40%" style="height:<?= $height ?>px;">
				<?= $val->account_title; ?>
		</td>
		<td><?= $val->uacs_code; ?></td>
		<td><?= number_format((float)$val->debit, 2, '.', ''); ?></td>
		<td><?= number_format((float)$val->credit, 2, '.', ''); ?></td>
	</tr>
	<?php } 
	}else{ 
	?>
	<tr align="center">
		<td colspan="6" width="40%"><div style="height:50px;" id="certifier">
		</td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<?php } ?>
</table>
<table width="100%">
	<tr>
		<td width="2%"><b>C.</b></td>
		<td width="47.5%" > <b>Certified:</b></td>
		<td width="2%"><b>D.</b></td>
		<td width="46.7%"> <b>Approved for Payment</b></td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="space"></div>
			<div class="rectangle"></div><span class="pt10"><small>Cash available</small></span><br>
			<div class="space"></div>
			<div class="rectangle"></div><span class="pt10"><small>Subject to Authority to Debit Account (when applicable)</span><br>
			<div class="space"></div>
			<div class="rectangle"></div><span class="pt10"><small>Supporting documents complete and amount claimed </small></span><br>
			<div class="space"></div><div class="space"></div><div class="space"></div><div class="space"></div>proper
		</td>
		<td colspan="2"></td>
	</tr>
</table>
<table width="100%">
	<tr align="center">
		<td width="15%">Signature</td>
		<td width="37.3%">
				<span class="sig_span">
				<?php
				if($doc_details->aa->is_signed==1){

					echo '<img src="data:image/'.$doc_details->aa->img_type.';base64,'.base64_encode($doc_details->aa->signature).'" class="img_sig '.$aa_sig_class.'">';
				}
				?>
				</span>
			</td>
		<td width="15%">Signature</td>
		<td width="32.7%">
			<span class="sig_span">
			<?php
			if($doc_details->ad->is_signed==1){
				echo '<img src="data:image/'.$doc_details->ad->img_type.';base64,'.base64_encode($doc_details->ad->signature).'" class="img_sig '.$ad_sig_class.'">';
			}
			?>
			</span>
		</td>
	</tr>
	<tr align="center">
		<td width="15%">Printed Name</td>
		<td width="37.3%" id="aa" class="pt10"><b><?= $doc_details->aa->fullname ?></b></td>
		<td width="15%">Printed Name</td>
		<td width="32.7%" id="ad" class="pt10"><b><?= $doc_details->ad->fullname ?></b></td>
	</tr>
	<tr align="center">
		<td width="15%">Position</td>
		<td width="37.3%" class="pt9">
			<?php 
				if($doc_details->aa->orig_uid != 0 && $doc_details->aa->is_signed == 1){
					echo "In-charge - Accounting<hr>Head, Accounting Unit/Authorized Representative";
				}else{
					echo "Chief Accounting<hr>Head, Accounting Unit/Authorized Representative";
				}
			?>
			</td>
		<td width="15%">Position</td>
		<td width="32.7%" class="pt9">
			<?php $status = $doc_details->ad->signatory_status == "PERMANENT"?"":$doc_details->ad->signatory_status." - ";
			if($doc_details->ad->orig_uid != 0 && $doc_details->ad->is_signed == 1){
				echo "In-Charge - ".$doc_details->ad->orig_divabv;
			}else{
				echo $status.$doc_details->ad->designation;
			}
			?>
			<hr>
			Agency Head/Authorized Representative</td>
	</tr>
	<tr align="center">
		<td width="15%">Date</td>
		<td width="37.3%"></td>
		<td width="15%">Date</td>
		<td width="32.7%"></td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td width="2%"><b>E.</b></td>
		<td width="78%" colspan="4"><b>Receipt of Payment</b></td>
		<td width="20%" rowspan="2" class="pt9" style="vertical-align: top;text-align: left;">
		JEV No.
		</td>
	</tr>
	<tr>
		<td colspan="2" class="pt9">Check / ADA No.:</td>
		<td><div style="height:30px;" id="ada"></td>
		<td class="pt9">Date:</td>
		<td class="pt9">Bank Name & Account Number:</td>
	</tr>
	<tr>
		<td colspan="2" class="pt9">Signature:</td>
		<td><div style="height:30px;" id="sig"></td>
		<td class="pt9">Date:</td>
		<td class="pt9">Printed Name:</td>
		<td width="20%" rowspan="2" class="pt9" style="vertical-align: top;text-align: left;">
		Date
		</td>
	</tr>
	<tr>
		<td colspan="5">Official Receipt No. & Date/Other Documents</td>
	</tr>
</table>