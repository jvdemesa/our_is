<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transcript_payments extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
		$this->load->model('Admin_model', '', TRUE);
		$this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'transcript_payments/index';
        $this->data['list'] = $this->Admin_model->list('link');
        $this->data['assigned_to'] = $this->General_model->list_dynamic('users',["section_id"=>3]);
		$this->load->view('template/admin', $this->data);
  	}

   	public function get_list(){
    	$this->load->model('Transcript_payments_model');
    	$this->load->model('General_model');
        $list = $this->Transcript_payments_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
            switch($val->status){
                case "0":
                    $status = "New Request";
                break;
                case "1":
                    $status = "Assessed";
                break;
                case "2":
                    $status = "Assessment Sent";
                break;
                case "3":
                    $status = "Proof of payment received";
                break;
                case "4":
                    $status = "Payment verified - On Process";
                break;
                case "5":
                    $status = "For release";
                break;
                case "6":
                    $status = "Released";
                break;
                default:
                    $status = "No matching request";
                break;
            }
            $action = "<div class='btn-group'>
                            ";
            if($val->is_found ==0){
                $action .= "<button class='btn btn-success btn-sm match  tool_tip' payment_id='$val->id' title='Match to request'>
                            <i class='fa fa-handshake-o'></i>
                        </button>";
            }
            $action .= "</div>";
        $row = array();
        $row[] = $val->trans_code;
        $row[] = $val->refnum;
        $row[] = $val->timestamp;
        $row[] = $val->fname." ".$val->lname;
        $row[] = "<a href='#' email='$val->email' class='compare_email' payment_id='$val->id'>".$val->email."</a>";
        $row[] = "<a link='$val->file_link' href='#' class='view_file'>View</a>";
        $row[] = $status;
        $row[] = $val->is_found==1?"Yes":"No";
        $row[] = $action;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Transcript_payments_model->count_all(),
                        "recordsFiltered" => $this->Transcript_payments_model->count_filtered(),
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

    public function match_payment(){
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

    public function compare_email()
    {
        $this->load->model('Transcript_payments_model');
        $email = $this->input->post('email');
        $jsondata = $this->Transcript_payments_model->matched_email($email);
        echo json_encode($jsondata);
    }
}
