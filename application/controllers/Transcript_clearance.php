<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Transcript_clearance extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Reclassification_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
 		// $this->load->model('Reclassification_model');
		$this->load->model('Admin_model', '', TRUE);
		$this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'transcript_clearance/index';
		$this->data['list'] = $this->Admin_model->list('link');
		$this->load->view('template/admin', $this->data);
  	}

    public function upload(){
        $this->load->model('Transcript_clearance_model', '', TRUE);
        $upload_dir = "assets/uploads/csv/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);  //create directory if not exist
        }

        $config['upload_path'] = $upload_dir;
        $config['allowed_types'] = 'xlsx|xls|csv';
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());
            print_r($error);
        } else {
            $data = $this->upload->data();
            $file_path = $upload_dir . $data['file_name'];

            $spreadsheet = IOFactory::load($file_path);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $last_date_approved = $this->Transcript_clearance_model->last_date_approved();
            foreach ($sheetData as $key=>$row) {
                $insert_data = new stdClass();
                $fullname = TRIM($row['A']);
                $student_no = TRIM($row['B']);
                $academic_program = TRIM($row['C']);
                $college = TRIM($row['D']);
                // $doa = TRIM($row['F']);
                // $last_enrolled = TRIM($row['G']);
                $date_approved = date('Y-m-d', strtotime($row['H']));
                $date_today = date("Y-m-d");
                //upload cleared students after the last date approved
                if($key > 1){
                    $tr_clearance[]= array("fullname"=>$fullname, 
                                    "student_no"=>$student_no, 
                                    "academic_program"=>$academic_program, 
                                    "college"=>$college, 
                                    // "doa"=>$doa, 
                                    // "last_enrolled"=>$last_enrolled, 
                                    "date_approved"=>$date_approved,
                                );
                }
            }
            if(isset($tr_clearance)){
              $inserted = $this->Transcript_clearance_model->insert_batch_if_not_exists($tr_clearance, "tr_clearance", 'student_no');
              // $this->General_model->insert_batch('tr_clearance',$tr_clearance);
            }
            unlink($file_path); // Delete the uploaded file
            echo json_encode($inserted);
        }
    }

   	public function get_cleared_list(){
    	$this->load->model('Transcript_clearance_model');
    	$this->load->model('General_model');
        $list = $this->Transcript_clearance_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
        	// $action = "<center>
         //                    <div class='btn-group'>
         //                        <button class='btn btn-warning btn-sm download  tool_tip' batch_id='$val->id' title='DOWNLOAD CSV'>
         //                            <i class='fa fa-download'></i>
         //                        </button>
         //                        <button class='btn btn-info btn-sm view  tool_tip' batch_id='$val->id' title='VIEW BATCH LIST'>
         //                            <i class='fa fa-eye'></i>
         //                        </button>
         //                        <button class='btn btn-danger btn-sm delete  tool_tip' batch_id='$val->id' title='DELETE BATCH'>
         //                            <i class='fa fa-trash'></i>
         //                        </button>
         //                    </div>
        	// 			</center>";
        $row = array();
        $row[] = $val->fullname;
        $row[] = $val->student_no;
        $row[] = $val->academic_program;
        $row[] = $val->college;
        $row[] = $val->date_approved;
        $row[] = "";
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Transcript_clearance_model->count_all(),
                        "recordsFiltered" => $this->Transcript_clearance_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
   	}

   	public function delete_batch(){
   		$batch_id = $this->input->post('batch_id');
   		$this->General_model->delete('student_batch',['id'=>$batch_id]);
   		$this->General_model->delete('students_raw',['batch_id'=>$batch_id]);
   		echo json_encode(1);
   	}
}
