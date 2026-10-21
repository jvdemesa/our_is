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
#aa_sig{
	position: absolute;
	top: 710px;
	left: 165px;
}

.pull-right {
    float: right;
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
.small-font{
	font-size: 8pt;
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
.img_display{
	width: 140px;
	height: 70px;
	position: absolute;
	top: -20px;
	left: 90px;
}
#header{
	border-bottom: 3px solid green !important;
}
.justify {
  text-align: justify;
  text-justify: inter-word;
  font-size: 12pt;
}
.page_size{
  height: 18cm !important;
	padding-left: 30px !important;
	padding-right: 30px !important;
	/*border: 1px solid;*/
}
.footer{
  position: absolute;
  bottom: 50;
  text-align: center;
  font-size: 9pt;
  width: 100%;
}

</style>
<?php
$aa_sig_class = "";
foreach($signature_status as $val){
	//accountant signatory
	if($val->signatory_type == 5 && $val->is_signed == 0){
		$aa_sig_class = "hideme";
	}
}

?>
<div class="page_size">
	<span id="barcode"><img src="<?= $img ?>"></span>
	<table class="table" width="100%" style="border:none;" id="header">
		<tr class="no_border">
			<td class="no_border">
				<center>
					<span><img src="<?= $erdb_logo ?>" class="img_display"></span>

					<small class="small-font">
						Republic of the Philippines<br>
						Department of Environment and Natural Resources<br>
						Ecosystems Research and Development Bureau<br>
						College, Laguna<br>
					</small>
					<br>
				</center>
			</td>
		</tr>
	</table>
	<br>
	<br>
	<center><u><h3>CERTIFICATE OF NO CLAIM</h3></u></center>
	<br>
	<br>
	<div class="justify">
		<span style="padding-left:3em">&nbsp;</span>This is to certify that 
		<span style="font-weight: bold;"><?= $doc_details->name ?></span>, 
		<?= $doc_details->claimnant_pos ?>, has no claim from this office in regards to his / her travelling expenses (TEV) dated 
		<?= $doc_details->dot ?> relative to his / her
		<?= $doc_details->purpose ?>, per Travel Order no.
		<?= ($to_no)?$to_no[0]->approved_number:""; ?> 
	</div>
	<br>
	<br>
	<div class="justify">
		<span style="padding-left:3em">&nbsp;</span>This certification is issued in line with his / her claim for travelling expenses from 
		<?= $doc_details->tef ?>.
	</div>
	<br>
	<br>
	<div class="justify">
		<?php
		if($doc_details->date_given!=null){
		$date = explode(" ",date('jS F Y', strtotime($doc_details->date_given)));
		?>
		<span style="padding-left:3em">&nbsp;</span>Given this <?= $date[0] ?> day of <?= $date[1]." ".$date[2] ?> at ERDB, Los Baños Laguna.
	<?php }else{ ?>
		<span style="padding-left:3em">&nbsp;</span>Given this _______ day of _____________ at ERDB, Los Baños, Laguna.	
	<?php } ?>
	</div>
	<br>
	<br>
	<br>
	<span class="width350px_nl"></span>
	<span class="space"></span>
	<span class="width200px_nl">
		<span class="sig_span">
			<?php echo '<img src="data:image/'.$doc_details->aa_sig->img_type.';base64,'.base64_encode($doc_details->aa_sig->signature).'" class="img_sig '.$aa_sig_class.'">';
			?>
		</span>
	</span>
	<br>
	<br>
	<br>
	<span class="width350px_nl"></span>
	<span class="space"></span>
	<span class="width200px_nl"><b><?= strtoupper($doc_details->aa_sig->fullname) ?></b></span>
	<br>
	<span class="width350px_nl"></span>
	<span class="space"></span>
	<span class="width200px_nl"><i>
	<?= $doc_details->aa_sig->designation.", " ?>
	<?= $doc_details->aa_sig->designation_id==6?$doc_details->aa_sig->unit_abv:$doc_details->aa_sig->divunit_abv; ?></i>
	</span>
</div>
<div class="footer">
College, 4031, Laguna, Philippines<br>
Tel Nos: (6349)536-3628; 536-2229; 536-2269 Fac No.(6349)536-2850<br>
E-mail: erdb@denr.gov.ph
</div>