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
.div_sig_display {
  height: 40px;
  width: 320px;
  background-color: none;
  display: inline-block;
  text-align:center;
  padding-bottom: 5px;
  vertical-align: top;
/*  border: 1px solid;
*/}

.div_sig_display img {
	margin-left: auto;
  margin-right: auto;
  height: 60px; /* Or whatever */
  width: auto;
  display: block;
}

.pull-right {
    float: right;
}
.small-font{
	font-size: 8pt;
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
.h150{
	height: 150px;
	text-align: center;
}
hr {
    margin-top: 5px;
    margin-bottom: 5px;
    border: 0;
    border-top: 1px solid #f4f4f4;
}
#barcode{
	position: absolute;
	top: 0px;
	right: 10px;
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
.no_border{
	border: none !important;
}

.width70px {
  height: 17px;
  width: 70px;
  background-color: white;
  border-bottom: 1px solid;
  display: inline-block;
  text-align: center;
}
.width200px {
  height: 17px;
  width: 200px;
  background-color: white;
  border-bottom: 1px solid;
  display: inline-block;
  text-align: center;
}
.width350px {
  height: 17px;
  width: 350px;
  background-color: white;
  border-bottom: 1px solid;
  display: inline-block;
  text-align: center;
}
.width700px {
  height: 20px;
  width: 700px;
  background-color: white;
  border-bottom: 1px solid;
  display: inline-block;
  text-align: left;
}

.width200px_nl {
  height: 17px;
  width: 200px;
  background-color: white;
  display: inline-block;
  text-align: center;
}
.width350px_nl {
  height: 17px;
  width: 350px;
  background-color: white;
  display: inline-block;
  text-align: center;
}
.space {
  height: 16px;
  width: 50px;
  background-color: white;
  display: inline-block;
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
.img_sig{
	width: 100%;
}
.hideme{
	visibility: hidden !important;
}
.indent{
	padding-left: 30px;
	padding-right: 30px;
}
.nb{
	border: none !important;
}
.printedname_display {
  height: 16px;
  width: 350px;
  background-color: none;
/*  border-bottom: 1px solid;
*/  display: inline-block;
/*  text-align: center;
*/}
.signatures{
    page-break-inside: avoid;
}
.nobreakinside{
/*    page-break-inside: avoid;
*/}
.nextpage{
    page-break-after: always;
}
@media screen {
  .signatures {
    position: absolute;
    bottom: 100;
  }
}

.page_size{
	max-height: 30px !important;
	padding-left: 30px !important;
	padding-right: 30px !important;
	/*border: 1px solid;*/
}
.img_display{
	width: 550px;
}
.h3{
	font-size: 16pt;
	font-weight: bold;
}
</style>
<span id="barcode"><img src="<?= $img ?>"></span>
<table class="table" width="100%" style="border:none;">
	<tr class="no_border">
		<td class="no_border">
			<center>
				<small class="small-font">
					Republic of the Philippines<br>
					Department of Environment and Natural Resources<br>
					Ecosystems Research and Development Bureau<br>
					College, Laguna<br>
				</small>
				<br>
				<h4>TRAVEL REPORT</h4>
			</center>
		</td>
	</tr>
</table>
<div class="page_size">
<?php
$max_div = count($travel_report);
if($travel_report){
	foreach($travel_report as $key=>$val){ 
		if($val->is_nextpage){
			echo "<div class='nextpage'></div>";
		}
	?>
	<div class="nobreakinside">
		<b>Date of Travel:</b><?= $val->dot ?><br>
		<b>Purpose of Travel:</b><?= $val->purpose ?><br>
		<b>Destination:</b><?= $val->destination ?><br><br>
		<span class="space"></span><?= $val->accomplishment ?>
		<hr>
		<br>
	</div>
<?php
	}
}
?>
</div>
<table class="table nb signatures" width="100%">
	<tr class="nb">
		<td class="nb">Prepared By:</td>
		<td class="nb">Noted By:<span class="pull-right"><img src="<?= $img ?>"></span></td>
	</tr>
	<tr class="nb">
		<td class="nb"><br>
			<span class="div_sig_display">
					<?php
					echo '<img src="data:image/'.$doc_details->claimnant_sig_type.';base64,'.base64_encode($doc_details->claimnant_sig).'" >';
					?>
			</span>
		</td>
		<td class="nb">
			<span class="div_sig_display">
				<?php
				if($doc_details->div_sig->is_signed==1){
				echo '<img src="data:image/'.$doc_details->div_sig->img_type.';base64,'.base64_encode($doc_details->div_sig->signature).'" >';
				}
				?>
			</span>
		</td>
	</tr>
	<tr>
		<td class="nb">
			<center>
				<span class="printedname_display">
						<?= strtoupper($doc_details->name) ?><br><?= $doc_details->position ?>		
				</span>
			</center>
		</td>
		<td class="nb">
			<center>
			<span class="printedname_display">
				<?= $doc_details->div_sig->fullname ?><br>
				<?php 
							if($doc_details->div_sig->orig_uid != 0 && $doc_details->div_sig->is_signed == 1){
								if(!in_array($doc_details->div_sig->designation_id,[2])){
									echo "In-Charge - ".$doc_details->div_sig->orig_unitabv;
								}else{
									echo "In-Charge - ".$doc_details->div_sig->orig_divabv;
								}
							}else{
								echo $doc_details->div_sig->sig_designation;
								if(!in_array($doc_details->div_sig->designation_id,[1,2])){
									echo ", " .$doc_details->div_sig->sig_div;
								}
							}
				?>
			</span>
			</center>
		</td>
	</tr>
</table>
<div class="nextpage"></div>
<span class="h3">APPENDIX A ( IMAGES )</span>
<img src="<?= $img ?>" class="pull-right">
<br>
<br>
<br>
<br>

<table class="table table-bordered nb">
<?php 
if($appendixa){
	foreach($appendixa as $key=>$val){ 
		?>
	<tr class="nb">
		<td class="nb" align="center">
			<img src="<?= $val ?>" class="img_display">
		</td>
	</tr>
	<tr class="nb">
		<td class="nb" align="center">
			<?= $caption[$key]; ?>
			<hr>
		</td>
	</tr>
<?php
	} 
}
?>
</table>

