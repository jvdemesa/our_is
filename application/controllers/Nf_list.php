<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Nf_list extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Nf_list_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
 		// $this->load->model('Nf_list_model');
		$this->load->model('Admin_model', '', TRUE);
		// $this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'nf_list/index';
		$this->data['list'] = $this->Admin_model->list('link');
		$this->load->view('template/admin', $this->data);
  	}

	public function upload_csv(){
		$this->load->model('Admin_model', '', TRUE);
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

                if (($handle = fopen($filePath, "r")) !== FALSE) {
                    $csvData = array();
	                $header = fgetcsv($handle, 1000, ","); // Read the header row

                	$batch_no = $this->Nf_list_model->generate_batch_no();
                	$upload_batch_data = ["batch_no"=>$batch_no,
                					"uploaded_by"=>$this->session->userdata(SESSION_DATA)->uid,
		                            'date' => date("Y-m-d"),
		                            'time' => date("H:i:s"),
                				];
	                $response['upload_data'] = $upload_batch_data;
                    $batch_id = $this->Admin_model->add($upload_batch_data,'nf_list_batch');
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $subject = TRIM($data[9])." ".TRIM(str_replace('.', '', $data[10]));
                        $year = "20".substr(TRIM($data[2]),1,2);
                        $sem = substr($data[2],-1,1);
                        // Add each row to $csvData array
                        $csvData[] = array(
                            'college' => TRIM($data[0]),
                            'sais_id' => TRIM($data[3]),
                            'campus_id' => TRIM($data[4]),
                            'lname' => TRIM($data[5]),
                            'fname' => TRIM($data[6]),
                            'mname' => TRIM($data[7]),
                            'subject' => $subject,
                            'section' => TRIM($data[11]),
                            'units' => TRIM($data[12]),
                            'academic_program' => TRIM($data[13]),
                            'year' => $year,
                            'sem' => $sem,
                            'batch_id' => $batch_id
	                        );
                    }
                    // Insert batch into database
                    if (!empty($csvData)) {
                        $this->db->insert_batch('nf_list', $csvData);
                    }
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

   	public function get_batch(){
    	$this->load->model('Nf_list_model');
    	$this->load->model('General_model');
        $list = $this->Nf_list_model->get_datatables();
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
                        "recordsTotal" => $this->Nf_list_model->count_all(),
                        "recordsFiltered" => $this->Nf_list_model->count_filtered(),
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
