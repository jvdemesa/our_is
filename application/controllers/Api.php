<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Api extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Reclassification_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

    public function get_release_details(){
        $apiKey = $this->input->post('apiKey');
        $json_data = new stdClass();
        if($apiKey=="656454397796577"){
            $this->load->model('Transcript_request_model');
            $trans_code = $this->input->post('trans_code');
            $status = $this->input->post('status');
            $queue_id = $this->input->post('queue_id');
            if (!$trans_code) {
                $response = array('status' => 'error', 'message' => 'trans_code parameter is missing');
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return;
            }
            $where = new stdClass();
            $where->trans_code = $trans_code;
            if($status == 0){
                $where->status = 1;
            }else{
                $where->queue_id = $queue_id;
            }
            $details = $this->Transcript_request_model->get_tr_details_trans_code($trans_code);
            if ($details) {
                $json_data->details =  $details[0];
                $json_data->error = 0;
                $json_data->for_release = $this->Transcript_request_model->list_requests($where);
            } else {
                $json_data->details =  false;
                $json_data->error = 1;
                $json_data->for_release = false;
            }
            $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json_data));
        }else{
            $json_data->status = 0;
            $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json_data));
        }
    }

    public function release_docs(){
        $apiKey = $this->input->post('apiKey');
        $json_data = new stdClass();
        if($apiKey=="656454397796577"){
            $this->load->model('Transcript_request_model');
            $trans_code = $this->input->post('trans_code');
            $released_to = $this->input->post('released_to');
            $released_by = $this->input->post('released_by');
            $queue_id = $this->input->post('queue_id');
            $details = $this->Transcript_request_model->get_tr_details_trans_code($trans_code);

            $email_logs_id = $this->General_model->add_data('email_logs',["trans_code"=>$trans_code,"action"=>'Released', 'staff_name'=>$released_by, 'datetime'=>date("Y-m-d H:i:s")]); 
            if($details){
                $trd_id = $details[0]->id;
                $staff_name = $details[0]->staff_name;
                $status = 6;
                $email_data = new stdClass();
                $email_data->to = $details[0]->email;

                $where = new stdClass();
                $where->trans_code = $trans_code;
                $where->status = 1;
                $table_data = $this->Transcript_request_model->list_requests($where);
                if($table_data){
                    $table_tbody = "";
                    foreach($table_data as $val){
                        $total = $val->quantity*$val->pages*$val->price;
                        $table_tbody.="<tr style='border:1px solid black;'>
                                        <td style='border:1px solid black;'>$val->request</td>
                                        <td style='border:1px solid black;'>$val->quantity</td>
                                        <td style='border:1px solid black;'>$val->pages</td>
                                        </tr>
                                        ";
                    }

                    $email_data->subject = "UPLB OTR/Certification Request Updates $trans_code";        
                    $email_data->message = "<b>Transaction Code:</b>
                                            <span style='color:red;'> <b>$trans_code</b></span>
                                            <br>
                                            <br>
                                            Good day!
                                            <br>
                                            <br>                                
                                            This is to inform you that the following documents were released to the owner/authorized representative $released_to.
                                            <br>
                                            <br>
                                            <table style='border:1px solid black; border-collapse: collapse;'>
                                                <tr style='border:1px solid black;'>
                                                    <td style='border:1px solid black;'>Document</td>
                                                    <td style='border:1px solid black;'>Number of copies</td>
                                                    <td style='border:1px solid black;'>Number of pages per copy</td>
                                                </tr>
                                                $table_tbody
                                            </table>
                                            <br>
                                            <br>
                                            This is an automatically-generated message; please <b>do not reply</b> to this email message.
                                            <br>
                                            <br>
                                            Should you have questions/concerns regarding your request, do not hesitate to contact us by sending email to transcript_our.uplb@up.edu.ph with the Subject: ATTN: Ms. $staff_name re OTR/Certification Request $trans_code.
                                            ";
                    $max_attempts = 2;
                    $attempts = 0;
                    $email_sent = false;

                    while ($attempts < $max_attempts && !$email_sent) {
                        if ($this->send_email($email_data)) {
                            $email_sent = true;
                        } else {
                            $attempts++;
                            $this->General_model->update(["attempts"=>$attempts],$email_logs_id,"email_logs","id");
                        }
                    }

                    if ($email_sent) {
                        // Update the database if email was sent successfully
                        $this->General_model->update(["is_sent"=>1],$email_logs_id,"email_logs","id");
                        $this->db->trans_start(); // Start transaction
                        $this->General_model->update_where(["status"=>2,"released_to"=>$released_to,"released_by"=>$released_by,"release_date"=>date("Y-m-d H:i:s"),"queue_id"=>$queue_id],["trans_code"=>$trans_code,"status"=>1],"tr_quantity");
                        $is_not_released = $this->Transcript_request_model->is_not_released($trans_code);
                        if(!$is_not_released){
                            $this->General_model->update(["status"=>$status],$trd_id,"tr_details","id");
                        }
                        $json_data->queue_id = $queue_id;
                        $json_data->released_to = $released_to;
                        $json_data->released_by = $released_by;
                        $json_data->error = 0;
                        $json_data->message = "Document has been released!";
                        $json_data->trd_id = $trd_id;
                        $this->db->trans_commit();
                    } else {
                        // If it fails after all attempts
                        $error = $this->email->print_debugger();
                        $json_data->error = 1;
                        $json_data->message = "Failed to send email. Try again later.";
                        $json_data->trd_id = $trd_id;
                        $this->db->trans_rollback();
                    }
                }
            }
            echo json_encode($json_data);
        }else{
            $json_data->status = 0;
            echo json_encode($json_data);
        }
    }
}
