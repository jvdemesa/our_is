<?php

use Google\Service\BigtableAdmin\Split;

defined('BASEPATH') OR exit('No direct script access allowed');
class Import_data extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		// $this->load->model('Dashboard_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
 		// $this->load->model('Dashboard_model');
		$this->load->model('Admin_model', '', TRUE);
		// $this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'dashboard/index';
		$this->data['list'] = $this->Admin_model->list('link');
		$this->load->view('template/admin', $this->data);
  	}

	public function csv(){
		$this->load->model('Admin_model', '', TRUE);
		$response = array();
		$upload_dir = "assets/import_data/csv";
		if (!file_exists($upload_dir)) {
    		mkdir($upload_dir, 0777, true);  //create directory if not exist
		}

        if ($_FILES['userfile']['error'] != 0) {
            $response['status'] = 'error';
            $response['message'] = 'File upload error.';
        } else {
            $config['upload_path']   = $upload_dir;
            $config['allowed_types'] = 'csv';
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
                $table= $fileData['file_name'];
                if (($handle = fopen($filePath, "r")) !== FALSE) {
                    $csvData = array();
                    $col = array();
	                $header = fgetcsv($handle, 1000, ","); // Read the header row
	                $i=0;
	                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    		$array_data = array();
	                        // Add each row to $csvData array
	                        foreach($data as $key=>$val){
	                        	 $array_data[$header[$key]] = $val;
                        	}
                        	$csvData[] = $array_data;
	                    }
	                    // Insert batch into database
	                    if (!empty($csvData)) {
	                        $this->db->insert_batch($table, $csvData);
	                    }
                	}//end else (if CSV column is same)
					fclose($handle);
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

   	public function get_students_batch(){
    	$this->load->model('Dashboard_model');
    	$this->load->model('General_model');
        $list = $this->Dashboard_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
        	$action = "<center>
                            <div class='btn-group'>
                                <button class='btn btn-warning btn-sm download  tool_tip' batch_id='$val->id' title='DOWNLOAD CSV'>
                                    <i class='fa fa-download'></i>
                                </button>
                                <button class='btn btn-info btn-sm view  tool_tip' batch_id='$val->id' title='VIEW BATCH LIST'>
                                    <i class='fa fa-eye'></i>
                                </button>
                                <button class='btn btn-danger btn-sm delete  tool_tip' batch_id='$val->id' title='DELETE BATCH'>
                                    <i class='fa fa-trash'></i>
                                </button>
                            </div>
        				</center>";
        $row = array();
        $row[] = $val->id;
        $row[] = $val->uploaded_by;
        $row[] = $val->date;
        $row[] = $val->time;
        $row[] = $val->batch_total;
        $row[] = $action;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Dashboard_model->count_all(),
                        "recordsFiltered" => $this->Dashboard_model->count_filtered(),
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

   	/**
   	 * Upload Student Admission / Transcript Info (new Excel format)
   	 * - Accepts .xls/.xlsx
   	 * - Validates headers and rows, detects duplicates, uses transaction
   	 */
   	public function upload_student_admission_info(){
   		$this->load->helper('file');
   		$response = array('status'=>'error','message'=>'Unknown error');

   		// upload dir: use system temp directory outside the public web root
   		$upload_dir = sys_get_temp_dir();

   		if (!isset($_FILES['userfile_admission'])){
   			$response['message'] = 'No file uploaded.';
   			echo json_encode($response); return;
   		}

   		$file = $_FILES['userfile_admission'];
   		if ($file['error'] !== UPLOAD_ERR_OK) {
   			$response['message'] = 'File upload error code: '.$file['error'];
   			echo json_encode($response); return;
   		}

   		// Save uploaded file to temp dir
   		$filename = time() . "_" . preg_replace('/[^a-zA-Z0-9_\.\-]/','_', $file['name']);
   		$dest = $upload_dir . DIRECTORY_SEPARATOR . $filename;
   		if (!move_uploaded_file($file['tmp_name'], $dest)){
   			$response['message'] = 'Failed to save uploaded file.';
   			echo json_encode($response); return;
   		}
   		// Ensure uploaded file is removed at script shutdown (covers early returns and exceptions)
   		register_shutdown_function(function() use (&$dest){ if(isset($dest) && file_exists($dest)){ @unlink($dest); } });

   		// Use PhpSpreadsheet
   		require_once(APPPATH.'../vendor/autoload.php');

   		try{
   			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($dest);
   		}catch(Exception $e){
   			$response['message'] = 'Failed to read spreadsheet: '.$e->getMessage();
   			echo json_encode($response); return;
   		}

   		$sheet = $spreadsheet->getActiveSheet();
   		$rows = $sheet->toArray(null, true, true, true); // preserve empty cells, indexed by A,B,C
   		if(empty($rows) || count($rows) < 1){
   			$response['message'] = 'Empty spreadsheet.';
   			echo json_encode($response); return;
   		}

   		// header row = first row
   		$headerRow = array_shift($rows);

   		// normalize headers: trim, uppercase, remove trailing dot
   		$normalizedHeaders = array();
   		foreach($headerRow as $col => $val){
   			$h = trim($val);
   			$h = preg_replace('/\s+/', ' ', $h);
   			$hUpper = strtoupper(rtrim($h, '. '));
   			$normalizedHeaders[$col] = $hUpper;
   		}

   		// mapping rules (normalized header => target key)
   		$map = array(
   			'STUDENT NO' => 'student_no',
   			'STUDENT NO.' => 'student_no',
   			'STUD NO' => 'student_no',
   			'DEGREE PROGRAM' => 'degree',
   			'GENDER' => 'sex',
   			'DATE OF BIRTH' => 'birthday',
   			'PLACE OF BIRTH' => 'pob',
   			'SEMESTER ADMITTED' => 'sem_admitted',
   			"FATHER'S NAME" => 'father_name',
   			"FATHERS LAST NAME" => 'father_last_name',
   			"MOTHER'S NAME" => 'mother_name',
   			"MOTHERS LAST NAME" => 'mother_last_name'
   		);

   		// build column -> key map
   		$colKeyMap = array();
   		foreach($normalizedHeaders as $col => $h){
   			if(isset($map[$h])){
   				$colKeyMap[$col] = $map[$h];
   			}
   		}

   		// required keys
   		$requiredKeys = array('student_no','degree','sex','birthday','sem_admitted');
   		$foundKeys = array_values($colKeyMap);
   		foreach($requiredKeys as $rk){
   			if(!in_array($rk, $foundKeys)){
   				$response['message'] = 'Required header missing: '. $rk;
   				echo json_encode($response); return;
   			}
   		}

   		// ensure DB table exists
   		$table = 'student_transcript_info';

   		// Check student_no column exists; do NOT modify schema here per requirements
   		$has_student_no = $this->db->query("SHOW COLUMNS FROM `{$table}` LIKE 'student_no'")->num_rows() > 0;
   		if(!$has_student_no){
   			$response['message'] = 'Target table is missing the student_no column. Please add the column before importing.';
   			echo json_encode($response); return;
   		}

   		// Parse rows and validate
   		$total_rows = 0;
   		$import_rows = array();
   		$invalid_rows = array();
   		$seen_in_file = array();
   		$file_duplicates = array();

   		$rowNumber = 1; // header was row 1
   		foreach($rows as $row){
   			$rowNumber++;
   			// build associative row by mapped keys
   			$assoc = array();
   			foreach($colKeyMap as $col => $key){
   				$val = isset($row[$col]) ? $row[$col] : null;
   				// trim and normalize
   				if(is_string($val)) $val = trim($val);
   				$assoc[$key] = $val;
   			}

   			// skip blank rows (all mapped values empty)
   			$allEmpty = true;
   			foreach($assoc as $v){ if($v !== null && $v !== ''){ $allEmpty = false; break; } }
   			if($allEmpty) continue;

   			$total_rows++;

   			// student_no must be present
   			$student_no = isset($assoc['student_no']) ? (string)$assoc['student_no'] : '';
   			$student_no = trim($student_no);
   			if($student_no === ''){
   				$invalid_rows[] = array('row'=>$rowNumber,'error'=>'Missing student number');
   				continue;
   			}

   			// preserve leading zeros: ensure string
   			$student_no = (string)$student_no;

   			// duplicate within file
   			if(in_array($student_no, $seen_in_file)){
   				$file_duplicates[] = $student_no;
   				$invalid_rows[] = array('row'=>$rowNumber,'error'=>'Duplicate student number in file: '.$student_no);
   				continue;
   			}
   			$seen_in_file[] = $student_no;

   			// birthday handling: Excel serial or date string
   			$dob = null;
   			if(isset($assoc['birthday']) && $assoc['birthday'] !== ''){
   				$val = $assoc['birthday'];
   				if(is_numeric($val)){
   					try{ $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val); $dob = $dt->format('Y-m-d'); }
   					catch(Exception $e){ $dob = null; }
   				} else {
   					// try parse
   					$ts = strtotime($val);
   					if($ts !== false && $ts !== -1) $dob = date('Y-m-d', $ts);
   				}
   			}

   			// if birthday required but invalid
   			if($dob === null){
   				$invalid_rows[] = array('row'=>$rowNumber,'error'=>'Invalid or missing date of birth');
   				continue;
   			}

   			// map fathers/mothers name fields: combine if both present
   			$fathers = '';
   			if(isset($assoc['father_name'])) $fathers = trim($assoc['father_name']);
   			if(isset($assoc['father_last_name']) && trim($assoc['father_last_name'])!==''){
   				$fathers = trim($fathers . ' ' . $assoc['father_last_name']);
   			}
   			$mothers = '';
   			if(isset($assoc['mother_name'])) $mothers = trim($assoc['mother_name']);
   			if(isset($assoc['mother_last_name']) && trim($assoc['mother_last_name'])!==''){
   				$mothers = trim($mothers . ' ' . $assoc['mother_last_name']);
   			}
			if(isset($assoc['sem_admitted'])) {
				$sem_ay = explode(', ', $assoc['sem_admitted']);
				if(count($sem_ay) < 2){
					if(isset($sem_ay[0]) && preg_match('/^\d{4}-\d{4}$/', trim($sem_ay[0]))){
						$sem_ay[1] = $sem_ay[0];
						$sem_ay[0] = null;
					}else if(isset($sem_ay[0]) && !preg_match('/^\d{4}-\d{4}$/', trim($sem_ay[0]))){
						$sem_ay[1] = null;
						$sem_ay[0] = $sem_ay[0];
					}else{
						$sem_ay[0] = null;
						$sem_ay[1] = null;
					}
				}
				$ay_admitted = $sem_ay[1];
				$sem_admitted = $sem_ay[0];
				if(isset($sem_admitted)) {
					preg_match_all('/\b(?:FIRST|1ST)\s+SEMESTER\b|\b(?:SECOND|2ND)\s+SEMESTER\b/i', $sem_admitted, $semester_matches);
					$semesters = array();
					foreach($semester_matches[0] as $semester) {
						$normalized_semester = preg_match('/^(?:FIRST|1ST)/i', $semester) ? '1st Semester' : '2nd Semester';
						if(!in_array($normalized_semester, $semesters)) {
							$semesters[] = $normalized_semester;
						}
					}
					$sem_admitted = implode(' and ', $semesters);
				}
			}
   			$insertRow = array(
   				'student_no' => $student_no,
   				'degree' => isset($assoc['degree']) ? $assoc['degree'] : null,
   				'sex' => isset($assoc['sex']) ? $assoc['sex'] : null,
   				'birthday' => $dob,
   				'pob' => isset($assoc['pob']) ? $assoc['pob'] : null,
   				'fathers_name' => $fathers,
   				'mothers_name' => $mothers,
   				'sem_admitted' => isset($sem_admitted) ? $sem_admitted : null,
				'ay_admitted' => isset($ay_admitted) ? $ay_admitted : null
   			);

   			$import_rows[] = $insertRow;
   		}

   		// if no rows to import
   		if(empty($import_rows)){
   			$response['message'] = 'No valid rows to import.';
   			$response['status'] = 'error';
   			$response['total_rows'] = $total_rows;
   			$response['invalid_rows'] = $invalid_rows;
   			echo json_encode($response); return;
   		}

   		// Check existing student numbers in DB
   		$studentNos = array_column($import_rows, 'student_no');
   		$chunks = array_chunk($studentNos, 200);
   		$existing = array();
   		foreach($chunks as $c){
   			$in = implode("','", array_map(function($v){return $this->db->escape_str($v);}, $c));
   			$q = $this->db->query("SELECT student_no FROM `{$table}` WHERE student_no IN ('".$in."')");
   			foreach($q->result() as $r) $existing[] = $r->student_no;
   		}

   		// filter import_rows to exclude existing
   		$to_insert = array();
   		$duplicates_skipped = 0;
   		foreach($import_rows as $r){
   			if(in_array($r['student_no'], $existing)){
   				$duplicates_skipped++;
   				continue;
   			}
   			$to_insert[] = $r;
   		}

   		// insert with explicit transaction control
   		$this->db->trans_begin();
   		$imported_count = 0;
   		if(!empty($to_insert)){
   			$imported_count = $this->General_model->insert_batch($table, $to_insert);
   		}

   		if ($this->db->trans_status() === FALSE) {
   			$this->db->trans_rollback();
   			$response['status'] = 'error';
   			$response['message'] = 'Database transaction failed.';
   		} else {
   			$this->db->trans_commit();
   			$response['status'] = 'success';
   			$response['message'] = 'Import completed.';
   			$response['total_rows'] = $total_rows;
   			$response['imported_count'] = $imported_count;
   			$response['duplicates_skipped'] = $duplicates_skipped + count($file_duplicates);
   			$response['invalid_rows_count'] = count($invalid_rows);
   			$response['invalid_rows'] = $invalid_rows;
   		}

   		echo json_encode($response);
   	}

}
