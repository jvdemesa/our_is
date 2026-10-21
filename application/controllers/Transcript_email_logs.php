<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transcript_email_logs extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('General_model', '', TRUE);
        $this->load->helper('url');
        $this->load->helper('file');
	}

  	public function index(){
		$this->load->model('Admin_model', '', TRUE);
		$this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'transcript_email_logs/index';
        $this->data['list'] = $this->Admin_model->list('link');
		$this->load->view('template/admin', $this->data);
  	}

   	public function get_list(){
    	$this->load->model('Transcript_email_logs_model');
    	$this->load->model('General_model');
        $list = $this->Transcript_email_logs_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
            $row = array();
            $row[] = $val->lname.", ".$val->fname." ".$val->mname;
            $row[] = $val->trans_code;
            $row[] = $val->action;
            $row[] = $val->staff_name;
            $row[] = $val->datetime;
            $row[] = $val->is_sent==1?"yes":"No";
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Transcript_email_logs_model->count_all(),
                        "recordsFiltered" => $this->Transcript_email_logs_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
   	}

}
