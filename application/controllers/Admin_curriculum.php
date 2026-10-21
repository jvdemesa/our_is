<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_curriculum extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
	}

	public function index(){
		$this->isAllowed();
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'admin/curriculum/index';
		$this->data['academic_program'] = $this->Admin_model->list("academic_program");
		$this->data['subjects'] = $this->Admin_model->list("subjects");
		$this->data['links'] = $this->linkGenerator();
		$this->data['id_name'] = "id";
		$this->data['table'] = "curriculum";
		$this->data['labels'] = "Curriculum";
		$this->data['dbval'] = "curriculum";
		$this->load->view('template/admin', $this->data);
	}

	public function save()
	{
		$this->load->model('Admin_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
		$table = $this->input->post('table');
		$id_name = $this->input->post('id_name');
		$data = array();
		$col = $this->input->post('dbval');
		$id = $this->input->post('id');
		$status = $this->input->post('status');
		$subjects = $this->input->post('subjects');
		$year = $this->input->post('year');
		$ap_id = $this->input->post('ap_id');
		$json_data = new stdClass();
		if($id==""){
			$check = $this->General_model->list_dynamic("curriculum_year",["ap_id"=>$ap_id, "year"=>$year]);
			if(!$check){
				$data = ["ap_id"=>$ap_id, "year"=>$year, "status"=>$status];
				$inserted_id = $this->Admin_model->add($data,"curriculum_year");
				$cur_data = array();
				foreach($subjects as $key=>$val){
					$cur_data[] = ["cy_id"=>$inserted_id, "subject_id"=>$val];
				}
				$this->General_model->insert_batch("curriculum",$cur_data);
				$json_data->message = "Data has been added to the list!";
				$json_data->error = 0;
			}
			else{
				$json_data->message = "Duplicate curriculum year for academic program! Cannot add Data!";
				$json_data->error = 1;
			}
		}else{
			$check = $this->General_model->list_dynamic("curriculum_year",["ap_id"=>$ap_id, "year"=>$year, "id !="=>$id]);		
			if(!$check){
				$data = ["ap_id"=>$ap_id, "year"=>$year, "status"=>$status];
				$this->Admin_model->edit($data,$id,"curriculum_year","id");
				$this->General_model->delete('curriculum',["cy_id"=>$id]);
				foreach($subjects as $key=>$val){
					$cur_data[] = ["cy_id"=>$id, "subject_id"=>$val];
				}
				$this->General_model->insert_batch("curriculum",$cur_data);
				$json_data->message = "Data has been updated!";
				$json_data->error = 0;
			}else{
				$json_data->message = "Cannot update data. Data already found in the list!";
				$json_data->error = 1;
			}
		}			

		echo json_encode($json_data);
	}

	public function delete(){
		$this->load->model('Admin_model', '', TRUE);
		$id = $this->input->post("id");
		$id_name = $this->input->post("id_name");
		$table = $this->input->post("table");
		$this->Admin_model->delete($id,$id_name,'curriculum_year');
		$this->Admin_model->delete($id,'cy_id','curriculum');
        $jsondata['message']=  'Curriculum has been deleted.';
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
    	$this->load->model('Admin_curriculum_model');
    	$this->load->model('General_model');
        $list = $this->Admin_curriculum_model->get_list();
        $data = array();
        foreach ($list as $val) {
        	$status_label = ($val->status=='0')?'ENABLE':'DISABLE';
            $row = array();
            $row[] = $val->id;
            $row[] = $val->college."($val->college_code)";
            $row[] = $val->academic_program."( <span class='text-danger'>$val->ap_code</span> )";
            $row[] = $val->subjects;
            $row[] = $val->year;
            $row[] = $status_label;
    		$actions='<center>
	                    <div class="btn-group">
	                        <button type="button" 
	                        class="btn btn-info btn-sm edit tooltip_btn" 
	                        title="UPDATE" 
	                        cur_id="'.$val->id.'"  
	                        ap_id="'.$val->ap_id.'"  
	                        academic_program="'.$val->academic_program.'"  
	                        subjects="'.$val->subjects.'"  
	                        subject_ids="'.$val->subject_ids.'"  
	                        year="'.$val->year.'"  
	                        status="'.$val->status.'" 
	                        >
	                          <i class="fa fa-edit"></i>
	                        </button>
	                        <button type="button" 
	                        class="btn btn-danger btn-sm delete tooltip_btn" 
	                        title="DELETE" 
	                        cur_id="'.$val->id.'"  
	                        ap_id="'.$val->ap_id.'"  
	                        academic_program="'.$val->academic_program.'"  
	                        subjects="'.$val->subjects.'" 
	                        subject_ids="'.$val->subject_ids.'"  
	                        year="'.$val->year.'"  
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
                        "recordsTotal" => $this->Admin_curriculum_model->count_all(),
                        "recordsFiltered" => $this->Admin_curriculum_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
	}
}