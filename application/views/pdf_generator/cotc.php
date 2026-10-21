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
.width100px {
  height: 17px;
  width: 100px;
  background-color: white;
  border-bottom: 1px solid;
  display: inline-block;
  text-align: center;
}
.width150px {
  height: 17px;
  width: 150px;
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

.width290px_nl {
  height: 17px;
  width: 290px;
  background-color: white;
  display: inline-block;
  text-align: center;
}
.width310px_nl {
  height: 17px;
  width: 310px;
  background-color: white;
  display: inline-block;
  text-align: center;
}
.width330px_nl {
  height: 17px;
  width: 330px;
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
#checkboxes label {
  float: left;
}
#checkboxes ul {
  margin: 0;
  list-style: none;
  float: left;
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

<?php 
echo ($doc_details->condition_no == 1)?".selected1{background-color: black;}":"";
echo ($doc_details->condition_no == 2)?".selected2{background-color: black;}":"";
echo ($doc_details->condition_no == 3)?".selected3{background-color: black;}":"";
echo ($doc_details->condition_no == 4)?".selected4{background-color: black;}":"";
?>

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
				<h3>CERTIFICATION OF TRAVEL COMPLETED</h3>
			</center>
		</td>
	</tr>
</table>
<br>
<br>
<b>
	Entity Name: <span class="width200px"><?= $doc_details->entity_name ?></span>
	<span class="space"></span>
	Fund Cluster: <span class="width200px"><?= $doc_details->fund_cluster ?></span>
	<br>
	<span class="width350px_nl">
		<span class="sig_span">
		</span>
	</span>
	<br>
	<br>
	<br>
	<span class="width350px">
			<?= $doc_details->dir_sig->fullname ?>
		</span>
	<span class="space"></span>
	<span class="space"></span>
	<span class="width200px"><?= $doc_details->official_station ?></span>
	<br>
	<span class="width350px_nl"><?= $doc_details->dir_sig->designation ?></span>
	<span class="space"></span>
	<span class="space"></span>
	<span class="width200px_nl">Station</span>
</b>
	<br>
	<br>
	<span class="space"></span>I HEREBY CERTIFY THAT I have completed the travel as authorized in the Travel Order/Itinerary of Travel No. <span class="width70px"></span>dated <span class="width70px"></span> under conditions indicated below:
	<br>
	<br>
	<div id="checkboxes">
		<ul>
			<li><span class="square selected1"></span> Strictly in accordance with the approved itinerary.</li>
			<li><span class="square selected2"></span> Cut short as explained below.  Excess payment in the amount of P
				<span class="width70px"><?= $doc_details->amount_cotc ?></span> 
				was refunded under <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O. R. No. 
				<span class="width100px"><?= $doc_details->or_no ?></span> dated 
				<span class="width150px"><?= ($doc_details->date!=null)?date("F d, Y", strtotime($doc_details->date)):""; ?></span>
			</li>
			<li><span class="square selected3"></span>Extended as explained below, additional itinerary was submitted</li>
			<li><span class="square selected4"></span>Other deviation as explained below.</li>
		</ul>
	</div>
	<br>
	<br>
	Explanation or justifications:<br>
	<span class="width700px"><?= $doc_details->explanation; ?></span>
	Evidence of travel: <br>
	<span class="width700px"><?= $doc_details->evidence; ?></span><br>
	<span class="width700px"></span><br>
	<span class="width700px"></span><br><br><br>

	<span class="width350px_nl"></span>
	<span class="width200px_nl">Respectfully submitted:</span>

	<span class="width350px_nl"></span>
	<span class="width310px_nl">
		<span class="sig_span">
		<?php
		echo '<img src="data:image/'.$doc_details->claimnant_sig_type.';base64,'.base64_encode($doc_details->claimnant_sig).'" class="img_sig">';
		?>
		</span>
	</span>
	<br>
	<br>
	<br>
	<span class="width350px_nl"></span>
	<span class="width310px_nl"><b><?= strtoupper($doc_details->name) ?></b></span>

	<br>
	<span class="width330px_nl"></span>
	<span class="width310px_nl"><i><?= $doc_details->position ?></i></span>
	<br>
	<br>
	<span class="space"></span><span class="space"></span>On evidence and information of which I have the knowledge, the travel was actually undertaken.
	<br><br><br>
	<span class="width350px_nl"></span>
	<span class="width200px_nl">Approved:</span>

	<span class="width330px_nl"></span>
	<span class="width310px_nl">
		<span class="sig_span">
		<?php
		if($doc_details->div_sig){
		$div_sig_class = ($doc_details->div_sig->is_signed==0)?"hideme":"";
			echo '<img src="data:image/'.$doc_details->div_sig->img_type.';base64,'.base64_encode($doc_details->div_sig->signature).'" class="img_sig '.$div_sig_class.'">';
		}
		?>
		</span>
	</span>
	<br>
	<br>
	<br>

	<span class="width330px_nl"></span>
	<span class="width310px_nl"><b><?= strtoupper($doc_details->div_sig->fullname) ?></b></span>
	<br>
	<span class="width330px_nl"></span>
	<span class="width310px_nl">
	<?php
		echo $doc_details->div_sig->sig_designation;
	 	if(!in_array($doc_details->div_sig->designation_id,[1,2])){
	    echo ", ";
			echo $doc_details->div_sig->designation_id==6?$doc_details->div_sig->sig_unit:$doc_details->div_sig->sig_div; 
		}
	?>
	<?php
	if($doc_details->div_sig->orig_uid != 0 && $doc_details->div_sig->is_signed == 1){
		echo "<br>In-Charge - ".$doc_details->div_sig->orig_divabv;
	}
	?>
	</span>
