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
.signatures{
    page-break-inside: avoid;
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
#claimnant_sig{
	position: absolute;
	top: 665px;
	left: 450px;
}
#div_sig{
	position: absolute;
	top: 750px;
	left: 130px;
}

#ad_sig{
	position: absolute;
	top: 750px;
	left: 450px;
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
  height: 12px;
  width: 250px;
  background-color: white;
  border-bottom: 1px solid;
  display: inline-block;
}
.space{
  height: 12px;
  width: 12px;	
  display: inline-block;

}
.pt10{
	font-size: 10pt;
}
.pt9{
	font-size: 9pt;
}
.pt11{
	font-size: 11pt;
}
.pt14{
	font-size: 14pt;
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
	top: 10px;
	right: 30px;
}
.underlined{
	border-bottom: 1px solid !important;
	min-width: 200px !important;
	display: inline-block;
}
.img_sig{
	width: 100%;
}
.hideme{
	visibility: hidden !important;
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
</style>
<span id="barcode"><img src="<?= $img ?>"></span>
<table class="table" width="100%" style="border-bottom: 0px !important">
	<tr>
		<td align="center" class="no_bot_border">
			<span class="pt11">
			Republic of the Philippines<br>
			Department of Environment and Natural Resources<br>
			<b>Ecosystems Research and Development Bureau</b><br>
			College, Laguna<br>
			</span>
			<br>
			<span class="pt14"><b>ITINERARY OF TRAVEL</b></span>
		</td>
	</tr>
</table>
<table class="table" style="border-top: 0px !important;border-bottom: 0px !important;" width="100%">
	<tr>
		<td width="15%" align="center" class="no_border">Entity Name:</td>
		<td colspan="3" class="no_border"><span class="underlined"><?= $doc_details->entity_name ?></span></td>
	</tr>
	<tr>
		<td width="15%" align="center" class="no_border">Fund Cluster:</td>
		<td width="45%" class="no_border">
			<span class="underlined"><?= isset($doc_details->fund_cluster)?$doc_details->fund_cluster:"&nbsp;"; ?></span>
		</td>
		<td width="10%" align="center" class="no_border">No.:</td>
		<td width="30%" class="no_border">
			<span class="underlined"><?= $doc_details->iot_no ?>&nbsp;</span>
		</td>
	</tr>
</table>
<table class="table" width="100%">
	<tr>
		<td width="15%"  class="nbb nbr">Name:</td>
		<td width="35%" class="nbb nbr nbl">
			<span class="underlined"><?= $doc_details->name ?></span>
		</td>
		<td width="15%"  class="nbb nbr">Date of Travel:</td>
		<td width="35%" class="nbb nbr nbl">
			<span class="underlined"><?= $doc_details->travel_duration ?></span>
		</td>
	</tr>
	<tr>
		<td width="15%" class="nbb nbt nbr">Position:</td>
		<td width="35%" class="nbb nbt nbr nbl">
			<span class="underlined"><?= $doc_details->position ?></span>
		</td>
		<td width="20%"  class="nbb nbt nbr">Purpose of Travel :</td>
		<td width="30%" class="nbb nbt nbr nbl">
			<span class="underlined"><?= $doc_details->purpose ?>&nbsp;</span>
		</td>
	</tr>
	<tr>
		<td width="15%" class="nbt nbr">Official Station:</td>
		<td width="35%" class="nbt nbr nbl">
			<span class="underlined"><?= $doc_details->official_station ?></span>
		</td>
		<td colspan="2" class="nbt"></td>
	</tr>
</table>
<table class="table" width="100%">
	<tr style="font-size:10pt !important; border-bottom: 2px solid black !important;">
		<td rowspan="2" align="center" width="10%">Date</td>
		<td rowspan="2" align="center" width="25%">Places to be visited</td>
		<td align="center" width="25%" colspan="2">Time</td>
		<td rowspan="2" align="center" width="10%">Means of Transportation</td>
		<td rowspan="2" align="center" width="5%">Transportation</td>
		<td rowspan="2" align="center" width="10%">Per Diem</td>
		<td rowspan="2" align="center" width="5%">Others</td>
		<td rowspan="2" align="center" width="10%">Total Amount</td>
	</tr>
	<tr style="font-size:10pt !important; border-bottom: 2px solid black !important;">
		<td align="center" width="12%">Departure</td>
		<td align="center" width="13%">Arrival</td>
	</tr>
	<?php 
	$total = 0;
	foreach($iot_details as $val){ 
		$td_total = $val->per_diem;
		if(is_numeric($val->transpo)){
			$td_total =$td_total+$val->transpo;
		}
		if(is_numeric($val->others)){
			$td_total =$td_total+$val->others;
		}
			$total +=$td_total;
		?>
		<tr style="font-size:9pt !important;">
			<td align="center" width="10%">
				<div class="height20px"><?= $val->date ?></div>			
			</td>
			<td align="center" width="25%">
				<div class="height20px"><?= $val->place ?></div>			
			</td>
			<td align="center" width="13%">
				<div class="height20px"><?= $val->departure ?></div>			
			</td>
			<td align="center" width="12%">
				<div class="height20px"><?= $val->arrival ?></div>			
			</td>
			<td align="center" width="10%">
				<div class="height20px"><?= $val->means_transpo ?></div>			
			</td>
			<td align="center" width="5%">
				<div class="height20px"><?= $val->transpo ?></div>			
			</td>
			<td align="center" width="10%">
				<div class="height20px"><?= $val->per_diem ?></div>			
			</td>
			<td align="center" width="5%">
				<div class="height20px"><?= $val->others ?></div>			
			</td>
			<td align="center" width="10%">
				<div class="height20px"><?= number_format((float)$td_total, 2, '.', ','); ?></div>			
			</td>
		</tr>
	<?php } 
	for($i =count($iot_details); $i<5; $i++){
	?>
		<tr>
			<td align="center" width="10%"><div class="height20px"></div></td>
			<td align="center" width="25%"></td>
			<td align="center" width="13%"></td>
			<td align="center" width="12%"></td>
			<td align="center" width="10%"></td>
			<td align="center" width="5%"></td>
			<td align="center" width="10%"></td>
			<td align="center" width="5%"></td>
			<td align="center" width="10%"></td>
		</tr>
	<?php
	}
	?>
	<tr style="font-size:9pt !important; border-bottom: 2px solid black !important;">
		<td colspan="4" align="center" style="text-align:right;">&nbsp;Total</td>
		<td colspan="4"  align="center" width="10%"></td>
		<td align="center" width="10%"><b><?= number_format((float)$total, 2, '.', ','); ?></b></td>
	</tr>
</table>
<table class="table signatures" width="100%">
	<tr>
		<td rowspan="2" width="50%">
			<span id="barcode_bot"><img src="<?= $img ?>"></span><br>
			I certify that : (1) I have reviewed the foregoing  itinerary,    (2)  the  travel  is necessary to  the service, (3) the period covered   is   reasonable   and   (4)  the expenses claimed are proper.   
			<br>
			<br>
			<br>
			<center>
				<?php
				if($doc_details->div_sig != false){
					$div_sig_class = ($doc_details->div_sig->is_signed == 0)?"hideme":"";
				?>
					<div class="sig_span">
					<?php
						echo '<img src="data:image/'.$doc_details->div_sig->img_type.';base64,'.base64_encode($doc_details->div_sig->signature).'" class="img_sig '. $div_sig_class.'">';
					?></div>
					<br>
					<b><?= strtoupper($doc_details->div_sig->fullname) ?></b><br>
					Signature over Printed Name<br>
					Immediate Supervisor
				<?php }else{ 
					$ad_sig_class = ($doc_details->ad_sig->is_signed == 0)?"hideme":"";
					?> 
					<div class="sig_span">
					<?php
						echo '<img src="data:image/'.$doc_details->ad_sig->img_type.';base64,'.base64_encode($doc_details->ad_sig->signature).'" class="img_sig '. $ad_sig_class.'">';
					?></div>
					<br>
					<b><?= strtoupper($doc_details->ad_sig->fullname) ?></b><br>
					Signature over Printed Name<br>
					Immediate Supervisor
				<?php } ?>      
			</center> 
		</td>
		<td width="50%">
			<b>Prepared by:</b>
			<br>
			<center>
				<div class="sig_span">
				<?php 
					echo '<img class="img_sig" src="data:image/'.$doc_details->claimnant_sig_type.';base64,'.base64_encode($doc_details->claimnant_sig).'" id="">'; 
				?>	
				</div>	<br>		
				<br><b><?= strtoupper($doc_details->name) ?></b><br>
				Signature over Printed Name
			</center>        
		</td>
	</tr>
	<tr>
		<td>
			<b>Approved by:</b>
			<br>
			<br>
			<br>
			<center>
				<span class="sig_span">
				<?php
					$ad_sig_class = ($doc_details->ad_sig->is_signed == 0)?"hideme":"";
				echo '<img src="data:image/'.$doc_details->ad_sig->img_type.';base64,'.base64_encode($doc_details->ad_sig->signature).'" class="img_sig '. $ad_sig_class.'">';
				?>
				</span>
				<br>
				<b><?= strtoupper($doc_details->ad_sig->fullname) ?></b><br>
				Signature over Printed Name<br>
				Agency Head/Authorized Representative
			</center>        
		</td>
	</tr>
</table>
