<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transcript_request extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
		$this->load->model('Admin_model', '', TRUE);
		$this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'transcript_request/index';
        $this->data['list'] = $this->Admin_model->list('link');
        $this->data['assigned_to'] = $this->General_model->list_dynamic('users',["section_id"=>3]);
        $this->data['request_lists'] = $this->General_model->list_dynamic('transcript_request_type',["status"=>0]);
		$this->load->view('template/admin', $this->data);
  	}

   	public function get_request_list(){
    	$this->load->model('Transcript_request_model');
    	$this->load->model('General_model');
        $list = $this->Transcript_request_model->get_datatables();
        $data = array();
        $box1 = 0;
        $box2 = 0;
        foreach ($list as $val) {
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
            $action = "<div class='btn-group'>
                            <button class='btn btn-info btn-sm view  tool_tip' tr_id='$val->id' title='VIEW FULL DETAILS'>
                                <i class='fa fa-eye'></i>
                            </button>
                            <button class='btn btn-warning btn-sm active_status  tool_tip' trans_code='$val->trans_code' title='Update Active Status'>
                                <i class='fa fa-check-square-o'></i>
                            </button>
                            ";
            if($val->status === "0"){
            $action.="<button class='btn btn-danger btn-sm transfer  tool_tip' 
                                assigned_to='$val->assigned_to' 
                                assigned_to_id='$val->assigned_to_id' 
                                trans_code='$val->trans_code' 
                                tr_id='$val->id' 
                                title='Update Active Status'>
                                <i class='fa fa-retweet'></i>
                            </button>";
            }
            //uncomment from here for otr update
            if($val->has_otr && $val->status == 4){
                switch($val->otr_step){
                    case 0:
                        $otr_step = 'For retrieval';
                    break;
                    case 1:
                        $otr_step = 'For Typing';
                    break;
                    case 2:
                        $otr_step = 'For Checking';
                    break;
                    case 3:
                        $otr_step = 'For Final Printing';
                    break;
                    case 4:
                        $otr_step = 'For Signature';
                    break;
                    default:
                    break;
                }
                $step_display = $val->otr_step+1;
                    if($val->otr_step <= 3){
                    $action .= "
                                <button class='btn btn-success btn-sm update_otr_status tool_tip' 
                                otr_step='$val->otr_step' 
                                trq_id='$val->trq_id' 
                                trd_id='$val->id' 
                                trans_code='$val->trans_code' 
                                title='$otr_step'>
                                    <span style='font-weight:bold; font-size:14px;'>$step_display</span>
                                </button>";
                }  
                if($val->otr_step == 4){
                    $action .= "
                                <button class='btn btn-success btn-sm tool_tip' 
                                title='$otr_step'>
                                    <span style='font-weight:bold; font-size:14px;'>$step_display</span>
                                </button>";
                }   
            }
            //uncomment to here for otr update
            if(in_array($val->status,[1,2,3])){
                $action .= "<button class='btn btn-secondary btn-sm reassess  tool_tip' tr_id='$val->id' title='Return to assessment'>
                            <i class='fa fa-refresh'></i>
                        </button>";
            }
            $action .= "</div>";
            
        $new_request = false;
        if ($val->timestamp != null) {
            $received_date = DateTime::createFromFormat("Y-m-d H:i:s", $val->timestamp);
            $now = new DateTime(); // Get the current date and time as a DateTime object
            $receive_elapse_days = $received_date->diff($now);
            
            if ($receive_elapse_days->days >= 3 && $val->status == 0) { // Check the total days difference
                $box1++;
                $new_request = true;
            }
        }
            
        $alert_payment = false;
        if($val->payment_date != null){
            $payment_date = DateTime::createFromFormat("Y-m-d H:i:s", $val->payment_date);
            $now = new DateTime(); // Get the current date and time as a DateTime object
            $payment_elapse_days = $payment_date->diff($now);
            if($payment_elapse_days->d  >=3 && $val->status == 3){
                $box2++;
                $alert_payment = true;
            }
        }
        $clearance = explode(",",$val->clearance_type);
        $cleared = "";
        if($val->clearance_type!=""){
            foreach($clearance as $cval){
                if($cval == "GS"){
                    $cleared .= "<i class='fa fa-check-circle uplb-red'></i>";
                }
                if($cval != "GS"){
                    $cleared .= "<i class='fa fa-check-circle text-success'></i>";
                }
            }
        }
        $row = array();
        $row[] = $val->trans_code;
        $row[] = $val->student_no;
        $row[] = $val->timestamp;
        $row[] = $val->payment_date;
        $row[] = $val->fname.$cleared;
        $row[] = $val->mname;
        $row[] = "<a href='#' class='look_up' lname='$val->lname' fname='$val->fname' mname='$val->mname'>".$val->lname."</a>";
        $row[] = $val->email;
        $row[] = $val->request_lists;
        $row[] = $val->assigned_to;
        $row[] = $status;
        $row[] = $val->is_printed==1?"<i class='fa fa-check uplb-green'></i>":"";
        $row[] = $action;
        $row[] = $val->active_status;
        $row[] = $new_request;
        $row[] = $alert_payment;
        $data[] = $row;
        }
        $request_last_update = $this->General_model->list_dynamic('tr_details_sheets_data',["type"=>1]);
        $request_last_update = $request_last_update?date("M d, Y H:i:s",strtotime($request_last_update[0]->last_update)):"";
        $payment_last_update = $this->General_model->list_dynamic('tr_details_sheets_data',["type"=>2]);
        $payment_last_update = $request_last_update?date("M d, Y H:i:s",strtotime($payment_last_update[0]->last_update)):"";
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Transcript_request_model->count_all(),
                        "recordsFiltered" => $this->Transcript_request_model->count_filtered(),
                        "data" => $data,
                        "box1" => $this->Transcript_request_model->get_new_requests(),
                        "box2" => $this->Transcript_request_model->get_paid(),
                        "request_last_update" => $request_last_update,
                        "payment_last_update" => $payment_last_update,
                );
        //output to json format
        echo json_encode($output);
   	}

    public function get_request_details(){
        $this->load->model('Transcript_request_model');
        $tr_id = $this->input->post("tr_id");
        $data = new stdClass();
        $data->details = $this->Transcript_request_model->get_tr_details($tr_id)[0];
        if($data->details->student_no != ""){
            $data->dup_stud_no = $this->General_model->list_dynamic("tr_details",["student_no"=>$data->details->student_no,"id != "=>$data->details->id, "active_status"=>0, "status <"=>6]);
        }else{
            $data->dup_stud_no = false;
        }
        $data->dup_name= $this->General_model->list_dynamic("tr_details",["fname"=>$data->details->fname,"lname"=>$data->details->lname,"mname"=>$data->details->mname,"id != "=>$data->details->id, "active_status"=>0, "status <"=>6]);
        $where = new stdClass();
        $where->trans_code = $data->details->trans_code;
        $data->schools = $this->General_model->list_dynamic("tr_schools_attended",$where,"type");
        $data->requests = $this->Transcript_request_model->new_request($where);
        $data->uploads = $this->General_model->list_dynamic("tr_uploads",$where);
        $data->purpose = $this->General_model->list_dynamic("tr_purpose",$where);
        $data->others_title = $this->General_model->list_dynamic("tr_others_title",$where);
        echo json_encode($data);
    }

    public function delete_batch(){
        $batch_id = $this->input->post('batch_id');
        $this->General_model->delete('student_batch',['id'=>$batch_id]);
        $this->General_model->delete('students_raw',['batch_id'=>$batch_id]);
        echo json_encode(1);
    }

    public function save_assessment(){
        $this->load->model('Transcript_request_model');
        $trans_code = $this->input->post('trans_code');
        $trd_id = $this->input->post('trd_id');
        $trq_id = $this->input->post('trq_id');
        $pages = $this->input->post('pages');
        $quantity = $this->input->post('quantity');
        $grand_total = $this->input->post('grand_total');
        $status = 1;
        $json_data = new stdClass();
        $this->db->trans_start();
            $this->General_model->update(["status"=>$status,"grand_total"=>$grand_total],$trd_id,"tr_details","id");
            $trq_id_array = [];
            foreach($trq_id as $key=>$val){
                $trq_id_array[] = $val;
                //update quantiity and no of pages
                $this->General_model->update(["pages"=>$pages[$key],"quantity"=>$quantity[$key]],$val,"tr_quantity","id");
            }
        // Complete the transaction
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            // If something went wrong, rollback
            // Log error or handle it appropriately
            $json_data->error = 1;
            $json_data->message = "Transaction Failed. Something went wrong. Please try again.";
            echo json_encode();
        } else {
            $json_data->error = 0;
            $json_data->message = "Assessment Saved! You may now send assessment to client email.";
            $json_data->trd_id = $trd_id;
            echo json_encode($json_data);
        }    
    }

    public function update_assessment(){
        $this->load->model('Transcript_request_model');
        $trans_code = $this->input->post('trans_code');
        $trd_id = $this->input->post('trd_id');
        $trq_id = $this->input->post('trq_id');
        $pages = $this->input->post('pages');
        $quantity = $this->input->post('quantity');
        $grand_total = $this->input->post('grand_total');
        $json_data = new stdClass();
        $this->db->trans_start();
            $this->General_model->update(["grand_total"=>$grand_total],$trd_id,"tr_details","id");
            $trq_id_array = [];
            foreach($trq_id as $key=>$val){
                $trq_id_array[] = $val;
                //update quantiity and no of pages
                $this->General_model->update(["pages"=>$pages[$key],"quantity"=>$quantity[$key]],$val,"tr_quantity","id");
            }
        // Complete the transaction
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            // If something went wrong, rollback
            // Log error or handle it appropriately
            $json_data->error = 1;
            $json_data->message = "Transaction Failed. Something went wrong. Please try again.";
            echo json_encode();
        } else {
            $json_data->error = 0;
            $json_data->message = "Request has been updated!";
            $json_data->trd_id = $trd_id;
            echo json_encode($json_data);
        }    
    }    

    public function delete_trq(){
        $this->load->model('Transcript_request_model');
        $trq_id = $this->input->post("trq_id");
        if($trq_id){
            $update = $this->Transcript_request_model->delete_trq($trq_id);
        }else{
            $update = false;
        }
        echo json_encode($update);
    }

    public function reassess(){
        $this->load->model('Transcript_request_model');
        $tr_id = $this->input->post("tr_id");
        if($tr_id){
            $update = $this->General_model->update_where(["status"=>0],["id"=>$tr_id],"tr_details");
        }else{
            $update = false;
        }
        echo json_encode($update);
    }

    public function disable_request(){
        $this->load->model('Transcript_request_model');
        $trq_id = $this->input->post("trq_id");
        $tr_id = $this->input->post("tr_id");
        $is_deleted = $this->input->post("is_deleted");
        if($trq_id){
            $this->General_model->update_where(["is_deleted"=>$is_deleted],["id"=>$trq_id],"tr_quantity");
            $update = $tr_id;
        }else{
            $update = false;
        }
        echo json_encode($update);
    }

    public function get_active_status_data(){
        $this->load->model('Transcript_request_model');
        $trans_code = $this->input->post("trans_code");
        $data = $this->Transcript_request_model->get_active_status($trans_code);
        echo json_encode($data);
    }

    public function add_active_status(){
        $this->load->model('Transcript_request_model');
        $trans_code = $this->input->post("trans_code");
        $remarks = $this->input->post("hold_remarks");
        $active_status = $this->input->post("active_status");
        $datetime = date("Y-m-d H:i:s");
        $this->General_model->add_data("tr_active_status",["trans_code"=>$trans_code,"remarks"=>$remarks, "active_status"=>$active_status,"datetime"=>$datetime, "updated_by"=>$this->session->userdata(SESSION_DATA)->fullname]);
        $this->General_model->update_where(["active_status"=>$active_status],["trans_code"=>$trans_code],"tr_details");
        $data = $this->Transcript_request_model->get_active_status($trans_code);
        echo json_encode($data);
    }

    public function add_request(){
        $this->load->model('Transcript_request_model');
        $trans_code = $this->input->post("trans_code");
        $rt_id = $this->input->post("request_id");
        $quantity = $this->input->post("request_quantity");
        $status = $this->input->post("status");
        if($status<=4){
            $q_status = 0;
        }else{
            $q_status = 1;
        }

       $this->General_model->add_data("tr_quantity",["trans_code"=>$trans_code,"rt_id"=>$rt_id, "quantity"=>$quantity, "status"=>$q_status]);
        echo json_encode(1);
    }

    public function print(){
        $this->load->model('Transcript_request_model');
        $this->data['content'] = 'transcript_request/print';

        $trans_code = $this->input->post("trans_code");
        $data = new stdClass();
        $this->data['details'] = $this->Transcript_request_model->get_tr_details_trans_code($trans_code)[0];
        $where = new stdClass();
        $where->trans_code = $trans_code;
        $this->data['schools'] = $this->General_model->list_dynamic("tr_schools_attended",$where,"type");
        $this->data['requests'] = $this->Transcript_request_model->new_request($where);
        $this->General_model->update_where(["is_printed"=>1],$where,"tr_details");

        $this->load->view('template/print_head', $this->data);
    }

    public function transfer(){
        $this->load->model('Transcript_request_model');
        $tr_id = $this->input->post("tr_id");
        $trans_code = $this->input->post("trans_code");
        $assign_to_id = $this->input->post("assign_to_id");
        $trans_code = $this->input->post("trans_code");
        if($assign_to_id){
            $this->General_model->update_where(["assigned_to"=>$assign_to_id],["id"=>$tr_id,"trans_code"=>$trans_code],"tr_details");
            $message = "Update Complete";
        }else{
            $message = "Failed to Update.";
        }
        echo json_encode($message);
    }

    public function look_up(){
        $this->load->model('Transcript_request_model');
        $name = $this->input->post("lname").", ".$this->input->post("fname");
        $data = $this->Transcript_request_model->look_up($name);
        echo json_encode($data);
    }
}
