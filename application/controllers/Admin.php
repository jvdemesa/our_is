<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
	}

	public function add()
	{
		$this->load->model('Admin_model', '', TRUE);
		$table = $this->input->post('table');
		$id_name = $this->input->post('id_name');
		$data = array();
		$data[$table] = $this->input->post('dbval');
		$data["status"] = $this->input->post('status');
		$id = $this->input->post('id');
		if($id == ""){
			$check = $this->Admin_model->checker($data[$table],$table);
			if($check == 0){
				$this->Admin_model->add($data,$table);
				$this->session->set_flashdata('message', "Data has been added to the list!");
				$this->session->set_flashdata('todo', "success");
			}
			else{
				$this->session->set_flashdata('message', "Duplicate Entry! Cannot add Data!");
				$this->session->set_flashdata('todo', "warning");
			}			
			
		}
		else{
			$this->Admin_model->edit($data,$id,$table,$id_name);
			$this->session->set_flashdata('message', 'Data has been updated!');
			$this->session->set_flashdata('todo', "info");
		}
		redirect(site_url("Admin/".$table));
	}

	public function add_all()
	{
		$this->load->model('Admin_model', '', TRUE);
		$table = $this->input->post('table');
		$id_name = $this->input->post('id_name');
		$data = array();
		$data[$table] = $this->input->post('dbval');
		$id = $this->input->post('id');
		foreach($this->input->post() as $key=>$val){
			if(!in_array($key,["table","id_name",'dbval'])){
				$data["$key"] = $val;
			}
		}
		if($id == ""){
			$check = $this->Admin_model->checker($data[$table],$table);
			if($check == 0){
				$this->Admin_model->add($data,$table);
				$this->session->set_flashdata('message', "Data has been added to the list!");
				$this->session->set_flashdata('todo', "success");
			}
			else{
				$this->session->set_flashdata('message', "Duplicate Entry! Cannot add Data!");
				$this->session->set_flashdata('todo', "warning");
			}			
			
		}
		else{
			$this->Admin_model->edit($data,$id,$table,$id_name);
			$this->session->set_flashdata('message', 'Data has been updated!');
			$this->session->set_flashdata('todo', "info");
		}
		redirect(site_url("Admin/".$table));
	}

	public function add_link()
	{
		$this->load->model('Admin_model', '', TRUE);
		$table = $this->input->post('table');
		$id_name = $this->input->post('id_name');
		$data = array();
		$data[$table] = $this->input->post('dbval');
		$data["status"] = $this->input->post('status');
		$data["icon"] = $this->input->post('icon');
		$data["label"] = $this->input->post('label');
		$data["multiple"] = $this->input->post('multiple');
		$data["parent_id"] = $this->input->post('parent_id');
		$data["arrangement"] = $this->input->post('arrangement');
		$id = $this->input->post('id');
		if($id == ""){
			$check = $this->Admin_model->checker($data[$table],$table);
			if($check == 0){
				$this->Admin_model->add($data,$table);
				$this->session->set_flashdata('message', "Data has been added to the list!");
				$this->session->set_flashdata('todo', "success");
			}
			else{
				$this->session->set_flashdata('message', "Duplicate Entry! Cannot add Data!");
				$this->session->set_flashdata('todo', "warning");
			}			
			
		}
		else{
			$this->Admin_model->edit($data,$id,$table,$id_name);
				$this->session->set_flashdata('message', 'Data has been updated!');
				$this->session->set_flashdata('todo', "info");
		}

		redirect(site_url("Admin/".$table));
	}

	public function add_classification()
	{
		$this->load->model('Admin_model', '', TRUE);
		$table = $this->input->post('table');
		$id_name = $this->input->post('id_name');
		$data = array();
		$data[$table] = $this->input->post('dbval');
		$data["status"] = $this->input->post('status');
		$data["code"] = $this->input->post('code');
		$id = $this->input->post('id');
		if($id == ""){
			$check = $this->Admin_model->checker($data[$table],$table);
			if($check == 0){
				$this->Admin_model->add($data,$table);
				$this->session->set_flashdata('message', "Data has been added to the list!");
				$this->session->set_flashdata('todo', "success");
			}
			else{
				$this->session->set_flashdata('message', "Duplicate Entry! Cannot add Data!");
				$this->session->set_flashdata('todo', "warning");
			}			
			
		}
		else{
			$this->Admin_model->edit($data,$id,$table,$id_name);
				$this->session->set_flashdata('message', 'Data has been updated!');
				$this->session->set_flashdata('todo', "info");
		}

		redirect(site_url("Admin/".$table));
	}

	public function delete()
	{
		$this->load->model('Admin_model', '', TRUE);
		$table = $this->input->post('table');
		$id_name = $this->input->post('id_name');
		$id = $this->input->post('id');
		$val = $this->input->post('dbval');
		$this->Admin_model->delete($id,$id_name,$table);
		$this->session->set_flashdata('message', "Data has been deleted!");
		$this->session->set_flashdata('todo', "error");
		redirect(site_url("Admin/".$table));
	}

	public function delete_json()
	{
		$this->load->model('Admin_model', '', TRUE);
		$table = $this->input->post('table');
		$id_name = $this->input->post('id_name');
		$id = $this->input->post('id');
		$val = $this->input->post('dbval');
		$this->Admin_model->delete($id,$id_name,$table);
		$this->session->set_flashdata('success_message', $val . '  has been deleted</div>');
		echo json_encode(1);
	}

	public function add_access(){
		$link_id = $this->input->post('link_id');
		$at_id = $this->input->post('at_id');
		$data = new stdClass();
		$data->link_id = $link_id;
		$data->at_id = $at_id;

 		$this->load->model('Admin_model');
		$this->Admin_model->add($data, 'access');

		//$data = $this->Admin_model->exempted_list($est_id);
		echo json_encode($data->at_id);
	}

	public function delete_link(){
		$a_id = $this->input->post('a_id');

 		$this->load->model('Admin_model');
		$this->Admin_model->delete($a_id, 'a_id','access');

		//$data = $this->Admin_model->exempted_list($est_id);
		echo json_encode('deleted');
	}

	public function delete_any(){
		$id = $this->input->post('id');

 		$this->load->model('Admin_model');
		$this->Admin_model->delete($id, 'id','ad_signatory');

		//$data = $this->Admin_model->exempted_list($est_id);
		echo json_encode('deleted');
	}

	public function get_access(){
		$at_id = $this->input->post('at_id');

 		$this->load->model('Admin_model');

		$data = $this->Admin_model->access_list($at_id);
		echo json_encode($data);
	}

	public function access_type(){
		$this->isAllowed();
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'admin/access_type';
		$this->data['list'] = $this->Admin_model->list('access_type');
		$this->data['links'] = $this->linkGenerator();
		$this->load->view('template/admin', $this->data);
	}

	public function services(){
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'admin/services';
		$this->data['list'] = $this->Admin_model->list('services');
		$this->data['links'] = $this->linkGenerator();
		$this->load->view('template/admin', $this->data);
	}

	public function classification(){
		$this->isAllowed();
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'admin/classification';
		$this->data['list'] = $this->Admin_model->list('classification');
		$this->data['links'] = $this->linkGenerator();
		$this->load->view('template/admin', $this->data);
	}

	public function add_services()
	{
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
		$table = 'services';
		$id_name = "id";
		$data = array();
		$data['description'] = $this->input->post('description');
		$data["status"] = $this->input->post('status');
		$data["default_number"] = $this->input->post('default_number');
		$data["ticketprefix"] = $this->input->post('ticketprefix');
		$data["hex_color"] = $this->input->post('hex_color');
		$id = $this->input->post('id');
		if($id == ""){
			$check = $this->General_model->list_dynamic($table,['description'=>$data['description']]);
			if(!$check){
				$this->Admin_model->add($data,$table);
				$this->session->set_flashdata('message', "Data has been added to the list!");
				$this->session->set_flashdata('todo', "success");
			}
			else{
				$this->session->set_flashdata('message', "Duplicate Entry! Cannot add Data!");
				$this->session->set_flashdata('todo', "warning");
			}			
			
		}
		else{
			$this->Admin_model->edit($data,$id,$table,$id_name);
				$this->session->set_flashdata('message', 'Data has been updated!');
				$this->session->set_flashdata('todo', "info");
		}

		redirect(site_url("Admin/".$table));
	}

	public function windows(){
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'admin/windows';
		$this->data['list'] = $this->Admin_model->windows_list();
		$this->data['services'] = $this->Admin_model->list('services');
		$this->data['links'] = $this->linkGenerator();
		$this->load->view('template/admin', $this->data);
	}

	public function add_windows()
	{
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
		$table = 'windows';
		$id_name = "id";
		$data = array();
		$data['description'] = $this->input->post('description');
		$data["status"] = $this->input->post('status');
		$data["service_id"] = $this->input->post('service_id');
		$data["hex_color"] = $this->input->post('hex_color');
		$id = $this->input->post('id');
		if($id == ""){
			$check = $this->General_model->list_dynamic($table,['description'=>$data['description']]);
			if(!$check){
				$this->Admin_model->add($data,$table);
				$this->session->set_flashdata('message', "Data has been added to the list!");
				$this->session->set_flashdata('todo', "success");
			}
			else{
				$this->session->set_flashdata('message', "Duplicate Entry! Cannot add Data!");
				$this->session->set_flashdata('todo', "warning");
			}			
			
		}
		else{
			$this->Admin_model->edit($data,$id,$table,$id_name);
				$this->session->set_flashdata('message', 'Data has been updated!');
				$this->session->set_flashdata('todo', "info");
		}

		redirect(site_url("Admin/".$table));
	}

	public function link(){
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'admin/link';
		$this->data['list'] = $this->Admin_model->list('link');
		$this->data['links'] = $this->linkGenerator();
		$this->load->view('template/admin', $this->data);
	}

    public function get_links()
	{
		$search = $this->input->POST('search');
		$at_id = $this->input->POST('at_id');
		$this->load->model('Admin_model');
		$data = $this->Admin_model->links_list($search,$at_id);
		echo json_encode($data);
	} 

  	public function students(){
 		// $this->load->model('Dashboard_model');
		$this->load->model('Admin_model', '', TRUE);
		// $this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'admin/master_list';
		$this->data['list'] = $this->Admin_model->list('link');
		$this->load->view('template/admin', $this->data);
  	}

	public function upload_csv(){
		$response = array();
		$upload_dir = "assets/uploads/csv";
		if (!file_exists($upload_dir)) {
    		mkdir($upload_dir, 0777, true);  //create directory if not exist
		}

        if ($_FILES['userfile']['error'] != 0) {
            $response['status'] = 'error';
            $response['message'] = 'File upload error.';
        } else {
            $config['upload_path']   = $upload_dir;
            $config['allowed_types'] = 'xlsx|csv|xls';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('userfile')) {
                $response['status'] = 'error';
                $response['message'] = $this->upload->display_errors();
            } else {
				 $this->db->trans_start(); // Start transaction
                 // File uploaded successfully
                $response['status'] = 'success';
                $response['message'] = 'File uploaded successfully.';
                // Process CSV file if needed
                $fileData = $this->upload->data();
                $filePath = $fileData['full_path'];

                if (($handle = fopen($filePath, "r")) !== FALSE) {
                    $csvData = array();
	                $header = fgetcsv($handle, 1000, ","); // Read the header row

	                // Check if required columns are present
	                $requiredColumns = array('Acad Group','Career','Term','ID','Campus ID', 'Last', 'First Name', 'Middle');
					$missingColumns = array_diff($requiredColumns, $header);
	                if (count(array_diff($requiredColumns, $header)) > 0) {
	                    $response['status'] = 'error';
	                    $response['message'] = 'Required columns are missing in the CSV file: ' . implode(', ', $missingColumns);
	                    $this->db->trans_rollback(); // Rollback transaction
	                } else {
	                	$new = 0;
	                	$update = 0;
	                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	                    	$array_data = array(
	                            'acad_group' => $data[0],
	                            'career' => $data[1],
	                            'term' => $data[2],
	                            'sais_id' => $data[3],
	                            'campus_id' => $data[4],
	                            'last_name' => $data[5],
	                            'first_name' => $data[6],
	                            'middle_name' => $data[7]
	                        );
	                    	$is_updated = $this->General_model->update_where($array_data,["campus_id"=>$data[4]],"students");
	                    	if($is_updated){
	                    		$update++;
		                    }else{
	                    		$new++;
		                        // Add each row to $csvData array
		                        $csvData[] = $array_data;
		                    }
	                    }
	                    // Insert batch into database
	                    if (!empty($csvData)) {
	                    	$response['new_student'] = $new;
	                    	$response['update'] = $update;
	                        $this->db->insert_batch('students', $csvData);
	                    }
                	}//end else (if CSV column is same)
					fclose($handle);
                } else {
                    // Error opening file
                    $response['status'] = 'error';
                    $response['message'] = 'Error opening CSV file.';
                    $this->db->trans_rollback(); // Rollback transaction
                }
				 $this->db->trans_complete(); // Complete transaction

	            if ($this->db->trans_status() === FALSE) {
	                // Transaction failed
	                $response['status'] = 'error';
	                $response['message'] = 'Database transaction failed.';
	            }
            }
        }

        // Send JSON response
        echo json_encode($response);	
   	}

   	public function get_students(){
    	$this->load->model('Admin_model');
    	$this->load->model('General_model');
        $list = $this->Admin_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
	        $row = array();
	        $row[] = $val->sais_id;
	        $row[] = $val->campus_id;
	        $row[] = $val->term;
	        $row[] = $val->last_name;
	        $row[] = $val->first_name;
	        $row[] = $val->middle_name;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Admin_model->count_all(),
                        "recordsFiltered" => $this->Admin_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
   	}

    public function forbidden()
	{
		$this->load->view('errors/index.html');
	}

	public function check_pass(){
		$curr_pass = $this->input->post('curr_pass');

 		$this->load->model('Admin_model');
		$userdata = $this->Admin_model->search_list("users",$this->session->userdata(SESSION_DATA)->uid,"uid");
		$data = (password_verify($curr_pass, $userdata[0]->password));


		//$data = $this->Admin_model->exempted_list($est_id);
		echo json_encode($data);
	}

	public function change_password(){
		$this->load->model('Users_model', '', TRUE);
		$uid = $this->session->userdata(SESSION_DATA)->uid;
		$data = array();

		$new_password = $this->input->post("new_pass");
		$data["password"] = password_hash($new_password, PASSWORD_BCRYPT);
		$this->Users_model->edit_users($data,$uid);
		echo json_encode(1);
	}
}