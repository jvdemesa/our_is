<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		//$this->output->enable_profiler(TRUE);
	}

	public function index()
	{

	}

	public function transcript_request()
	{
		$this->isAllowed();
		$this->load->model('Admin_model', '', TRUE);
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'reports/transcript_request/index';

		$where = new stdClass();
		$where->is_parent_only = 0;
		$this->data['request_type'] =  $this->General_model->list_dynamic("transcript_request_type",$where);
		$this->data['served_by'] =  $this->General_model->list_dynamic("users",['section_id'=>3]);
		$this->load->view('template/admin', $this->data);
	}

	public function transcript_request_list(){
    	$this->load->model('Reports_transcript_request_model');
    	$this->load->model('General_model');
        $list = $this->Reports_transcript_request_model->get_list();
        $status = $list?1:0;
        $data = array();
        $paid =0;
        $unpaid =0;
        foreach ($list as $val) {
       //  	$time_analysis = "<label>Waiting Time:</label><p class='red'>$val->waiting_time</p>
       //  						<hr>
							// <label>Service_time:</label><p class='red'>$val->service_time</p>";
       //  	$called_time = "<label>Initial Call:</label><p class='red'>$val->time_called</p>
       //  						<hr>
							// <label>Last Called:</label><p class='red'>$val->time_last_called</p>
       //  						<hr>
							// <label>Number of Calls:$val->call_count</label>
							// ";
            switch($val->status){
                case "0":
                    $status = "New Request";
                break;
                case "1":
                    $status = "Assessed";
                break;
                case "2":
                    $status = "Assessment Sent";
                break;
                case "3":
                    $status = "Proof of payment received";
                break;
                case "4":
                    $status = "Payment verified - On Process";
                break;
                case "5":
                    $status = "For release";
                break;
                case "6":
                    $status = "Released";
                break;
            }
            $row = array();
            $row[] = $val->trans_code;
            $row[] = $val->student_no;
            $row[] = $val->client_name;
            $row[] = $val->assigned_to;
            $row[] = $val->degree;
            $row[] = $val->request_type;
            $row[] = ($val->quantity);
            $row[] = $val->request_date;
            $row[] = $val->assessment_date_sent;
            $row[] = $val->payment_date;
            $row[] = $val->payment_confirmation_time;
            $row[] = $val->ready_date;
            $row[] = $status;
            $data[] = $row;
            if($val->payment_date != null){
            	$paid++;
            }else{
            	$unpaid++;
            }
            if($val->assessment_date_sent != null){
                $assessment_date_sent = strtotime($val->assessment_date_sent);
                $request_date = strtotime($val->request_date);
                if($assessment_date_sent>$request_date){
    	            $assessment_time[] = $assessment_date_sent -  $request_date;
                }
            }
            if($val->payment_date != null && $val->ready_date != null  ){
                $ready_date = strtotime($val->ready_date);
                $payment_date = strtotime($val->payment_date);
                if($ready_date>$payment_date){
    	            $pay_time[] =  $ready_date - $payment_date;
                }
            }
        }

        function formatDuration($seconds) {
		    $days = floor($seconds / 86400); // 86400 seconds in a day
		    $hours = floor(($seconds % 86400) / 3600); // Remainder in hours
		    $minutes = floor(($seconds % 3600) / 60); // Remainder in minutes
		    return sprintf("%d Days %02d Hours %02d Minutes", $days, $hours, $minutes);
		}
        $assessment_time_data = null;
        if(isset($assessment_time)){
            $assessment_time_data = ["assessment_min"=>formatDuration(min($assessment_time)), "assessment_max"=>formatDuration(max($assessment_time)), "assessment_avg"=>formatDuration(array_sum($assessment_time) / count($assessment_time))];
        }
        $pay_time_data = null;
        if(isset($pay_time)){
            $pay_time_data = ["min"=>formatDuration(min($pay_time)), "max"=>formatDuration(max($pay_time)), "avg"=>formatDuration(array_sum($pay_time) / count($pay_time))];
        }
		$service_time = null;
		$call_status =null;
        $output = array(
                        "data" => $data,
                        "assessment_time"=>$assessment_time_data,
                        "pay_time"=>$pay_time_data,
                        "service_time"=>$service_time,
                        "paid"=>$paid,
                        "unpaid"=>$unpaid,
                        "daily_client_count"=>$this->Reports_transcript_request_model->daily_client_count(),
                );
        //output to json format
        echo json_encode($output);
	}
}
