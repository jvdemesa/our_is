<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transcript_records extends MY_Controller {

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
		$this->data['content'] = 'transcript_records/index';
        $this->data['list'] = $this->Admin_model->list('link');
		$this->load->view('template/admin', $this->data);
  	}

   	public function get_list(){
    	$this->load->model('Transcript_records_model');
    	$this->load->model('General_model');
        $list = $this->Transcript_records_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
            $action = "<div class='btn-group'>
                            ";
                $action .= "<button class='btn btn-success btn-sm download  tool_tip' payment_id='$val->id' title='Match to request'>
                            <i class='fa fa-eye'></i>
                        </button>";
            $action .= "</div>";
        $row = array();
        $row[] = $val->id;
        $row[] = $val->fullname;
        $row[] = $val->filename;
        $row[] = $action;
        $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Transcript_records_model->count_all(),
                        "recordsFiltered" => $this->Transcript_records_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
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

    public function download() {
        $this->load->helper('download');
        $file_name = 'test.xlsx';
         // Path to the shared drive, update this path to where your shared drive is mounted
        $shared_drive_path = 'C:\Users\demes\Downloads/' . $file_name;

        if (file_exists($shared_drive_path)) {
            // Use CodeIgniter's force_download helper to download the file
            force_download($shared_drive_path, NULL);
        } else {
            // echo "asd";
            show_404(); // File not found
        }

    }

}
