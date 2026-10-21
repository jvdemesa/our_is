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
  height: 65px;
  width: 50%;
  background-color: none;
  /*border-bottom: 1px solid;*/
  display: block;
  margin-left: auto;
  margin-right: auto;
  text-align:center;
}

.div_sig_display2 img {
	margin-left: auto;
  margin-right: auto;
  display: block;
  align-content: center;
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
    page-break-inside: avoid;
}
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
  height: 18cm !important;
	padding-left: 30px !important;
	padding-right: 30px !important;
	/*border: 1px solid;*/
}
.img_display{
	width: 600px;
}
.h3{
	font-size: 16pt;
	font-weight: bold;
}
</style>
<span id="barcode"><img src="<?= $img ?>"></span>
<?php
echo "Created By: <b>".$doc_details->first_track->fullname."</b><br>";
echo "Document Type: <b>".$doc_details->first_track->doc_type."</b><br>";
echo "Sub Type: <b>".$doc_details->first_track->sub_type."</b><hr>";
foreach($doc_details->details as $val){
	echo $val->fieldname.":<b>".$val->value."</b><br>";
}
?>