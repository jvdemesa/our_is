<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Client;

class Authentication extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('cookie');	
	}
	
	public function index()
	{
		$this->login_page();
	}
	
	public function login_page()
	{
		if(!isset($this->session->userdata[SESSION_DATA]))
		{
			if ($this->input->cookie('our_is_token')) {
				//$this->login_with_token();
			}	
	        $client = new Google_Client();
	        $client->setClientId('138293049240-lfag7m2o3nva9m6v6hcikbk14jenmp86.apps.googleusercontent.com');
	        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
	        $client->setRedirectUri('http://localhost/our_is/Google_auth');
		  	$client->addScope("email");
		    $client->addScope("profile");
    		$this->data['page_title'] = 'Login';
			$this->data['validation_message'] = '';
			$this->data['google_auth_url'] = $client->createAuthUrl();
			$this->load->view('authentication/login', $this->data); 

		} else {
			$home_page = $this->session->userdata(SESSION_DATA)->home_page;
			redirect(site_url($home_page));
		}
	}
	
	public function validate_email()
	{
		$this->load->model('User_login_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
		$email = $this->input->POST('email');
		$this->load->model('Admin_model');
		$data = $this->User_login_model->validate_email($email);
		$json = new stdClass();
		if($data !=0){
			$ucode = random_int(100000, 999999);
			$code = new stdClass();
			$code->uid = $data;
			$code->code = password_hash(TRIM($ucode), PASSWORD_BCRYPT);
			$code->with_expiration = 1;
			$code->datetime_created = date("Y-m-d H:i:s");

			$code_id = $this->General_model->add_data("verification_code",$code);

			$email_data = new stdClass();
			$email_data->to = $this->input->post('email');
			$email_data->subject = "Los Baños E-Boss Forgot Password";
			$email_data->message = "Good day,<br><br>You have succesfully requested to update / change your account password with this email. Please enter the code below to change your password. This code is valid only for 5 minutes.<br><br><h4 style='color:red;'>$ucode</h4>";
			$this->send_email($email_data);
			$json->code_id = $code_id;
		}
		$json->uid = $data;
		$json->start_time = $code->datetime_created;
		echo json_encode($json);
	}
	
	public function update_password()
	{
		$this->load->model('General_model', '', TRUE);
		$uid = $this->input->POST('uid');
		$password = $this->input->POST('password');
		$code_id = $this->input->POST('code_id');
		$code = $this->input->POST('code');
		$where = new stdClass();
		$where->uid = $uid;
		$where->id = $code_id;
		$verify_code = $this->General_model->list_dynamic("verification_code",$where);
		if($verify_code){
			if(password_verify($code, $verify_code[0]->code)){
				$from_time = $verify_code[0]->datetime_created;
				$to_time = date("Y-m-d H:i:s");
				$diff = abs(strtotime($to_time) - strtotime($from_time));
				$interval_sec = round($diff);
				if($interval_sec <= 300){
					$data = new stdClass();
					$data->password = password_hash(TRIM($password), PASSWORD_BCRYPT);
					$where_uid = new stdClass();
					$where_uid->uid = $uid;
					$this->General_model->update_where($data,$where_uid,"users");
					$this->General_model->delete("verification_code",$where);
					$result = 1;
				}else{
					$result = 2;
					$this->General_model->delete("verification_code",$where);
				}
			}else{
				$result = 0;
			}
		}else{
			$result = 0;
		}
		echo json_encode($result);
	}

	public function login()
	{
	 	$this->load->library('form_validation');
		$this->load->model('General_model', '', TRUE);
		$message = "Not Validation";
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[50]');
	    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]');	
		$data = new stdClass();
		if ($this->form_validation->run() !== FALSE) 
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');	
			// $captcha = $this->input->post('captcha');	
			// $long = $this->input->post('long');
			// $lat = $this->input->post('lat');	
			$this->load->model('User_login_model', '', TRUE);
			// if($captcha==$this->session->userdata('captcha')){
				$user_information = $this->User_login_model->verify($username);
				
				if($user_information != false) 
				{
					if($user_information->is_disabled == 0){
						if(password_verify($password, $user_information->password)){
							$sessiondata = new stdClass();
							$sessiondata->log_type = '1';
							$sessiondata->uid = $user_information->uid;
							$sessiondata->username = $user_information->username;
							$sessiondata->fullname = strtoupper($user_information->first_name." ".$user_information->last_name);
							$sessiondata->first_name = $user_information->first_name;
							$sessiondata->middle_name = $user_information->middle_name;
							$sessiondata->last_name = $user_information->last_name;
							$sessiondata->access = explode(",", $user_information->access);
							$sessiondata->home_page = $user_information->home_page;
							$sessiondata->window_id = $user_information->window_id;
							$links = $this->User_login_model->get_links($sessiondata->access);
							//$array[]->link to $Link[] (multidimension to single)
							$sessiondata->links_label = array_column($links, 'label');
							$sessiondata->links = array_column($links, 'link');
							$this->session->set_userdata(SESSION_DATA, $sessiondata);
							//set session for total number of client to 0 upon login
							$this->session->set_userdata('total_client',0);
							
						   // Check for the IP address in the $_SERVER superglobal
						    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
						        // Use the forwarded IP address if it's available
						        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
						    } else {
						        // Otherwise, use the remote address
						        $ip = $_SERVER['REMOTE_ADDR'];
						    }
							$log_details = new stdClass();
							$log_details->uid = $user_information->uid;
							$log_details->date = date('Y-m-d');
							$log_details->time = date('H:i:s');
							$log_details->ip = $ip;
							$log_details->log_type = "1";
							$sessiondata->log_id = $this->General_model->add_data('log_details',$log_details);

							$this->session->set_userdata(SESSION_DATA, $sessiondata);
							//set session for total number of client to 0 upon login
							$this->session->set_userdata('total_client',0);
							$this->session->sess_regenerate();
							$session_id = session_id();
						     // If the "remember me" checkbox is checked, set a cookie
				            if ($this->input->post('remember_me')) {
				                $cookie_expiration = time() + (30 * 24 * 60 * 60); // Set the cookie expiration to 30 days
				                $token = bin2hex(random_bytes(32));
					            $data = new stdClass();
					            $data->uid = $user_information->uid;
					            $data->session_id = $session_id;
					            $data->token =  password_hash($token, PASSWORD_BCRYPT);
					            $data->expiration = date('Y-m-d H:i:s', $cookie_expiration);
					            $token_id = $this->General_model->add_data("user_token",$data);	
				                setcookie('Reclassification_model', base64_encode($session_id . ':' . $token), $cookie_expiration, '/', $_SERVER['HTTP_HOST'], true, true);
				        	}
							$data->status = 0;					
							$data->message = "Completed";
							$data->home_page = $sessiondata->home_page;
						}else{
							$data->status = 1;					
							$data->message = "Incorrect Password.";
						}
					}else{
						$data->message = "User is disabled or not yet verified by the administrator. For clarification please contact the MIS.";
					}
				}
				else
				{
					$data->status = 2;					
					$data->message = "Username not found.";
				}	
			// }else{
			// 		$data->message = "Incorrect Captcha!";
			// }				
		} 
		else 
		{
			$data->message = "Please enter correct values";					
			$data->status = 3;					
			//$data->error = validation_errors();					
		}	

		output_to_json($this, $data);	
	}	

	public function login_with_token()
	{
		$this->load->model('General_model', '', TRUE);
		$cookie = base64_decode($this->input->cookie('our_is_token'));
        list($session_id, $token) = explode(':', $cookie);
		$this->load->model('User_login_model', '', TRUE);
		// if($captcha==$this->session->userdata('captcha')){
		$user_information = $this->User_login_model->verify_token($session_id);
		if($user_information != false) 
		{

			if(password_verify($token, $user_information->token)){
				$sessiondata = new stdClass();
				$sessiondata->uid = $user_information->uid;
				$sessiondata->username = $user_information->username;
				$sessiondata->fullname = strtoupper($user_information->first_name." ".$user_information->last_name);
				$sessiondata->first_name = $user_information->first_name;
				$sessiondata->middle_name = $user_information->middle_name;
				$sessiondata->last_name = $user_information->last_name;
				$sessiondata->access = explode(",", $user_information->access);
				$sessiondata->home_page = $user_information->home_page;
				$links = $this->User_login_model->get_links($sessiondata->access);
				//$array[]->link to $Link[] (multidimension to single)
				$sessiondata->links_label = array_column($links, 'label');
				$sessiondata->links = array_column($links, 'link');
				$this->session->set_userdata(SESSION_DATA, $sessiondata);
				$this->session->set_userdata('total_client', 0);
				$this->session->sess_regenerate();
				$new_session_id = session_id();
				$data = new stdClass();
				$data->session_id = $new_session_id;
				$where = new stdClass();
				$where->session_id = $session_id;
				$this->General_model->update_where($data,$where,"user_token");
                setcookie('our_is_token', base64_encode($new_session_id . ':' . $token), $cookie_expiration, '/', $_SERVER['HTTP_HOST'], true, true);
				redirect(base_url($user_information->home_page));
			}
		}
	}

	public function logout()
	{
		$this->load->model('General_model', '', TRUE);
		$cookie = base64_decode($this->input->cookie('our_is_token'));
		if($cookie){
  			list($session_id, $token) = explode(':', $cookie);
			$update_data = new stdClass();
			$update_data->force_expire = 1;
			$where = new stdClass();
			$where->session_id = $session_id;
			$this->General_model->update_where($update_data,$where,"user_token");
  		}

		$this->General_model->update_where(["logout_date"=>date("Y-m-d"),"logout_time"=>date("H:i:s"),"is_logout"=>1],["id"=>$this->session->userdata(SESSION_DATA)->log_id],"log_details");

		$sess_array = array(
		'username' => ''
		);
		$accessToken = $this->session->userdata('token');
		// var_dump($accessToken);
		//if logged using gmail
		if($this->session->userdata(SESSION_DATA)->log_type == '2'){
	        $client = new Google_Client();
	        $client->setClientId('138293049240-lfag7m2o3nva9m6v6hcikbk14jenmp86.apps.googleusercontent.com');
	        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
	        $client->setRedirectUri('http://localhost/our_is/Google_auth');
	        $client->addScope("email");
	        $client->addScope("profile");
	        $client->setAccessToken($accessToken);
			$client->revokeToken();
			// $this->session->unset_userdata('token');
		}
		setcookie('our_is_token', '', date("Y-m-d H:i:s"), '/', $_SERVER['HTTP_HOST'], true, true);
		$this->session->unset_userdata(SESSION_DATA, $sess_array);
		$data['message_display'] = '<strong>Info:</strong> Successfully Logout';
		redirect('Authentication');
	}

	public function restricted(){
		$this->load->model('User_login_model', '', TRUE);
		//check if still restricted
		if($this->User_login_model->get_user_restriction()){
			//if still restricted go to restricted page
			$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
			$this->load->view('authentication/restricted', $this->data); 
		}else{
			//if not restricted, go to dashboard
			redirect('Dashboard/');
		}
	}	
}
