<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf_generator extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Submit_document_model', '', TRUE);
		$this->load->model('Pdf_generator_model', '', TRUE);
	}

  	public function travel_order(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/travel_order';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$detail = $this->Viewdoc_model->get_doc_detail($dtsno);
		$doc_details = new stdClass();
		foreach($detail as $val){
		    switch ($val->unique_case_no) { 
				case '1':
			    $user = $this->User_information_model->get_user_details($val->value);
				$doc_details->name = strtoupper($user->fullname);
				$doc_details->position = strtoupper($user->position);
				$doc_details->div = strtoupper($user->div_abv." | ".$user->unit_abv);
				$doc_details->user_sig = $user->signature;
				$doc_details->user_img_type = $user->img_type;
				$doc_details->official_station =$user->official_station;
				break;
				case '2':
				$value = explode(" - ",$val->value);
				if($value[0] == $value[1]){
				$doc_details->duration = $value[0];
				}else{
				$doc_details->duration = $val->value;
				}
				break;
				case '3':
				$doc_details->destination = $val->value;
				break;
				case '4':
				$doc_details->purpose = $val->value;
				break;
				case '5':
				$doc_details->per_diems = $val->value;
				break;
				case '6':
				$doc_details->assistants = $val->value;
				break;
				case '7':
				$doc_details->remarks = $val->value;
				break;
				case '8':
				$where = new stdClass();
				$where->id =  $val->value;
				$rc = $this->General_model->list_dynamic("rc",$where);
				$doc_details->rc = $rc[0]->description;
				break;
				case '9':
				$this->data['i_sig'] = $this->Admin_model->get_signature($val->value,$dtsno);
				break;
				case '10':
					if($val->value!=""){
						$this->data['ra_sig'] = $this->Admin_model->get_signature($val->value,$dtsno);
					}else{
						$this->data['ra_sig'] = false;
					}
				break;
				case '11':
				$this->data['approval_sig'] = $this->Admin_model->get_signature($val->value,$dtsno);
				break;
			    default:
			}
		}
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$cs = $this->General_model->list_dynamic('concerned_signatories',$where);
		$this->data['i_sig'] = false;
		$this->data['ra_sig'] = false;
		$this->data['approval_sig'] = false;
		foreach($cs as $val){
			switch($val->ads_id){
				case "3":
					$this->data['i_sig'] = $this->Admin_model->get_signature($val->id,$dtsno);
				break;
				case "4":
					$this->data['ra_sig'] = $this->Admin_model->get_signature($val->id,$dtsno);
				break;
				case "5":
					$this->data['approval_sig'] = $this->Admin_model->get_signature($val->id,$dtsno);
				break;
			}
		}
		$this->data['doc_details'] = $doc_details;
		$where->doc_type_id = 5;
		$this->data['to_number'] = $this->General_model->list_dynamic('approved_document_number',$where);
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."travel_order-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."travel_order-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("travel_order-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function dv(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/dv';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$detail = $this->Viewdoc_model->get_doc_detail($dtsno);
		$doc_details = new stdClass();
		foreach($detail as $val){
		    switch ($val->unique_case_no) { 
				case '13':
				$doc_details->date = $val->value;
				break;
				case '14':
		 		$user = $this->Submit_document_model->get_employee_details($val->value);
				$doc_details->payee = strtoupper($user->fullname);
				$div_id = $user->division;
				$doc_details->address = $user->address;
				break;
				case '16':
				$doc_details->details = $val->value;
				break;
				case '17':
				$doc_details->amount = $val->value;
				break;
				case '18':
				$doc_details->charged_to = $val->value;
				break;
			    default:
			}
		}
		$concerned_signatories = $this->Admin_model->search_list("concerned_signatories",$dtsno,'dtsno');
		$this->data['signature_status'] = $concerned_signatories;
		foreach($concerned_signatories as $val){
			if($user->is_region==1){//if region
				if(in_array( $user->designation ,[8,6])){//if staff & unit Head
					if($val->signatory_type == 4){//get Center head
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 5){
						$doc_details->aa = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 2){
						$doc_details->ad = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if(in_array( $user->designation ,[4])){//if center head
					if($val->signatory_type == 2){//get AD
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 5){//get accountant
						$doc_details->aa = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 1){
						$doc_details->ad = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
			}

			if($user->is_region==0){//if ERDB MAIN
				if(in_array( $user->designation ,[8,7,6,5])){//if staff & unit Head
					if($user->division == 1){
						if($user->unit == 14){
							if($val->signatory_type == 5){
								$doc_details->aa = $this->Admin_model->get_signature($val->id,$dtsno);
							}
							if($val->signatory_type == 1){
								$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
								$doc_details->ad = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}else{
							if($val->signatory_type == 2){//get AD
								$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
							}
							if($val->signatory_type == 5){
								$doc_details->aa = $this->Admin_model->get_signature($val->id,$dtsno);
							}
							if($val->signatory_type == 1){
								$doc_details->ad = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}
					}else{
						if($val->signatory_type == 3){//get Division Chief
							$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
						}
						if($val->signatory_type == 5){
							$doc_details->aa = $this->Admin_model->get_signature($val->id,$dtsno);
						}
						if($val->signatory_type == 2){
							$doc_details->ad = $this->Admin_model->get_signature($val->id,$dtsno);
						}
					}
				}
				if(in_array( $user->designation ,[3])){//if divison Chief
					if($val->signatory_type == 2){//get AD
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 5){//get accountant
						$doc_details->aa = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 1){
						$doc_details->ad = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if(in_array( $user->designation ,[2,1])){//if AD & D
					if($val->signatory_type == 2){//get D
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 5){//get accountant
						$doc_details->aa = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 1){
						$doc_details->ad = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
			}	
		}
		if(!isset($doc_details->certifier)){
			$doc_details->certifier = $doc_details->ad;
		}
		// //2 designation id of assistant director
		// $doc_details->ad = $this->Admin_model->get_sig_base_on_designation('2');
		// //5 designation id of accountant
		// $doc_details->aa = $this->Admin_model->get_sig_base_on_designation('5');
		//$doc_details->ad = $this->Admin_model->get_div_signatory('');
		$this->data['doc_details'] = $doc_details;
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$this->data['ae'] = $this->Viewdoc_model->get_accounting_entry($dtsno);
		$this->data['obr_main'] = $this->General_model->list_dynamic("obr_main",$where);
		$this->data['ors_burs'] = $this->General_model->list_dynamic("ors_burs",$where);
		$where->doc_type_id = 2;
		$this->data['dv_no'] = $this->General_model->list_dynamic("approved_document_number",$where);
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."dv-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."dv-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("dv-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function fixed_dv(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/fixed_dv';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$detail = $this->Viewdoc_model->get_doc_detail($dtsno);
		$doc_details = new stdClass();
		foreach($detail as $val){
		    switch ($val->fieldname_id) { 
				case '368':
				$doc_details->date = $val->value;
				break;
				case '374':
				$doc_details->payee = strtoupper( $val->value);
				break;
				case '365':
				$doc_details->details = $val->value;
				break;
				case '367':
				$doc_details->amount = $val->value;
				break;
				case '366':
				$doc_details->charged_to = $val->value;
				break;
			    default:
			}
		}
		$concerned_signatories = $this->Admin_model->search_list("concerned_signatories",$dtsno,'dtsno');
		$this->data['signature_status'] = $concerned_signatories;
		foreach($concerned_signatories as $key=>$val){
			switch($key){
				case "0":
					$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
				break;
				case '1':
					$doc_details->aa = $this->Admin_model->get_signature($val->id,$dtsno);
				break;
				case '2':
					$doc_details->ad = $this->Admin_model->get_signature($val->id,$dtsno);
				break;
			}
		}
		$this->data['doc_details'] = $doc_details;
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$this->data['ae'] = $this->Viewdoc_model->get_accounting_entry($dtsno);
		$this->data['obr_main'] = $this->General_model->list_dynamic("obr_main",$where);
		$this->data['ors_burs'] = $this->General_model->list_dynamic("ors_burs",$where);
		$where->doc_type_id = 130;
		$this->data['dv_no'] = $this->General_model->list_dynamic("approved_document_number",$where);
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."dv-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."dv-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("dv-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function obr(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/obr';
		$this->data['img'] = $this->input->post('img');
		$doc_details = new stdClass();
		$dtsno = $this->input->post('dtsno');
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$detail = $this->General_model->list_dynamic('obr_main',$where)[0];
 		$user = $this->Submit_document_model->get_employee_details($detail->uid);
		$doc_details->address = $user->address;
		$doc_details->payee = strtoupper($user->fullname);
		$doc_details->particulars = $detail->particulars;
		$doc_details->funding_source = $detail->funding_source;
		$doc_details->funding_source_code = $detail->funding_source_code;
		$doc_details->date = $detail->date;
		$concerned_signatories = $this->General_model->list_dynamic("concerned_signatories",$where);

		// $this->data['signature_status'] = $concerned_signatories;
		$this->data['obr_details'] = $this->General_model->list_dynamic("obr",$where);
		foreach($concerned_signatories as $val){
			if($val->signatory_type == 8){
				$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
			}	
			if($val->signatory_type == 7){
				$doc_details->ab = $this->Admin_model->get_signature($val->id,$dtsno);
			}	
		}
		$doc_details->div_name = $doc_details->certifier->div_name;
		$doc_details->unit_abv = $doc_details->certifier->unit_abv;
		$doc_details->designation_id = $doc_details->certifier->designation_id;
		//6 designation id of Budget officer
		$this->data['doc_details'] = $doc_details;
		$this->data['ors'] = $this->General_model->list_dynamic("ors_burs",$where);
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."obr-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."obr-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("obr-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function ors(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/ors';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$detail = $this->General_model->list_dynamic('obr_main',$where)[0];
 		$user = $this->Submit_document_model->get_employee_details($detail->uid);
		$doc_details = new stdClass();
		$doc_details->payee = strtoupper($user->fullname);
		$doc_details->particulars = $detail->particulars;
		$doc_details->funding_source = $detail->funding_source;
		$doc_details->funding_source_code = $detail->funding_source_code;
		$doc_details->address = $user->address;
		$doc_details->date = $detail->date;
		$concerned_signatories = $this->Admin_model->search_list("concerned_signatories",$dtsno,'dtsno');
		$this->data['signature_status'] = $concerned_signatories;
		$this->data['obr_details'] = $this->Admin_model->search_list("obr",$dtsno,'dtsno');
		foreach($concerned_signatories as $val){
			if($val->signatory_type == 8){
				$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
			}	
			if($val->signatory_type == 7){
				$doc_details->ab = $this->Admin_model->get_signature($val->id,$dtsno);
			}	
		}
		$doc_details->div_name = $doc_details->certifier->div_name;
		$doc_details->unit_abv = $doc_details->certifier->unit_abv;
		$doc_details->designation_id = $doc_details->certifier->designation_id;
		//6 designation id of Budget officer
		$this->data['doc_details'] = $doc_details;
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$where->ors_burs = "ORS";
		$ors = $this->General_model->list_dynamic("ors_burs",$where);
		$this->data['ors'] = $ors!=false?$ors[0]:false;
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		if($ors){
			$html = $this->output->get_output();
			$this->load->library('pdf');
			$options = $this->dompdf->getOptions(); 
		    $options->set(array('isHtml5ParserEnabled'=> false,
						    	'isRemoteEnabled' => false, 
						    	'isJavascriptEnabled', true));
		    $this->dompdf->setOptions($options);
			$this->dompdf->loadHtml($html);
			$this->dompdf->setPaper('A4', 'portrait');
			$this->dompdf->render();
			if($todo == 2){
				$serverpath = (FCPATH)."assets/pdf/temp/";
				file_put_contents($serverpath."ors-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."ors-".$dtsno.".pdf", $this->dompdf->output());
			}else{
				$this->dompdf->stream("ors-".$dtsno.".pdf", array("Attachment"=>$todo));
			}
		}
  	}

  	public function burs(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/burs';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$detail = $this->General_model->list_dynamic('obr_main',$where)[0];
 		$user = $this->Submit_document_model->get_employee_details($detail->uid);
		// $this->array_dump($detail);
		// exit();
		$doc_details = new stdClass();
		$doc_details->payee = strtoupper($user->fullname);
		$doc_details->particulars = $detail->particulars;
		$doc_details->funding_source = $detail->funding_source;
		$doc_details->funding_source_code = $detail->funding_source_code;
		$doc_details->address = $user->address;
		$doc_details->date = $detail->date;
		// $doc_details->fund_cluster = $detail->fund_cluster;
		$obr_main = $this->Admin_model->search_list("obr_main",$dtsno,'dtsno');
		if($obr_main){
			$doc_details->fund_cluster = $obr_main[0]->fund_cluster;
		}
		$concerned_signatories = $this->Admin_model->search_list("concerned_signatories",$dtsno,'dtsno');
		$this->data['signature_status'] = $concerned_signatories;
		$this->data['obr_details'] = $this->Admin_model->search_list("obr",$dtsno,'dtsno');
		foreach($concerned_signatories as $val){
			if($val->signatory_type == 8){
				$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
			}	
			if($val->signatory_type == 7){
				$doc_details->ab = $this->Admin_model->get_signature($val->id,$dtsno);
			}	
		}
		$doc_details->div_name = $doc_details->certifier->div_name;
		$doc_details->unit_abv = $doc_details->certifier->unit_abv;
		$doc_details->designation_id = $doc_details->certifier->designation_id;
		//6 designation id of Budget officer
		$this->data['doc_details'] = $doc_details;
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$where->ors_burs = "BURS";
		$ors = $this->General_model->list_dynamic("ors_burs",$where);
		$this->data['ors'] = $ors!=false?$ors[0]:false;
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		if($ors){
			$html = $this->output->get_output();
			$this->load->library('pdf');
			$options = $this->dompdf->getOptions(); 
		    $options->set(array('isHtml5ParserEnabled'=> false,
						    	'isRemoteEnabled' => false, 
						    	'isJavascriptEnabled', true));
		    $this->dompdf->setOptions($options);
			$this->dompdf->loadHtml($html);
			$this->dompdf->setPaper('A4', 'portrait');
			$this->dompdf->render();
			if($todo == 2){
				$serverpath = (FCPATH)."assets/pdf/temp/";
				file_put_contents($serverpath."burs-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."burs-".$dtsno.".pdf", $this->dompdf->output());
			}else{
				$this->dompdf->stream("burs-".$dtsno.".pdf", array("Attachment"=>$todo));
			}
		}
  	}

  	public function iot(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/iot';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$doc_details = new stdClass();
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$detail = $this->General_model->list_dynamic("iot_main",$where)[0];
 		$user = $this->Submit_document_model->get_employee_details($detail->uid);
		$name = $this->General_model->get_user_details($detail->uid);
		$doc_details->entity_name = strtoupper($detail->entity_name);
		// $doc_details->fund_cluster = $detail->fund_cluster;
		$doc_details->name = $user->fullname;
		$doc_details->claimnant_sig = $name->signature;
		$doc_details->claimnant_sig_type = $name->img_type;
		$doc_details->official_station = $user->official_station;
		$doc_details->travel_duration = $detail->travel_duration;
		$doc_details->position = $name->position;
		$doc_details->purpose = $detail->purpose;
		$doc_details->iot_no = $detail->iot_no;
		$concerned_signatories = $this->Admin_model->search_list("concerned_signatories",$dtsno,'dtsno');
		$this->data['signature_status'] = $concerned_signatories;
		$this->data['iot_details'] = $this->Admin_model->search_list("iot",$dtsno,'dtsno');

		$where_linked = new stdClass();
		$where_linked->linked_to = $dtsno;
		$linked_doc_list = $this->General_model->list_dynamic('linked_documents',$where_linked);	
		if($linked_doc_list){
			$linked_doc = $linked_doc_list[0];
		
			$where_first_track = new stdClass();
			$where_first_track->dtsno = $linked_doc->linked_dtsno;
			$first_track = $this->General_model->list_dynamic('first_track',$where_first_track)[0];
			if($first_track->sub_type == 80){
				$obr_main = $this->Admin_model->search_list("obr_main",$linked_doc->linked_dtsno,'dtsno');
			}else{
				$obr_main = $this->Admin_model->search_list("obr_main",$dtsno,'dtsno');
			}
		}else{
			$obr_main = $this->Admin_model->search_list("obr_main",$dtsno,'dtsno');
		}
		if($obr_main){
			$doc_details->fund_cluster = $obr_main[0]->fund_cluster;
		}
		$doc_details->div_sig = false;
		$doc_details->ad_sig = false;
		foreach($concerned_signatories as $val){
			if($user->is_region==1){//if region
				if($user->designation==8){//if staff
					if($val->signatory_type == 6){//get unit head as supervisor
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}	
					if($val->signatory_type == 4){//get center head as approver
						$doc_details->ad_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
	
				}
				if($user->designation==6){//if unit head
					if($val->signatory_type == 4){//get center head for 2 signatory
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
						$doc_details->ad_sig = $doc_details->div_sig;
					}	
				}
				if($user->designation==4){//if center head
					if($val->signatory_type == 2){//get unit head as supervisor
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}	
					if($val->signatory_type == 1){//get center head as approver
						$doc_details->ad_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}	
				}
			}
			if($user->is_region==0){//if ERDB Main
				if(in_array( $user->designation ,[8,7,6,5])){//if staff & unit Head
					if($user->division == 1){//if user in OD OR AD
						if($user->unit == 14){//if user in OD
							if($val->signatory_type == 1){//get AD as approver
								$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
								$doc_details->ad_sig = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}else{//IF user in other unit
							if($val->signatory_type == 2){//get div chief as supervisor
								$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
							}
							if($val->signatory_type == 1){//get AD as approver	
								$doc_details->ad_sig = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}

					}else{
						if($val->signatory_type == 3){//get div chief as supervisor
							$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
						}
						if($val->signatory_type == 2){//get AD as approver	
							$doc_details->ad_sig = $this->Admin_model->get_signature($val->id,$dtsno);
						}
					}
				}
				if($user->designation==3){//if divison chief
					if($val->signatory_type == 2){//get AD as supervisor
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}	
					if($val->signatory_type == 1){//get D as approver
						$doc_details->ad_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}	
				}
				if($user->designation==2 || $user->designation==1 ){//if AD or D
					if($val->signatory_type == 2){//get D as signatories
						$doc_details->div_sig =  $this->Admin_model->get_signature($val->id,$dtsno);
					}	
					if($val->signatory_type == 1){//get D as signatories
						$doc_details->ad_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}	
				}
			}
		}
		if(!isset($doc_details->div_sig)){
			$doc_details->div_sig = $doc_details->ad_sig;
		}
		// $doc_details->div_name = $doc_details->certifier->div_name;
		// $doc_details->unit_abv = $doc_details->certifier->unit_abv;
		// $doc_details->designation_id = $doc_details->certifier->designation_id;
		$this->data['doc_details'] = $doc_details;
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."iot-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."iot-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("iot-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function cotc(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/cotc';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$detail = $this->General_model->list_dynamic("iot_main",$where)[0];
		//get user detail
 		$user = $this->Submit_document_model->get_employee_details($detail->uid);
		$detail_cotc = $this->General_model->list_dynamic("cotc",$where)[0];
		$name = $this->General_model->get_user_details($detail->uid);
		$doc_details = new stdClass();
		$doc_details->entity_name = strtoupper($detail->entity_name);
		$doc_details->name = $user->fullname;
		$doc_details->claimnant_sig = $name->signature;
		$doc_details->claimnant_sig_type = $name->img_type;
		$doc_details->official_station = $user->official_station;
		$doc_details->travel_duration = $detail->travel_duration;
		$doc_details->position = $name->position;
		$doc_details->condition_no = $detail_cotc->condition_no;
		$doc_details->explanation = $detail_cotc->explanation;
		$doc_details->evidence = $detail_cotc->evidence;
		$doc_details->amount_cotc = $detail_cotc->amount;
		$doc_details->or_no = $detail_cotc->or_no;
		$doc_details->date = $detail_cotc->date!="0000-00-00"?$detail_cotc->date:"";

		$concerned_signatories = $this->Admin_model->search_list("concerned_signatories",$dtsno,'dtsno');
		$where_linked = new stdClass();
		$where_linked->linked_to = $dtsno;
		$linked_doc_list = $this->General_model->list_dynamic('linked_documents',$where_linked);	
		if($linked_doc_list){
			$linked_doc = $linked_doc_list[0];
		
			$where_first_track = new stdClass();
			$where_first_track->dtsno = $linked_doc->linked_dtsno;
			$first_track = $this->General_model->list_dynamic('first_track',$where_first_track)[0];
			if($first_track->sub_type == 80){
				$obr_main = $this->Admin_model->search_list("obr_main",$linked_doc->linked_dtsno,'dtsno');
			}else{
				$obr_main = $this->Admin_model->search_list("obr_main",$dtsno,'dtsno');
			}
		}else{
			$obr_main = $this->Admin_model->search_list("obr_main",$dtsno,'dtsno');
		}

		if($obr_main){
			$doc_details->fund_cluster = $obr_main[0]->fund_cluster;
		}
		$this->data['signature_status'] = $concerned_signatories;
		//Get the Director
		$doc_details->dir_sig = $this->Admin_model->get_sig_base_on_designation(1);
		foreach($concerned_signatories as $val){
			if($user->is_region==1){//if region
				if($user->designation==6){//if unit Head
					if($val->signatory_type == 4){//get center head	
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if($user->designation==8 && $user->is_oc == 0){//if staff
					if($val->signatory_type == 6){//get unit head	
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if($user->designation==8 && $user->is_oc == 1){//if staff
					if($val->signatory_type == 4){//get center head	
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if($user->designation==4){
					if($val->signatory_type == 2){//get center head	
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
			}
			if($user->is_region==0){//if ERDB Main
				if(in_array( $user->designation ,[8,7,6,5])){//if staff & unit Head & budget & accountant
					if($user->division == 1){
						if($user->unit == 14){
							if($val->signatory_type == 1){//get Division chief	
								$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}else{
							if($val->signatory_type == 2){//get Division chief	
								$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}
					}else{
						if($val->signatory_type == 3){//get Division chief	
							$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
						}
					}
				}
				if(in_array( $user->designation ,[3])){//if division chief
					if($val->signatory_type == 2){//get center head	
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if(in_array( $user->designation ,[2,1])){//if D & AD
					if($val->signatory_type == 2){//get center head	
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 1){//get center head	
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
			}
		}
		//if div sig is still not set
		if(!isset($doc_details->div_sig) || $doc_details->div_sig == false){
			foreach($concerned_signatories as $val){
				if(in_array($val->signatory_type,[4,1])){//get or div chief
					//get center head or div chief
					$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
				}
			}
		}
		$this->data['doc_details'] = $doc_details;
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		//write temporary file to server
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."cotc-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."cotc-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("cotc-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function travel_report(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/travel_report';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$detail = $this->General_model->list_dynamic("travel_report",$where);
		$name = $this->General_model->get_user_details($detail[0]->uid);
 		$user = $this->Submit_document_model->get_employee_details($detail[0]->uid);
		$doc_details = new stdClass();
		$doc_details->name = $user->fullname;
		$doc_details->claimnant_sig = $name->signature;
		$doc_details->claimnant_sig_type = $name->img_type;
		$doc_details->position = $name->position;
		$this->data['travel_report'] = $detail;
		$concerned_signatories = $this->Admin_model->search_list("concerned_signatories",$dtsno,'dtsno');
		$this->data['signature_status'] = $concerned_signatories;
		foreach($concerned_signatories as $val){
			if($user->is_region==1){//if region
				if(in_array( $user->designation ,[8,6])){//if staff & unit Head
					if($val->signatory_type == 4){//get center head				
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if(in_array( $user->designation ,[4])){//if center head
					if($val->signatory_type == 2){//get AD				
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
			}	
			if($user->is_region==0){//if ERDB Main
				if(in_array( $user->designation ,[8,7,6,5])){//if staff & unit Head & budget & accountant
					if($user->division == 1){
						if($user->unit == 14){// if payee is in OD OC
							if($val->signatory_type == 1){//get OD	
								$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}else{// if payee is not in OD
							if($val->signatory_type == 2){//get OD AD	
								$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}
					}else{//if Other division
						if($val->signatory_type == 3){//get Division chief	
							$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
						}
					}
				}
				if(in_array( $user->designation ,[3])){//if division chief
					if($val->signatory_type == 2){//get AD				
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if(in_array( $user->designation ,[2,1])){//if AD & D
					if($val->signatory_type == 2){//get D				
						$doc_details->div_sig = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if($val->signatory_type == 1){//get D				
						$doc_details->div_sig_d = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
			}
		}
		if(!isset($doc_details->div_sig)){
			$doc_details->div_sig = $doc_details->div_sig_d;
		}
		$this->data['doc_details'] = $doc_details;
		foreach($detail as $val){
			if($val->attached_file!=""){
				$path = base_url().'assets/uploads/travel_report/'.$val->attached_file;
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$data = $this->get_img($path);
				$base_64[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
				$caption[] = $val->caption;
			}
			if($val->attached_file2!=""){
				$path = base_url().'assets/uploads/travel_report/'.$val->attached_file2;
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$data = $this->get_img($path);
				$base_64[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
				$caption[] = $val->caption2;
			}
		}
		$this->data['appendixa'] = isset($base_64)?$base_64:false;
		$this->data['caption'] = isset($caption)?$caption:false;
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$this->load->view('template/print_head', $this->data);
		
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', false));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."travel_report-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."travel_report-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("travel_report-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function conc(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/conc';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$detail = $this->General_model->list_dynamic("conc",$where)[0];
		$where_linked = new stdClass();
		$where_linked->linked_to = $dtsno;
		$to_no = $this->General_model->list_dynamic("linked_documents",$where_linked)[0];
		$where_cs = new stdClass();
		$where_cs->dtsno = $dtsno;
		$where_cs->signatory_type = 5;
		$date_signed = $this->General_model->list_dynamic("concerned_signatories",$where_cs)[0]->date_signed;

		$name = $this->General_model->get_user_details($detail->uid);
 		$user = $this->Submit_document_model->get_employee_details($detail->uid);

		$doc_details = new stdClass();
		$doc_details->name = $user->fullname;
		$doc_details->claimnant_sig = $name->signature;
		$doc_details->claimnant_sig_type = $name->img_type;
		$doc_details->claimnant_pos = $name->position;
		$doc_details->dot = $detail->dot;
		$doc_details->purpose = $detail->purpose;
		$doc_details->to_no = $to_no->linked_dtsno;
		$doc_details->tef = $detail->tef;
		$doc_details->date_given = $date_signed;

		$concerned_signatories = $this->Admin_model->search_list("concerned_signatories",$dtsno,'dtsno');
		$this->data['signature_status'] = $concerned_signatories;
		foreach($concerned_signatories as $val){
			if($val->signatory_type == 5){
				$doc_details->aa_sig = $this->Admin_model->get_signature($val->id,$dtsno);
			}	
		}
		$path = base_url().'assets/images/erdb.jpg';
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = $this->get_img($path);
		$this->data['erdb_logo'] = 'data:image/' . $type . ';base64,' . base64_encode($data);

		$this->data['doc_details'] = $doc_details;
		$where_to_no = new stdClass();
		$where_to_no->doc_type_id = 5;
		$where_to_no->dtsno = $to_no->linked_dtsno;
		$this->data['to_no'] = $this->General_model->list_dynamic("approved_document_number",$where_to_no);
			//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$this->load->view('template/print_head', $this->data);
		
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."conc-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."conc-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("conc-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function mergepdf_fpdi(){

  		require_once("application/libraries/fpdf/fpdf.php");
  		require_once("application/libraries/fpdi/src/autoload.php");
		$pdf = new \setasign\Fpdi\Fpdi();
		$files = [(FCPATH)."assets/pdf/temp/DV-".$dtsno.".pdf", (FCPATH)."assets/pdf/temp/OBR-".$dtsno.".pdf", (FCPATH)."assets/pdf/temp/IOT-".$dtsno.".pdf", (FCPATH)."assets/pdf/temp/COTC-".$dtsno.".pdf", (FCPATH)."assets/pdf/temp/TR-".$dtsno.".pdf"];

		// iterate over array of files and merge
		foreach ($files as $file) {
		    $pageCount = $pdf->setSourceFile((FCPATH)."assets/pdf/tr1.pdf");
		    for ($i = 0; $i < $pageCount; $i++) {
		        $tpl = $pdf->importPage($i + 1, '/MediaBox');
		        $pdf->addPage();
		        $pdf->useTemplate($tpl);
		    }
		}

		// output the pdf as a file (http://www.fpdf.org/en/doc/output.htm)
			
		$pdf->Output('I', 'concat.pdf');
  	}

  	public function ca_liquidation(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/ca_liquidation';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		//get ca_liquidation details
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$detail = $this->General_model->list_dynamic('ca_liquidation',$where)[0];
 		$user = $this->Submit_document_model->get_employee_details($detail->uid);
		//get linked documents
		$where_linked = new stdClass();
		$where_linked->linked_to = $dtsno;
		$linked_doc = $this->General_model->list_dynamic('linked_documents',$where_linked)[0];
		//get linked documents obr details
		$where_obr = new stdClass();
		$where_obr->dtsno = $linked_doc->linked_dtsno;
		$obr_main = $this->General_model->list_dynamic('obr_main',$where_obr)[0];
		$obr = $this->General_model->list_dynamic('obr',$where_obr)[0];
		$ca = $this->General_model->list_dynamic('cash_advance',$where_obr)[0];
 		//get linked dv dv_no
		$where_dv = new stdClass();
		$where_obr->dtsno = $linked_doc->linked_dtsno;
		$where_dv->doc_type_id = 2;
		$dv = $this->General_model->list_dynamic('approved_document_number',$where_dv)[0];
		// $this->array_dump($detail);
		// exit();
		$refund = $ca->amount - $detail->amount;
		$reimburse = $detail->amount - $ca->amount;
		$doc_details = new stdClass();
		$doc_details->payee = strtoupper($user->fullname);
		$doc_details->signature= $user->signature;
		$doc_details->img_type= $user->img_type;
		$doc_details->particulars = $detail->particulars;
		$doc_details->serial_no = $detail->serial_no;
		$doc_details->jev_no = $detail->jev_no;
		$doc_details->or_no = $detail->or_no;
		$doc_details->or_date = $detail->or_date;
		$doc_details->date = $detail->date;
		$doc_details->amount = $detail->amount;
		$doc_details->fund_cluster = $obr_main->fund_cluster;
		$doc_details->rc_code = $obr->rc_code;
		$doc_details->dv_no = $dv->approved_number;
		$doc_details->dv_date = $dv->date_approved;
		$doc_details->ca_amount = $ca->amount;
		$doc_details->refund = $refund>0?$refund:"";
		$doc_details->reimburse = $reimburse>0?$reimburse:"";
		$concerned_signatories = $this->Admin_model->search_list("concerned_signatories",$dtsno,'dtsno');
		$this->data['signature_status'] = $concerned_signatories;
		$this->data['obr_details'] = $this->Admin_model->search_list("obr",$dtsno,'dtsno');
		foreach($concerned_signatories as $val){
			if($val->signatory_type == 5){//get AA			
				$doc_details->aa = $this->Admin_model->get_signature($val->id,$dtsno);
			}
			if($user->is_region==1){//if region
				//if staff and not in OCH
				if(in_array( $user->designation ,[8]) && $user->is_oc == 0){
					if($val->signatory_type == 6){//get unit head				
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
					if(!$doc_details->certifier){
						if($val->signatory_type == 4){//get center head				
							$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
						}
					}
				}
				//if staff and in OCH
				if(in_array( $user->designation ,[8]) && $user->is_oc == 1){
					if($val->signatory_type == 4){//get  center head				
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				//if unit head
				if(in_array( $user->designation ,[6])){//if staff & unit Head
					if($val->signatory_type == 4){//get unit head				
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if(in_array( $user->designation ,[4])){//if center head
					if($val->signatory_type == 2){//get AD				
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
			}	
			if($user->is_region==0){//if ERDB Main
				if(in_array( $user->designation ,[8,7,6,5])){//if staff & unit Head & budget & accountant
					if($user->division == 1){
						if($user->unit == 14){// if payee is in OD OC
							if($val->signatory_type == 1){//get OD	
								$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}else{// if payee is not in OD
							if($val->signatory_type == 2){//get OD AD	
								$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
							}
						}
					}else{//if Other division
						if($val->signatory_type == 3){//get Division chief	
							$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
						}
					}
				}
				if(in_array( $user->designation ,[3])){//if division chief
					if($val->signatory_type == 2){//get AD				
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
				if(in_array( $user->designation ,[2,1])){//if AD & D
					if($val->signatory_type == 1){//get D				
						$doc_details->certifier = $this->Admin_model->get_signature($val->id,$dtsno);
					}
				}
			}
		}
		$this->data['doc_details'] = $doc_details;
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$this->load->view('template/print_head', $this->data);
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."ca_liquidation-".$dtsno.".pdf", $this->dompdf->output());
		}elseif($todo == 3){
			$serverpath = (FCPATH)."assets/pdf/approved/";
			file_put_contents($serverpath."ca_liquidation-".$dtsno.".pdf", $this->dompdf->output());
		}else{
			$this->dompdf->stream("ca_liquidation-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function ada(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/ada';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$doc_details = new stdClass();
		//get ca_liquidation details
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$ada = $this->General_model->list_dynamic('ada',$where)[0];
		$doc_details->ada_no = $ada->ada_no;
		$doc_details->adv_no = $ada->adv_no;
		$doc_details->date_added = $ada->date_added;
		$doc_details->mds = $ada->mds;
		$doc_details->nca = $ada->nca;
		//get linked documents
		$where_linked = new stdClass();
		$where_linked->linked_to = $dtsno;
		$where_linked->is_deleted = '0';
		$linked_doc = $this->General_model->list_dynamic('linked_documents',$where_linked);
		$item = array();
		foreach($linked_doc as $linked_val){
			//get linked documents obr details
			$where_dtsno = new stdClass();
			$where_dtsno->dtsno = $linked_val->linked_dtsno;
			$ors_no = $this->General_model->list_dynamic('ors_burs',$where_dtsno)[0]->ors_burs_no;
			$object_code = $this->General_model->list_dynamic('obr',$where_dtsno)[0]->uacs_code;
			$dv_no = $this->General_model->list_dynamic('approved_document_number',$where_dtsno)[0]->approved_number;

			$dv_detail = $this->Viewdoc_model->get_doc_detail($linked_val->linked_dtsno);
			foreach($dv_detail as $val){
			    switch ($val->unique_case_no) { 
					case '13':
					$date = $val->value;
					break;
					case '14':
			 		$user = $this->Submit_document_model->get_employee_details($val->value);
					$payee = strtoupper($user->fullname);
					$div_id = $user->division;
					$address = $user->address;
					$bank_acc_no = $user->bank_acc_no;
					break;
					case '16':
					$dv_details = $val->value;
					break;
					case '17':
					$gross_amount =str_replace(',', '', $val->value);
					break;
					case '18':
					$charged_to = $val->value;
					break;
				    default:
				}
			}

			$where_due_to_bir = new stdClass();
			$where_due_to_bir->dtsno = $linked_val->linked_dtsno;
			$where_due_to_bir->account_title = 1;
			$due_to_bir = $this->General_model->list_dynamic('dv_acc_ent',$where_due_to_bir);
			$withholding = "";
			if($due_to_bir){
				$withholding ="";
				if($due_to_bir[0]->credit!=""){
					$withholding =str_replace(',', '', $due_to_bir[0]->credit);
				}
				if($due_to_bir[0]->debit!=""){
					$withholding =str_replace(',', '', $due_to_bir[0]->debit);
				}
				if($withholding != ""){
					$net = $gross_amount - $withholding;
				}else{
					$net = $gross_amount;
				}
			}else{
				$net = $gross_amount;
			}
			$obj = new stdCLass();
			$obj->ors_no = $ors_no;
			$obj->object_code = $object_code;
			$obj->dv_no = $dv_no;
			$obj->payee = $payee;
			$obj->gross_amount = $gross_amount;
			$obj->withholding = $withholding;
			$obj->net = $net;
			$obj->bank_acc_no = $bank_acc_no;
			$item[] = $obj;
		}
		$obr_main = $this->General_model->list_dynamic('obr_main',$where_dtsno);
		$doc_details->fund_code = $obr_main[0]->funding_source_code;
		// $doc_details->mds = $obr_main[0]->mds;
		// $doc_details->nca = $obr_main[0]->nca;

		$where = new stdClass();
		$where->dtsno = $dtsno;
		$sig = $this->General_model->list_dynamic("concerned_signatories",$where);

		$doc_details->acc = $this->Admin_model->get_sig_base_on_designation(5);	
		$doc_details->cashier = $this->Admin_model->get_sig_base_on_designation(9);
		$doc_details->approver = $sig[0]->fullname;
		$doc_details->position = $sig[0]->position;
		$this->data['doc_details'] = $doc_details;
		$this->data['item'] = $item;
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$this->load->view('template/print_head', $this->data);
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."ada-".$dtsno.".pdf", $this->dompdf->output());
		}elseif($todo == 3){
			$serverpath = (FCPATH)."assets/pdf/approved/";
			file_put_contents($serverpath."ada-".$dtsno.".pdf", $this->dompdf->output());
		}else{
			$this->dompdf->stream("ada-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function jo_mis(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/jo_mis';
		$this->data['img'] = $this->input->post('img');
		$doc_details = new stdClass();
		$dtsno = $this->input->post('dtsno');
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$detail = $this->Pdf_generator_model->get_jo_mis($dtsno);
 		$user = $this->Submit_document_model->get_employee_details($detail->requested_by);
		$doc_details->address = $user->address;
		$doc_details->payee = strtoupper($user->fullname);
		$doc_details->position = strtoupper($user->position);
		$doc_details->div_abv = strtoupper($user->div_abv);
		$doc_details->unit_abv = strtoupper($user->unit_abv);
		$doc_details->assistance_needed = $detail->assistance_needed;
		$doc_details->date = $detail->date;
		$doc_details->diagnosis = $detail->diagnosis;
		$doc_details->recommendation = $detail->recommendation;
		$doc_details->service_performed = $detail->service_performed;
		$doc_details->remarks = $detail->remarks;
		$doc_details->service_by = $detail->service_by;
		$doc_details->service_date = $detail->service_date;
		$doc_details->noted_by_mis = $detail->noted_by;
		$doc_details->noted_date = $detail->noted_date;
		$doc_details->noted_by_img_type = $detail->noted_by_img_type;
		$doc_details->noted_by_sig = $detail->noted_by_sig;
		$doc_details->service_by_img_type = $detail->service_by_img_type;
		$doc_details->service_by_sig = $detail->service_by_sig;
		$concerned_signatories = $this->General_model->list_dynamic("concerned_signatories",$where);

		// $this->data['signature_status'] = $concerned_signatories;
		// $this->data['obr_details'] = $this->General_model->list_dynamic("obr",$where);
		foreach($concerned_signatories as $val){
			if($val->signatory_type == 10){
				$doc_details->approved_by = $this->Admin_model->get_signature($val->id,$dtsno);
			}else{
				$doc_details->noted_by = $this->Admin_model->get_signature($val->id,$dtsno);
			}	
		}
		//6 designation id of Budget officer
		$this->data['doc_details'] = $doc_details;
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."jo_mis-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."jo_mis-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("jo_mis-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function default(){
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('Viewdoc_model', '', TRUE);
		$this->load->model('User_information_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'pdf_generator/default';
		$this->data['img'] = $this->input->post('img');
		$dtsno = $this->input->post('dtsno');
		$where = new stdClass();
		$where->dtsno = $dtsno;
		$details = $this->Viewdoc_model->get_doc_detail($dtsno);
		$first_track = $this->Pdf_generator_model->first_track_details($dtsno)[0];
		$doc_details = new stdClass();
		$doc_details->first_track = $first_track;
		$doc_details->details = $details;
		$this->data['doc_details'] = $doc_details;
		$where->doc_type_id = 5;
		//0 = view || 1 = download
		$todo = $this->input->post('todo');
		$where = new stdClass();
		$where->id = 1;
		$this->load->view('template/print_head', $this->data);
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$options = $this->dompdf->getOptions(); 
	    $options->set(array('isHtml5ParserEnabled'=> false,
					    	'isRemoteEnabled' => false, 
					    	'isJavascriptEnabled', true));
	    $this->dompdf->setOptions($options);
		$this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		if($todo == 2){
			$serverpath = (FCPATH)."assets/pdf/temp/";
			file_put_contents($serverpath."travel_order-".$dtsno.".pdf", $this->dompdf->output());
			}elseif($todo == 3){
				$serverpath = (FCPATH)."assets/pdf/approved/";
				file_put_contents($serverpath."travel_order-".$dtsno.".pdf", $this->dompdf->output());
			}else{
			$this->dompdf->stream("travel_order-".$dtsno.".pdf", array("Attachment"=>$todo));
		}
  	}

  	public function mergepdf_gs(){
		$this->load->model('Viewdoc_model', '', TRUE);
  		$dtsno = $this->input->post("dtsno");
  		$doc_type_id = $this->input->post("doc_type_id");
  		$sub_type_id = $this->input->post("sub_type_id");
  		//for ubuntu follow this https://installati.one/ubuntu/20.04/ghostscript/
  		//for ubuntu https://orangeable.com/php/pdf-merge
  		//merge pdf using ghostscript install ghost script follow this site
  		//The Ghostscript is a command-line library that is not built-in on windows. First, we need to install the Ghostscript from the link https://ghostscript.com/releases/gsdnld.html.
  		//Run the downloaded file and install it. Once the installation is complete, go to the installation directory’s bin folder(C:\Program Files\gs\gs9.55.0\bin) and rename the file gswin64c.exe to gs.exe to use gs as a command.
		//The installation of Ghostscript is complete. The next step is to use PHP to merge pdf files with Ghostscript.
		$linked_docs = $this->Viewdoc_model->get_linked_document_active($dtsno);
		$attached_docs = $this->Viewdoc_model->get_attached_files($dtsno);
		$files = array();
		if($doc_type_id == 2){
			$files[] = "pdf/temp/dv-".$dtsno.".pdf";
			$result = $this->Viewdoc_model->get_docs_tobe_merged($sub_type_id);
		}
		if($doc_type_id == 107){
			$files[] = "pdf/temp/ca_liquidation-".$dtsno.".pdf";
			$result = $this->Viewdoc_model->get_docs_tobe_merged($doc_type_id);
		}
		if($linked_docs){
			foreach($linked_docs as $val){
				if($val->type == 5){//TO
					//check if doc has permanent document
					if (file_exists((FCPATH)."assets/pdf/approved/travel_order-".$val->linked_dtsno.".pdf")) {  
						$files[] = "pdf/approved/travel_order-".$val->linked_dtsno.".pdf";
					}else{
						$files[] = "pdf/temp/travel_order-".$val->linked_dtsno.".pdf";
					}
				}else if($sub_type_id == 116 && $doc_type_id == 2){
					//check if doc has permanent document
					if (file_exists((FCPATH)."assets/pdf/approved/ca_liquidation-".$val->linked_dtsno.".pdf")) {  
						$files[] = "pdf/approved/ca_liquidation-".$val->linked_dtsno.".pdf";
					}else{
						$files[] = "pdf/temp/ca_liquidation-".$val->linked_dtsno.".pdf";
					}
				}else if($val->type == 23){//if linked_doc is SO
					if($val->file2!=""){
						if (file_exists((FCPATH).$val->attached_file2)) {  
							$files[] = trim($val->attached_file2, "assets/");
						}
					}
					if($val->file!=""){
						if (file_exists((FCPATH).$val->attached_file)) {  
							$files[] = trim($val->attached_file, "assets/");
						}
					}
				}else{
					//check if doc has permanent document
					if (file_exists((FCPATH)."assets/pdf/approved/dv-".$val->linked_dtsno.".pdf")) {  
						$files[] = "pdf/approved/dv-".$val->linked_dtsno.".pdf";
					}else{
						$files[] = "pdf/temp/dv-".$val->linked_dtsno.".pdf";
					}
				}
			}
		}
		foreach($result as $val){
			$file = "pdf/temp/{$val->pdf_link}-".$dtsno.".pdf";
			if (file_exists((FCPATH)."assets/".$file)) {  
			$files[] = $file;
			}
		}

		if($attached_docs){
			foreach($attached_docs as $val){
				if($val->filename!=""){
					$attached_file = explode("/", $val->file);
					$files[] = $attached_file[1]."/".$attached_file[2]."/".$attached_file[3]."/".$attached_file[4];
				}
			}
		}
		$pdfdir = (FCPATH)."assets/";
		//we gave absolute path because sometimes the libraries can't detect the path. Please use your path here.
		$output = $pdfdir."pdf/MergedPDF-".$dtsno.".pdf";

		$cmd = "gs -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=".$output." -dBATCH ";
		//setting enviroment variable path for windows if ubuntu remove code
		putenv('PATH=C:\Program Files\gs\gs9.56.1\bin');

		foreach($files as $file) {
		    //Setting path for each file
		    $pdf= $pdfdir.$file;
			if (file_exists($pdfdir.$file)) {  
				//adding each file to the command
			    $cmd = $cmd.$pdf." ";
			}
		}
		// The final command will be the comment below, if you run this command directly in cmd, the output will be similar
		//gs -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=C:/xampp/htdocs/erdbdts/pdf/NewMergedPDF.pdf -dBATCH C:/xampp/htdocs/erdbdts/pdf/doc1.pdf C:/xampp/htdocs/erdbdts/pdf/doc2.pdf
		//for ubuntu use shell_exec($cmd); for windows use exec($cmd);
		//$result = shell_exec($cmd);
		$result = exec($cmd);
		foreach($files as $file) {
		    //Setting path for each file
		    $pdf= $pdfdir.$file;
		    $file_dir = explode("/",$pdf);
		    if($file_dir[1] == "pdf"){
			    //unlink all temporary pdf
				if (file_exists($pdf)) {  
				    unlink($pdf);
				}
			}
		}
        echo json_encode($result);
  	}

  	public function mergepdf_gs_permanent(){
		$this->load->model('Viewdoc_model', '', TRUE);
  		$dtsno = $this->input->post("dtsno");
  		$sub_type_id = $this->input->post("sub_type_id");
  		$doc_type_id = $this->input->post("doc_type_id");
  		//for ubuntu follow this https://installati.one/ubuntu/20.04/ghostscript/
  		//for ubuntu https://orangeable.com/php/pdf-merge
  		//merge pdf using ghostscript install ghost script follow this site
  		//The Ghostscript is a command-line library that is not built-in on windows. First, we need to install the Ghostscript from the link https://ghostscript.com/releases/gsdnld.html.
  		//Run the downloaded file and install it. Once the installation is complete, go to the installation directory’s bin folder(C:\Program Files\gs\gs9.55.0\bin) and rename the file gswin64c.exe to gs.exe to use gs as a command.
		//The installation of Ghostscript is complete. The next step is to use PHP to merge pdf files with Ghostscript.
		$linked_docs = $this->Viewdoc_model->get_linked_document_active($dtsno);
		$attached_docs = $this->Viewdoc_model->get_attached_files($dtsno);
		$files = array();
		if($doc_type_id == 2){
			$files[] = "pdf/approved/dv-".$dtsno.".pdf";
			$result = $this->Viewdoc_model->get_docs_tobe_merged($sub_type_id);
		}
		if($doc_type_id == 107){
			$files[] = "pdf/approved/ca_liquidation-".$dtsno.".pdf";
			$result = $this->Viewdoc_model->get_docs_tobe_merged($doc_type_id);
		}
		if($linked_docs){
			foreach($linked_docs as $val){
				if($val->type == 5){//TO
					//check if doc has permanent document
					if (file_exists((FCPATH)."assets/pdf/approved/travel_order-".$val->linked_dtsno.".pdf")) {  
						$files[] = "pdf/approved/travel_order-".$val->linked_dtsno.".pdf";
					}else{
						$files[] = "pdf/temp/travel_order-".$val->linked_dtsno.".pdf";
					}
				}else if($sub_type_id == 116 && $doc_type_id == 2){
					//check if doc has permanent document
					if (file_exists((FCPATH)."assets/pdf/approved/ca_liquidation-".$val->linked_dtsno.".pdf")) {  
						$files[] = "pdf/approved/ca_liquidation-".$val->linked_dtsno.".pdf";
					}else{
						$files[] = "pdf/temp/ca_liquidation-".$val->linked_dtsno.".pdf";
					}
				}else if($val->type == 23){//if linked_doc is SO
					if($val->file2!=""){
						if (file_exists((FCPATH).$val->attached_file2)) {  
							$files[] = trim($val->attached_file2, "assets/");
						}
					}
					if($val->file!=""){
						if (file_exists((FCPATH).$val->attached_file)) {  
							$files[] = trim($val->attached_file, "assets/");
						}
					}
				}else{
					//check if doc has permanent document
					if (file_exists((FCPATH)."assets/pdf/approved/dv-".$val->linked_dtsno.".pdf")) {  
						$files[] = "pdf/approved/dv-".$val->linked_dtsno.".pdf";
					}else{
						$files[] = "pdf/temp/dv-".$val->linked_dtsno.".pdf";
					}
				}
			}
		}
		foreach($result as $val){
			$file = "pdf/approved/{$val->pdf_link}-".$dtsno.".pdf";
			if (file_exists((FCPATH)."assets/".$file)) {  
			$files[] = $file;
			}
		}
		if($attached_docs){
			foreach($attached_docs as $val){
				if($val->filename!=""){
					$attached_file = explode("/", $val->file);
					$files[] = $attached_file[1]."/".$attached_file[2]."/".$attached_file[3]."/".$attached_file[4];
				}
			}
		}

		$pdfdir = (FCPATH)."assets/";
		//we gave absolute path because sometimes the libraries can't detect the path. Please use your path here.
		$output = $pdfdir."pdf/approved/"."MergedPDF-".$dtsno.".pdf";

		$cmd = "gs -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=".$output." -dBATCH ";
		//setting enviroment variable path
		putenv('PATH=C:\Program Files\gs\gs9.56.1\bin');
		foreach($files as $file) {
		    //Setting path for each file
		    $pdf= $pdfdir.$file;
			if (file_exists($pdfdir.$file)) {  
				//adding each file to the command
			    $cmd = $cmd.$pdf." ";
			}
		}

		// The final command will be the comment below, if you run this command directly in cmd, the output will be similar
		//gs -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=C:/xampp/htdocs/erdbdts/pdf/NewMergedPDF.pdf -dBATCH C:/xampp/htdocs/erdbdts/pdf/doc1.pdf C:/xampp/htdocs/erdbdts/pdf/doc2.pdf
		exec($cmd);
        echo json_encode(true);
  	}

  	public function download_mergedPdf(){
  		$dtsno = $this->input->post("dtsno");
		$pdfdir = (FCPATH)."assets/pdf/";
		//we gave absolute path because sometimes the libraries can't detect the path. Please use your path here.
		$output = $pdfdir."MergedPDF-".$dtsno.".pdf";
		$this->load->helper('download');
	    $data = file_get_contents($output);
		if(file_exists($output)){
		    unlink($output);
		}
        $name = 'AllPdf-'.$dtsno.'.pdf'; // custom file name for your download
        force_download($name, $data);
  	}

  	public function download_mergedPdf_permanent(){
  		$dtsno = $this->input->post("dtsno");
		$pdfdir = (FCPATH)."assets/pdf/approved/";
		//we gave absolute path because sometimes the libraries can't detect the path. Please use your path here.
		$output = $pdfdir."MergedPDF-".$dtsno.".pdf";
		$this->load->helper('download');
	    $data = file_get_contents($output);
        $name = 'AllPdf-'.$dtsno.'.pdf'; // custom file name for your download
        force_download($name, $data);
  	}
}