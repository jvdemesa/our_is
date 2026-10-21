<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class ID_requests extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
        $this->load->helper('download');
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
		$this->load->model('Admin_model', '', TRUE);
		$this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'id_requests/index';
        $this->data['list'] = $this->Admin_model->list('link');
        $this->data['assigned_to'] = $this->General_model->list_dynamic('users',["section_id"=>3]);
		$this->load->view('template/admin', $this->data);
  	}

   	public function get_list(){
    	$this->load->model('ID_requests_model');
    	$this->load->model('General_model');
        $list = $this->ID_requests_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
            switch($val->request_type){
                case "1":
                    $request_type = "NEW: New first year undergraduate";
                break;
                case "2":
                    $request_type = "NEW: Transferee";
                break;
                case "3":
                    $request_type = "Second degree";
                break;
                case "4":
                    $request_type = "Non-degree";
                break;
                case "5":
                    $request_type = "NEW: New Graduate Student";
                break;
                case "6":
                    $request_type = "Exchange student";
                break;
                case "7":
                    $request_type = "REPLACEMENT: Lost ID";
                break;
                case "8":
                    $request_type = "REPLACEMENT: Damaged ID";
                break;
                case "9":
                    $request_type = "REPLACEMENT: Change information";
                break;
                case "10":
                    $request_type = "REPLACEMENT: shifted to another degree program";
                break;
                case "11":
                    $request_type = "Continuing undergraduate: First Request";
                break;
                case "12":
                    $request_type = "Continuing Graduate Student: First Request";
                break;
                default:
                    $status = "No matching request";
                break;
            }
        switch($val->status){
            case "1":
                $status = "<p class='text-success'>Approve</p>";
            break;
            case "2":
                $status = "<p class='text-danger'>Disapprove</p>";
            break;
            case "3":
                $release_date = $val->release_date!==null?date("F d",strtotime($val->release_date)):"";
                $status = "<p class='text-info'>For Release $release_date </p>";
            break;
            case "4":
                $status = "<p class='text-info'>Non-compliance Email Sent</p>";
            break;
            default:
                $status = "<p class='text-warning'>Pending Approval</p>";
            break;
        }
        $action = "<div class='btn-group'>
                        ";
        $rejection_count = $val->rejection_count+1;
        $new_transaction_id = $val->transaction_id===""?"":$val->transaction_id."-".$rejection_count;
        $action .= "<button class='btn btn-info btn-sm view_files  
                        tool_tip' 
                        id_request_id='$val->id'
                        status='$val->status'
                        student_number='$val->student_number' 
                        email='$val->email' 
                        fname='$val->fname' 
                        mname='$val->mname' 
                        lname='$val->lname' 
                        college='$val->college' 
                        birthday='$val->birthday' 
                        address='$val->address' 
                        transaction_id='$new_transaction_id'
                        rejection_count='$rejection_count' 
                        id_picture_file='$val->id_pic' 
                        signature_file='$val->signature' 
                        proof_of_payment_file='$val->proof_of_payment' 
                        form5_file='$val->form5' 
                        current_form5_file='$val->current_form5' 
                        first_year_form5_file='$val->first_year_form5' 
                        student_number='$val->student_number' 
                        title='View Files'>
                        <i class='fa fa-file'></i>
                    </button>";            
        if($val->status == 1 && !empty(array_intersect($this->session->userdata(SESSION_DATA)->access, [9,1]))){
            $action .= "<button class='btn btn-warning btn-sm release_schedule  
                            tool_tip' 
                            id_request_id='$val->id'
                            student_number='$val->student_number' 
                            fname='$val->fname' 
                            email='$val->email' 
                            request_type='$val->request_type' 
                            title='Email release'>
                            <i class='fa fa-envelope'></i>
                        </button>";     
        }
        if(in_array($val->request_type, [3,4,6,7,8,9,10,11,12]) && $val->status == 0 && !empty(array_intersect($this->session->userdata(SESSION_DATA)->access, [8,1]))){
            $action .= "<button class='btn btn-success btn-sm 
                        approve_request  
                        tool_tip'
                        id_request_id='$val->id' 
                        form5_file='$val->form5' 
                        form_file='$val->application_form' 
                        request_type='$request_type'
                        requestor='$val->lname, $val->fname $val->mname'
                        student_number='$val->student_number' 
                        fname='$val->fname' 
                        email='$val->email' 
                        title='Approve Request'>
                        <i class='fa fa-thumbs-up'></i>
                    </button>";
        }
        $action .= "</div>";
        $row = array();
        $row[] = $val->student_number;
        $row[] = $val->email;
        $row[] = strtoupper($val->fname);
        $row[] = strtoupper($val->mname);
        $row[] = strtoupper($val->lname);
        $row[] = $val->college;
        $row[] = $val->birthday;
        $row[] = $val->address;
        $row[] = $request_type;
        $row[] = $status;
        $row[] = $action;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->ID_requests_model->count_all(),
                        "recordsFiltered" => $this->ID_requests_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
   	}

    public function approve_id_request(){
        $id = $this->input->post("id_request_id");
        $status = $this->input->post("status");
        $update_status = $this->General_model->update_where(["status"=>$status],['id'=>$id],"id_requests");
        echo json_encode($update_status);
    }

    public function count_release_sched(){
        $this->load->model('ID_requests_model');
        $release_date = date("Y-m-d",strtotime($this->input->post("release_date")));
        $total_release = $this->ID_requests_model->count_release_sched($release_date);
        echo json_encode($total_release);
    }

	public function id_requests_duplicates()
	{
		$sql = "
			SELECT
				ir.student_number,
				CONCAT_WS(' ', ir.fname, ir.mname, ir.lname) AS name,
				ir.status,
				ir.request_type
			FROM id_requests ir
			INNER JOIN (
				SELECT student_number
				FROM id_requests
				WHERE is_deleted = 0
				AND student_number IS NOT NULL
				AND TRIM(student_number) <> ''
				GROUP BY student_number
				HAVING COUNT(*) > 1
			) duplicates
				ON duplicates.student_number = ir.student_number
			WHERE ir.is_deleted = 0
			ORDER BY ir.student_number ASC, ir.id ASC
		";

		$records = $this->db->query($sql)->result_array();

		$status_labels = [
			0 => 'Pending Approval',
			1 => 'Approved',
			2 => 'Disapproved',
			3 => 'Release Email Sent',
			4 => 'Non Compliance'
		];

		$request_type_labels = [
			1 => 'New First Year Undergraduate',
			2 => 'Transferee',
			3 => 'Second Degree',
			4 => 'Non-degree',
			5 => 'New Graduate Student',
			6 => 'Exchange Student',
			7 => 'Lost ID',
			8 => 'Damaged ID',
			9 => 'Change Information',
			10 => 'Shifted to Another Degree Program',
			11 => 'Continuing Undergraduate: First Request',
			12 => 'Continuing Graduate Student: First Request'
		];

		echo '<table border="1" cellpadding="6" cellspacing="0">';
		echo '<thead><tr>
				<th>Student Number</th>
				<th>Name</th>
				<th>Status</th>
				<th>Request Type</th>
			</tr></thead><tbody>';

		foreach ($records as $row) {
			echo '<tr>';
			echo '<td>' . html_escape($row['student_number']) . '</td>';
			echo '<td>' . html_escape($row['name']) . '</td>';
			echo '<td>' . html_escape(
				$status_labels[$row['status']] ?? $row['status']
			) . '</td>';
			echo '<td>' . html_escape(
				$request_type_labels[$row['request_type']] ?? $row['request_type']
			) . '</td>';
			echo '</tr>';
		}

		if (empty($records)) {
			echo '<tr><td colspan="4">No duplicate student records found.</td></tr>';
		}

		echo '</tbody></table>';
	}

	/**
	 * Persists a Non-Compliance submission (general message and/or
	 * file-specific upload reasons) for an ID request.
	 *
	 * Expects POST:
	 *   id_request_id            (int)
	 *   non_compliance_message   (string, optional)
	 *   file_reasons             (array: file_type => reason)
	 *
	 * Returns JSON: error (0|1), message, id, non_compliance_case_id,
	 * upload_count.
	 */
	public function save_non_compliance(){
		$this->load->model('ID_requests_model');
		$userdata = $this->session->userdata(SESSION_DATA);

		$id_request_id = $this->input->post('id_request_id');
		$message = $this->input->post('non_compliance_message');
		$file_reasons = $this->input->post('file_reasons');

		$result = $this->ID_requests_model->save_non_compliance(
			$id_request_id,
			$message,
			$file_reasons,
			($userdata ? $userdata->uid : null)
		);

		echo json_encode($result);
	}

	/**
	 * Returns the latest Non-Compliance submission for a request together
	 * with the upload reasons belonging to that submission only.
	 *
	 * Accepts id_request_id via POST (or GET). Returns JSON: error,
	 * message, found, submission, uploads.
	 */
	public function get_non_compliance(){
		$this->load->model('ID_requests_model');

		$id_request_id = $this->input->post('id_request_id');
		if($id_request_id === null || $id_request_id === false){
			$id_request_id = $this->input->get('id_request_id');
		}

		echo json_encode($this->ID_requests_model->get_latest_non_compliance($id_request_id));
	}
}
