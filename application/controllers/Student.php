<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Student_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
 		// $this->load->model('Student_model');
		$this->load->model('Admin_model', '', TRUE);
		// $this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'student/index';
		$this->data['list'] = $this->Admin_model->list('link');
		$this->data['batch_id'] = $this->input->post('batch_id');
		$this->load->view('template/admin', $this->data);
  	}

   	public function list(){
   		$batch_id = $this->input->post("batch_id");
    	$this->load->model('Student_model');
    	$this->load->model('General_model');
        $list = $this->Student_model->get_datatables($batch_id);
        $data = array();
        foreach ($list as $val) {
        $row = array();
        $row[] = $val->sais_id;
        $row[] = $val->campus_id;
        $row[] = $val->last_name;
        $row[] = $val->first_name;
        $row[] = $val->middle_name;
        $row[] = $val->classification;
        $row[] = $val->degree;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Student_model->count_all($batch_id),
                        "recordsFiltered" => $this->Student_model->count_filtered($batch_id),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
   	}
}
