<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registration extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');		
	}
	
	public function index()
	{
		$this->load->model('General_model', '', TRUE);
		$this->data['content'] = 'registration/index';
		$this->load->view('registration/index', $this->data);
	}
	
	public function new(){
	    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	        show_404(); // or redirect('register/form');
	        return;
	    }
		$this->load->model('Users_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
		$this->load->model('Users_model', '', TRUE);
		$data1 = array();
		$data1["first_name"] = $this->input->post('first_name');
		$data1["last_name"] = $this->input->post('last_name');
		$data1["middle_name"] = $this->input->post('middle_name');
		$data1["username"] = $this->input->post('username');
		$data1["mobile_number"] = $this->input->post('mobile_number');
		$data1["password"] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
		$data1["email"] = $this->input->post('email');
		$data1["mobile_number"] = $this->input->post('mobile_number');
		$data1["is_disabled"] = 2;//unverified
		$data1["home_page"] = 1;
		$access = $this->input->post('access');
		if($access){
			foreach($access as $key=>$acc){
				if($key=='0'){
				$data1["access"] = $acc;
				}
				else{
				$data1["access"] .= ','.$acc;
				}
			}
		}else{
			$data1["access"] = 3;
		}
		$where = new stdClass();
		$where->first_name = $this->input->post('first_name');
		$where->last_name = $this->input->post('last_name');
		$where->middle_name = $this->input->post('middle_name');
		$user = $this->General_model->list_dynamic("users",$where);
		$json = new stdClass();
		if($user == false){
			// $uid = $this->General_model->add_data("users",$data1);
			if($uid){
				// $ucode = random_int(100000, 999999);
				// $code = new stdClass();
				// $code->uid = $uid;
				// $code->code = password_hash(TRIM($ucode), PASSWORD_BCRYPT);
				// $code->with_expiration = 0;
				// $code->datetime_created = date("Y-m-d H:i:s");

				// $code_id = $this->General_model->add_data("verification_code",$code);

				// $email_data = new stdClass();
				// $email_data->to = $this->input->post('email');
				// $email_data->subject = "Los Baños E-Boss Account Verification";
				// $email_data->message = "Good day,<br><br>You have succesfully registered to Los Baños E-Boss. To verify your account please enter this code <p style='color:red;'><b>$ucode</b></p> in the confirmation dialog box.";

				// $this->send_email($email_data);
				// $json->error = 0;
				// $json->code_id = $code_id;
				// $json->uid = $uid;
				// $json->msg = "User details has been submitted. To validate account a confirmation email has been send to your email.";
			}else{
				$json->msg = "Something went wrong!";
				$json->error = 1;
			}
		}else{
			$json->msg = "This user is already registered!";
			$json->error = 1;
		}
		echo json_encode($json);
	}
}
