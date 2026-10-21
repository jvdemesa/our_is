<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Client;

class Google_auth extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('cookie');	
	}
	
	public function index() {
		$this->load->model('User_login_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	    // Load the Google API PHP Client Library
	    $client = new Google_Client();
        $client->setClientId('138293049240-lfag7m2o3nva9m6v6hcikbk14jenmp86.apps.googleusercontent.com');
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri('http://localhost/our_is/Google_auth');
        $client->addScope("email");
        $client->addScope("profile");

	    if ($this->input->get('code')) { 
	        $client->authenticate($this->input->get('code'));
	        $this->session->set_userdata('token', $client->getAccessToken());
	        $client->setAccessToken($this->session->userdata('token'));
	    } 

	    if ($client->getAccessToken()) { 
	        $google_oauth = new Google_Service_Oauth2($client);
	        // Get user profile data from Google 
	        $google_data = $google_oauth->userinfo->get(); 
	        $user_information = $this->User_login_model->verify_via_email($google_data->email);
	        if($user_information){
				$sessiondata = new stdClass();
				$sessiondata->log_type = '2';
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
	                setcookie('remember_me', base64_encode($session_id . ':' . $token), $cookie_expiration, '/', $_SERVER['HTTP_HOST'], true, true);
	        	}
		        redirect(base_url().$sessiondata->home_page);
	        }else{
		        redirect(base_url()."/Authentication");
	        }
	    } else {
	        // Redirect the user to the Google authentication page
	        redirect($client->createAuthUrl());
	    }
	}

}
