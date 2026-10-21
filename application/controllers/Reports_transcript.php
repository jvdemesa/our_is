<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reports_transcript extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('General_model', '', TRUE);
	}

  	public function income(){
		$this->load->model('Admin_model', '', TRUE);
		// $this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'reports/transcript_income/index';
        $this->data['list'] = $this->Admin_model->list('link');
        $this->data['requests'] = $this->General_model->list_dynamic('transcript_request_type',["status"=>0]);
		$this->load->view('template/admin', $this->data);
  	}

   	public function get_income_list(){
    	$this->load->model('Reports_transcript_model');
    	$this->load->model('General_model');
        $data = $this->Reports_transcript_model->get_income_list();
        echo json_encode($data);
   	}

    public function match(){
        $this->load->model('Transcript_payments_model');
        $trans_code = $this->input->post("trans_code");
        $payment_id = $this->input->post("payment_id");
        $json_data = new stdClass();
        $this->db->trans_start(); // Start transaction
            $update_data = ["status"=>3];
            $where = ["trans_code"=>$trans_code,"status"=>2];
            $table = "tr_details";
            $json_data->tr_details = $this->General_model->update_where($update_data,$where,$table);
            $update_payment_data = ["trans_code"=>$trans_code,"is_found"=>1];
            $where_payment = ["id"=>$payment_id];
            $table_payment = "tr_payments";
            $json_data->tr_payments = $this->General_model->update_where($update_payment_data,$where_payment,$table_payment);
        if ($this->db->trans_status() === FALSE) {
            // If there is an error, rollback and return false
            $this->db->trans_rollback();
            $json_data->error = 1;
        } else {
            // Commit the transaction if everything is okay
            $this->db->trans_commit();
            $json_data->error = 0;
        }
        echo json_encode($json_data);
    }

    public function get_trans_code_list()
    {
        $this->load->model('Transcript_payments_model');
        $search = $this->input->post('q');
        $limit = 15;
        $jsondata = $this->Transcript_payments_model->list_requests($search,$limit);
        echo json_encode($jsondata);
    }
}
