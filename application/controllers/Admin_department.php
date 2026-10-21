<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_department extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
	}

	public function index(){
		$this->isAllowed();
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'admin/departments/index';
		$this->data['links'] = $this->linkGenerator();
		$this->data['id_name'] = "id";
		$this->data['table'] = "departments";
		$this->data['labels'] = "Departments";
		$this->data['colleges'] = $this->General_model->list("colleges");
		$this->data['subjects'] = $this->General_model->list("subjects");
		$this->load->view('template/admin', $this->data);
	}

	public function save(){
		$this->load->model('Admin_model', '', TRUE);
		$response = array();
        $data = array();
        $data["department"] = $this->input->post("department");
        $data["code"] = $this->input->post("code");
		$data["status"] = $this->input->post('status');
		$data["college_id"] = $this->input->post('college_id');
		$subject_id = $this->input->post('subject_id');
        $id = $this->input->post("id");
        $table = $this->input->post("table");
        $id_name = $this->input->post("id_name");
        if($id){
			$this->Admin_model->edit($data,$id,$table,$id_name);
        }else{
			$id = $this->Admin_model->add($data,$table);
        }
        //remove all subject of the department
		$this->Admin_model->edit(["department_id"=>"0"],$id,"subjects","department_id");
        if($subject_id){
	        foreach($subject_id as $val){
	        	//update subjects department
				$this->Admin_model->edit(["department_id"=>$id],$val,"subjects","id");
	        }
	    }
		echo json_encode($response);
	}

	public function delete(){
		$this->load->model('Admin_model', '', TRUE);
		$id = $this->input->post("id");
		$id_name = $this->input->post("id_name");
		$table = $this->input->post("table");
		$this->Admin_model->delete($id,$id_name,$table);
        $jsondata['message']=  'department has been deleted.';
		echo json_encode($jsondata);
	}

	public function get_subjects(){
		$this->load->model('Admin_model', '', TRUE);
		$id = $this->input->post("id");
		$table = "subjects";
        $subjects=  $this->General_model->list_dynamic($table,["department_id"=>$id]);
        $jsondata['data']= [];
        if($subjects){
	        foreach ($subjects as $row) {
	            $jsondata['data'][] = $row->id;
	        }
    	}
		echo json_encode($jsondata);
	}

	public function toggle_media(){
		$this->load->model('General_model', '', TRUE);
		$id = $this->input->post("id");
		$id_name = $this->input->post("id_name");
		$table = $this->input->post("table");
		$status = $this->input->post("status")==0?1:0;
		$data = ["status"=>$status];
		$jsondata = array();
		if($this->General_model->update($data,$id,$table,$id_name)){
			$label = $status==1?"DISABLED":"ENABLED";
			$jsondata["message"] = "Media has been ".$label.".";
		}else{
			$jsondata["message"] = "Media not found";
		}
		echo json_encode($jsondata);
	}

	public function get_list() {
    	$this->load->model('Admin_department_model');
    	$this->load->model('General_model');
        $list = $this->Admin_department_model->get_list();
        $data = array();
        foreach ($list as $val) {
        	$status_label = ($val->status=='0')?'ENABLE':'DISABLE';
            $row = array();
            $row[] = $val->id;
            $row[] = $val->department;
            $row[] = $val->code;
            $row[] = $status_label;
    		$actions='<center>
	                    <div class="btn-group">
	                        <button type="button" 
	                        class="btn btn-info btn-sm edit tooltip_btn" 
	                        title="UPDATE" 
	                        id="'.$val->id.'"  
	                        department="'.$val->department.'"  
	                        college_id="'.$val->college_id.'"  
	                        code="'.$val->code.'"  
	                        status="'.$val->status.'" 
	                        >
	                          <i class="fa fa-edit"></i>
	                        </button>
	                        <button type="button" 
	                        class="btn btn-danger btn-sm delete tooltip_btn" 
	                        title="DELETE" 
	                        id="'.$val->id.'"  
	                        department="'.$val->department.'"  
	                        code="'.$val->code.'"  
	                        status="'.$val->status.'" 
	                        >
	                          <i class="fa fa-trash"></i>
	                        </button>
                      	</div> 
                    </center>
					';
			$row[] = $actions;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Admin_department_model->count_all(),
                        "recordsFiltered" => $this->Admin_department_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
	}
}