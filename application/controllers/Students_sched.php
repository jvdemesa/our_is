<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Students_sched extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
	}

	public function index(){
		$this->isAllowed();
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'students_sched/index';
		$this->data['academic_program'] = $this->Admin_model->list("academic_program");
		$this->data['links'] = $this->linkGenerator();
		$this->load->view('template/admin', $this->data);
	}

	public function save_draft()
	{
		$this->load->model('General_model', '', TRUE);
		$id = $this->input->post("id");
		$equota = $this->input->post("equota");
		for($i =0 ; $i<count($id); $i++){
			$this->General_model->update(['equota'=>$equota[$i]],$id[$i],'equota','id');
		}
		echo json_encode(1);
	}

	public function finalize()
	{
		$this->load->model('General_model', '', TRUE);
		$id = $this->input->post("id");
		$equota = $this->input->post("equota");
		for($i =0 ; $i<count($id); $i++){
			$this->General_model->update(['equota'=>$equota[$i], "is_finalized"=>1],$id[$i],'equota','id');
		}
		echo json_encode(1);
	}

	public function delete(){
		$this->load->model('Admin_model', '', TRUE);
		$id = $this->input->post("id");
		$this->Admin_model->delete($id,"id",'equota');
        $jsondata['message']=  'Curriculum has been deleted.';
		echo json_encode($jsondata);
	}

	public function generate() {
		$this->load->model('Students_sched_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
		$list = $this->Students_sched_model->list_ap();
		$data = array();
		foreach($list as $key=>$val){
			$col = new stdClass();
			$col->ap_id = $val->ap_id;
			$col->year = $this->input->post("year");
			$col->sem = $this->input->post("sem");
			$col->is_finalized = 0;
			$data[] = $col;
		}
		$this->General_model->insert_batch('equota',$data);
        echo json_encode(1);
	}

	public function get_list() {
		$this->load->model('Students_sched_model', '', TRUE);
		$where = new stdClass();
		$where->year = $this->input->post("year");
		$where->sem = $this->input->post("sem");
		$data = $this->Students_sched_model->list($where);
        echo json_encode($data);
	}


	public function get_nf_list() {
		$this->load->model('Students_sched_model', '', TRUE);
		$where = new stdClass();
		$where->year = $this->input->post("year");
		$where->sem = $this->input->post("sem");
		$data = $this->Students_sched_model->nf_list($where);
		$json_data = array();
		if($data){
			foreach($data as $val){
				$where = new stdClass();
				$where->year = $val->year;
				$where->sem = $val->sem;
				$where->date_uploaded = $val->date_uploaded;
	            $subject_cap = $this->Students_sched_model->check_nf_equota($where);
	            if($subject_cap){
	            	$json_data[] = array("year"=>$val->year, "sem"=>$val->sem, "date_uploaded"=>$val->date_uploaded, "underload"=>count($subject_cap));
	            }else{
	            	$json_data[] = array("year"=>$val->year, "sem"=>$val->sem, "date_uploaded"=>$val->date_uploaded, "underload"=>0);
	            }
			}
		}else{
			$json_data = false;
		}
        echo json_encode($json_data);
	}

}