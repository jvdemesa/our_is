<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Equota extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
	}

	public function index(){
		$this->isAllowed();
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'equota/index';
		$this->data['academic_program'] = $this->Admin_model->list("academic_program");
		$this->data['links'] = $this->linkGenerator();
		$this->load->view('template/admin', $this->data);
	}

	public function save_draft()
	{
		$this->load->model('General_model', '', TRUE);
		$id = $this->input->post("id");
		$equota = $this->input->post("equota");
		for($i =0 ; $i<count($id); $i++){
			$this->General_model->update(['equota'=>$equota[$i]],$id[$i],'equota','id');
		}
		echo json_encode(1);
	}

	public function finalize()
	{
		$this->load->model('General_model', '', TRUE);
		$id = $this->input->post("id");
		$equota = $this->input->post("equota");
		for($i =0 ; $i<count($id); $i++){
			$this->General_model->update(['equota'=>$equota[$i], "is_finalized"=>1],$id[$i],'equota','id');
		}
		echo json_encode(1);
	}

	public function delete(){
		$this->load->model('Admin_model', '', TRUE);
		$id = $this->input->post("id");
		$this->Admin_model->delete($id,"id",'equota');
        $jsondata['message']=  'Curriculum has been deleted.';
		echo json_encode($jsondata);
	}

	public function generate() {
		$this->load->model('Equota_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
		$list = $this->Equota_model->list_ap();
		$data = array();
		foreach($list as $key=>$val){
			$col = new stdClass();
			$col->ap_id = $val->ap_id;
			$col->year = $this->input->post("year");
			$col->sem = $this->input->post("sem");
			$col->is_finalized = 0;
			$data[] = $col;
		}
		$this->General_model->insert_batch('equota',$data);
        echo json_encode(1);
	}

	public function get_list() {
		$this->load->model('Equota_model', '', TRUE);
		$where = new stdClass();
		$where->year = $this->input->post("year");
		$where->sem = $this->input->post("sem");
		$data = $this->Equota_model->list($where);
        echo json_encode($data);
	}


	public function get_nf_list() {
		$this->load->model('Equota_model', '', TRUE);
		$where = new stdClass();
		$where->year = $this->input->post("year");
		$where->sem = $this->input->post("sem");
		$data = $this->Equota_model->nf_list($where);
		$json_data = array();
		if($data){
			foreach($data as $val){
				$where = new stdClass();
				$where->year = $val->year;
				$where->sem = $val->sem;
				$where->date_uploaded = $val->date_uploaded;
	            $subject_cap = $this->Equota_model->check_nf_equota($where);
	            $regist_status = $this->General_model->list_dynamic("regist_section_status",["year"=>$this->input->post("year"),"sem"=>$this->input->post("sem")]);
	            if($regist_status){
	            	$regist_status=$regist_status[0]->status;
	            }else{
	            	$regist_status=0;
	            }
	            if($subject_cap){
	            	$json_data[] = array("year"=>$val->year, "sem"=>$val->sem, "date_uploaded"=>$val->date_uploaded, "underload"=>count($subject_cap),"regist_status"=>$regist_status);
	            }else{
	            	$json_data[] = array("year"=>$val->year, "sem"=>$val->sem, "date_uploaded"=>$val->date_uploaded, "underload"=>0,"regist_status"=>$regist_status);
	            }
			}
		}else{
			$json_data = false;
		}
        echo json_encode($json_data);
	}

	public function download_nf_template()
	{
	    $this->load->helper('download');
	    force_download(
	        FCPATH.'assets/uploads/nf_sched/Sample_Template_Upload_NF.csv',
	        NULL
	    );
	}

	public function generate_students(){
        $this->load->model('Equota_model', '', TRUE);
        $this->load->model('General_model', '', TRUE);
        $where = new stdClass();
        $year = "2025";//$this->input->post("year");
        $sem = "1";//$this->input->post("sem");
        $where->year = $year;
        $where->sem = $sem;
        $data = $this->Equota_model->dummy_students($where);
        $populate = [];
        $key = 1;
        $num_start = 60000;
        foreach($data as $val){
            for($i=1;$i<=$val->equota;$i++){
                $stud_num =$num_start+$key;
	        	$populate[] = [	"student_no"=>"$year-$stud_num",
	        					"name"=>"$val->ap_code-$i",
	        					"cy_id"=>$val->cy_id,
	        					"max_units"=>18,
	        					"year"=>$year,
	        					"sem"=>$sem,
	        				];
		        $key++;
	        }
        }
		$this->db->trans_start();

		$this->General_model->delete("regist_students", [
		    "year" => $year,
		    "sem"  => $sem
		]);

		$this->General_model->insert_batch("regist_students", $populate);

		$this->db->trans_complete();

		return $this->db->trans_status();
    }

    public function generate_regist_sections(){
        $this->load->model('Equota_model', '', TRUE);
        $year = $this->input->post("year");
        $sem = $this->input->post("sem");
        $date_uploaded = $this->input->post("date_uploaded");
        $where = new stdClass();
        $where->year=$year;
        $where->sem=$sem;
        //for generation of students 1st
        $data = $this->Equota_model->dummy_students($where);
        $populate = [];
        $key = 1;
        $num_start = 60000;
        foreach($data as $val){
            for($i=1;$i<=$val->equota;$i++){
                $stud_num =$num_start+$key;
	        	$populate[] = [	"student_no"=>"$year-$stud_num",
	        					"name"=>"$val->ap_code-$i",
	        					"cy_id"=>$val->cy_id,
	        					"max_units"=>18,
	        					"year"=>$year,
	        					"sem"=>$sem,
	        				];
		        $key++;
	        }
        }        
        //add where clause for nf_sched
        $where->date_uploaded=$date_uploaded;
        $nf_data = $this->General_model->list_dynamic("nf_sched",$where);
        //get all subject
        $nf_subjects = $this->General_model->list_dynamic('subjects',['status'=>0]);
        //convert all subject to array for use in in_array
        $subjects_list = array_column($nf_subjects, 'code');
        if($nf_data){
            $sections = [];
            $section_schedules = [];
            foreach ($nf_data as $key=>$row) {
                if($row->subject === "GI ."){
                    $is_gi = 1;
                }else{
                    $is_gi = 0;
                }
                $subject_code = $row->subject;
                $section = $row->section;
                $capacity = $row->capacity;
                $time = $row->time;
                $days = $row->days;
                $class_no = $row->class_no;
                $sais_section = $row->sais_section;
                $sais_day = $row->sais_day;
                $sais_time = $row->sais_time;
                if(in_array($subject_code,$subjects_list)){
                	$section_group_name = str_replace(' ', '', $subject_code)."_".$key."_".$sem."_".$year;
                	$sections[$key] = [ "subject"		=>	$subject_code,
                					"section_name"		=>	$section,
                					"max_capacity"		=>	$capacity,
                					"section_group_name"=>	$section_group_name,
                					"year"=>$year,
									"sem"=>$sem,
									"is_gi"=>$is_gi,
                					];
	                $daysExpanded = $this->parseDays($days);
	                // var_dump($daysExpanded);
	                // exit();
					$time = $row->sais_time;

					if (!empty($time) && str_contains($time, '-')) {
					    [$start, $end] = explode('-', $time, 2);
					} else {
					    $start = null;
					    $end = null;
					}
					$start24 = date("H:i", strtotime($start));
					$end24   = date("H:i", strtotime($end));

	                foreach ($daysExpanded as $day) {
	                    $sections_schedules[] = [
	                        'day'        => $day,
	                        'start_time' => $start24,
	                        'end_time'   => $end24,
        					"section_group_name"	=>	$section_group_name,
        					"date_updated"	=>	date("Y-m-d H:i:s"),
        					"update_by"	=>	$this->session->userdata(SESSION_DATA)->uid,
	                    ];
	                }


                }
            }
			// var_dump($sections_schedules);
			// exit();

			$this->db->trans_start();
				//for generation of students elete all students first in the same year and sem
				$this->General_model->delete("regist_students", [
				    "year" => $year,
				    "sem"  => $sem
				]);
				//save students
				$this->General_model->insert_batch("regist_students", $populate);
				//for generation of sections delete all sections first
				$this->Equota_model->delete_regist_section($year,$sem);
	            //insert new sections
	            $this->General_model->insert_batch("regist_sections",$sections);
	            $this->General_model->insert_batch("regist_section_schedules",$sections_schedules);
			$this->db->trans_complete();
			$insert_status = $this->db->trans_status();
			if($insert_status){
				$status_data = ["year"=>$year,
								"sem"=>$sem,
								"status"=>1
								];
				$this->General_model->add_data('regist_section_status',$status_data);
			}

			return $this->db->trans_status();
        }
    }

    private function parseDays($daysStr)
    {
        $daysStr = trim($daysStr);

        // Direct full-day names
        $fullDays = ['Mon','Tues','Wed','Thurs','Fri','Sat','Sun'];
        if (in_array($daysStr, $fullDays)) {
            return [$daysStr];
        }

        // Match tokens in correct order
        preg_match_all('/Th|Su|M|T|W|F|S/', $daysStr, $matches);

        $map = [
            'M'  => 'Mon',
            'T'  => 'Tues',
            'W'  => 'Wed',
            'Th' => 'Thurs',
            'F'  => 'Fri',
            'S'  => 'Sat',
            'Su' => 'Sun',
        ];

        $result = [];

        foreach ($matches[0] as $token) {
            if (isset($map[$token])) {
                $result[] = $map[$token];
            }
        }

        return $result;
    } 
}