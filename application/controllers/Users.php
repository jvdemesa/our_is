<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
	}

	public function index()
	{
		$this->isAllowed();	
		$this->load->model('Users_model', '', TRUE);
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'users/list';
		$this->data['at_list'] = $this->Admin_model->at_list('access_type');
		$this->data['link_list'] = $this->Admin_model->list('link');
		$this->data['section_list'] = $this->Admin_model->list('sections');
		$this->load->view('template/admin', $this->data);
	}

	public function profile()
	{
		$this->load->model('Users_model', '', TRUE);
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'users/profile';
		$this->data['at_list'] = $this->Admin_model->at_list('access_type');
		$this->data['link_list'] = $this->Admin_model->list('link');
		$this->data['positions'] = $this->Admin_model->list(ERDBDTRS.'.positions');
		$this->data['province'] = $this->Admin_model->list(ERDBDTRS.'.refprovince');
		$this->data['official_station'] = $this->Admin_model->list(ERDBDTRS.'.official_station');
		$where = new stdClass();
		$where->unit_parent = 0;
		$this->data['division'] = $this->General_model->list_dynamic(ERDBDTRS.'.divunit',$where);
		$this->data['divunit'] = $this->Admin_model->division();
		$this->load->view('template/admin', $this->data);
	}
	
	public function update_profile()
	{
		$this->load->model('Users_model', '', TRUE);
		
		$userdata = $this->session->userdata(SESSION_DATA);
		$data1 = array();
		$data1["fname"] = $this->input->post('fname');
		$data1["lname"] = $this->input->post('lname');
		$data1["mname"] = $this->input->post('mname');
		$data1["middle_initial"] = $this->input->post('middle_initial');
		$data1["suffix"] = $this->input->post('suffix');
		$data1["position"] = $this->input->post('position');
		$data1["division"] = $this->input->post('division');
		$data1["unit"] = $this->input->post('unit');
		$data1["employmentStatus"] = $this->input->post('employmentStatus');
		$data1["bday"] = date( 'Y-m-d', strtotime($this->input->post('bday')) );
		$data1["idnum"] = $this->input->post('idnum');
		$data1["email"] = $this->input->post('email');
		$data1["address"] = $this->input->post('address');
		$data1["username"] = $this->input->post('username');
		$data1["is_included"] = $this->input->post('is_included');
		$data1["section_id"] = $this->input->post('section_id');
		if($this->input->post('home_page')){
		$data1["home_page"] = $this->input->post('home_page');
		}
		if($this->input->post('password')!=''){
			$data1["password"] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
		}
		$data1["provCode"] = $this->input->post('provCode');
		$data1["citymunCode"] = $this->input->post('citymunCode');
		$data1["brgyCode"] = $this->input->post('brgyCode');
		$data1["province"] = $this->input->post('province');
		$data1["citymun"] = $this->input->post('citymun');
		$data1["brgy"] = $this->input->post('brgy');
		$data1["official_station"] = $this->input->post('official_station');
		$data1["bank_acc_no"] = $this->input->post('bank_acc_no');
		
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
		}

		if($this->input->post('privilege')) {
			$privilege = $this->input->post('privilege');
			foreach($privilege as $key=>$acc){
				if($key=='0'){
				$data1["privilege"] = $acc;
				}
				else{
				$data1["privilege"] .= ','.$acc;
				}
			}
		}
	
		$uid = $userdata->uid;
		if(trim($this->input->post('display_pic')) != '') {
			$imagedata = $_POST["display_pic"];
			//add file name
			$imageName = 'user'.$uid.'.jpg';
			//upload image
			if($imagedata != 'data:,'){
				$image_array_1 = explode(";", $imagedata);
				$image_array_2 = explode(",", $image_array_1[1]);
				$imagedata = base64_decode($image_array_2[1]);
				file_put_contents("assets/images/user_photo/".$imageName, $imagedata);
				$this->resizeImage($imageName,"assets/images/user_photo/");
			}
		} 

		$upload_dir = "assets/images/signature/";
		$file_name = "";
		if (!file_exists($upload_dir)) {
    		mkdir($upload_dir, 0777, true);  //create directory if not exist
		}
		if ( $this->input->post('action_signature') ) {

				$signed_img = $this->input->post('input_signature_img');
				if ( !strpos($signed_img,"image/png") ) {
					$signed_img = str_replace('data:image/svg+xml;base64,', '', $signed_img);
					$signed_img = str_replace(' ', '+', $signed_img);
					$data1["img_type"] = 'svg+xml';
				} else {
					$signed_img = str_replace('data:image/png;base64,', '', $signed_img);
					$data1["img_type"] = 'image/png';
				}
				
				$data = base64_decode($signed_img);
				$data1["signature"] = $data;
		}


		if ( $this->input->post('action_initial_signature') ) {

				$signed_img = $this->input->post('input_initial_signature_img');
				if ( !strpos($signed_img,"image/png") ) {
					$signed_img = str_replace('data:image/svg+xml;base64,', '', $signed_img);
					$signed_img = str_replace(' ', '+', $signed_img);
					$data1["sign_initial_img_type"] = 'svg+xml';
				} else {
					$signed_img = str_replace('data:image/png;base64,', '', $signed_img);
					$data1["sign_initial_img_type"] = 'image/png';
				}
				
				$data = base64_decode($signed_img);
				$data1["sign_initial"] = $data;
		}

		// if(file_exists($_FILES["signature"]["tmp_name"])) {
		// 	$imageProperties = getimagesize($_FILES['signature']['tmp_name']);
		// 	$data1["signature"] =file_get_contents($_FILES['signature']['tmp_name']);
		// 	$data1["img_type"] = $imageProperties['mime'];	
		// }

		$this->Users_model->edit_users($data1,$uid);

		$msg = "User details has been updated.";
		echo json_encode($msg);
	}

	public function add_users()
	{
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
		$data1["is_disabled"] = 0;
		$data1["home_page"] = $this->input->post('home_page');
		$data1["section_id"] = $this->input->post('section_id');
		$data1["on_leave"] = $this->input->post('on_leave');
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
			$data1["access"] = 0;
		}
		$id = $this->Users_model->add_users($data1,'users');
		$msg = "User has been added.";
		echo json_encode($msg);
		//redirect(site_url("Users/"));
	}

	public function edit_info()
	{
		$this->load->model('Users_model', '', TRUE);
		$data1 = array();
		$data1["first_name"] = $this->input->post('first_name');
		$data1["last_name"] = $this->input->post('last_name');
		$data1["middle_name"] = $this->input->post('middle_name');
		$data1["username"] = $this->input->post('username');
		$data1["mobile_number"] = $this->input->post('mobile_number');
		$data1["email"] = $this->input->post('email');
		$data1["mobile_number"] = $this->input->post('mobile_number');
		$data1["home_page"] = $this->input->post('home_page');
		$data1["section_id"] = $this->input->post('section_id');
		$data1["on_leave"] = $this->input->post('on_leave');
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
			$data1["access"] = 0;
		}
		$uid = $this->input->post('uid');

		$this->Users_model->edit_users($data1,$uid);

		$msg = "User details has been updated.";
		echo json_encode($msg);
	}	

	public function get_users_json() {
    	$this->load->model('Users_model');
    	$this->load->model('General_model');
        $list = $this->Users_model->get_users_list();
        $at_list = $this->General_model->list("access_type");
        $data = array();
        foreach ($list as $val) {
        	$user_privilege = "";
        	$privilege = explode(",",$val->access);
        	foreach($privilege as $key=>$value){
    			foreach($at_list as $at){
        			if($value == $at->at_id){
        				if($key == 0){
        					$user_privilege .= "- $at->access_type";
        				}else{
        					$user_privilege .= "<br>- $at->access_type";
        				}
        			}
    			}
        	}
            $row = array();
            $row[] = $val->uid;
            $row[] = $val->first_name." ".$val->last_name;
            $row[] = $val->username;
            $row[] = $user_privilege;
    		$actions='<center>
						<div class="btn btn-group">
    						<button type="button" 
                                class="btn bg-primary btn-sm edit_info" 
                                title="Edit User Information" 
                                uid="'.$val->uid.'">
                                <i class="fa fa-edit"></i>
                                </button>

								<button type="button" 
                                class="btn btn-success btn-sm reset_password" 
                                title="Reset password" 
                                uid="'.$val->uid.'" uname="'.$val->last_name.", ".$val->first_name." ".$val->middle_name. '">
                                <i class="fa fa-rotate-left "></i></button>
								';
			if($val->is_disabled == 0){
    		$actions.='	<button type="button" 
                                class="btn btn-warning btn-sm disable_user" 
                                title="Disable User" 
                                uid="'.$val->uid.'" uname="'.$val->last_name.", ".$val->first_name." ".$val->middle_name. '">
                                <i class="fa fa-toggle-on "></i></button>
								';
			}else{
    		$actions.='	<button type="button" 
                                class="btn btn-warning btn-sm enable_user" 
                                title="Enable User" 
                                uid="'.$val->uid.'" uname="'.$val->last_name.", ".$val->first_name." ".$val->middle_name. '">
                                <i class="fa fa-toggle-off "></i></button>
								';			}
			$actions .='</div></center>';
			$row[] = $actions;
			$row[] = $val->is_disabled;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Users_model->count_all(),
                        "recordsFiltered" => $this->Users_model->count_all(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
	}

	public function users_details_json() {
		$this->load->model('Users_model', '', TRUE);
		$uid = $this->input->post('uid');
		$result = $this->Users_model->users_details_json($uid);

        echo json_encode($result);
	}

	public function reset_password()
	{
		$this->load->model('Users_model', '', TRUE);
		$uid = $this->input->post('uid');
		$data = array();

		$new_password = generate_random_keys(8);
		$data["password"] = password_hash($new_password, PASSWORD_BCRYPT);
		$this->Users_model->edit_users($data,$uid);

		$this->outputJSON(['success' => true, 'new_password' => $new_password ]);
	}

	public function disable_user()
	{
		$this->load->model('Users_model', '', TRUE);
		$uid = $this->input->post('uid');
		$data = array();

		$data["is_disabled"] = $this->input->post('is_disable');
		$this->Users_model->edit_users($data,$uid);

		$this->outputJSON(1);
	}
	
	public function edit_user_profile()
	{
		$this->load->model('Users_model', '', TRUE);
		$imagedata = $_POST["display_pic"];
		$data1 = array();
		$data1["fname"] = $this->input->post('fname');
		$data1["lname"] = $this->input->post('lname');
		$data1["mname"] = $this->input->post('mname');

		$uid = $this->input->post('uid');

		//add file name
		$imageName = 'user'.$uid.'.jpg';
		//upload image
		if($imagedata != 'data:,'){
		$image_array_1 = explode(";", $imagedata);
		$image_array_2 = explode(",", $image_array_1[1]);
		$imagedata = base64_decode($image_array_2[1]);
			file_put_contents("assets/images/user_photo/".$imageName, $imagedata);
		}

		if(file_exists($_FILES["signature"]["tmp_name"])) {
			$imageProperties = getimagesize($_FILES['signature']['tmp_name']);
			$data1["signature"] =file_get_contents($_FILES['signature']['tmp_name']);
			$data1["img_type"] = $imageProperties['mime'];	
		}

		if($this->input->post('password')!=''){
			$data1["password"] = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
		}

		$this->Users_model->edit_users($data1,$uid);

		$msg = "User details has been updated.";
		echo json_encode($msg);
	}

	public function update_all_password(){
				$this->load->model('Users_model', '', TRUE);

		$list = $this->General_model->list(ERDBDTRS.".users");
		foreach($list as $val){
			$data = new stdClass();
			$data->password = password_hash("1234", PASSWORD_BCRYPT);
			$data->home_page = "17";
			$data->privilege = "3";
			$this->Users_model->edit_users($data,$val->uid);
		}
	}

}
