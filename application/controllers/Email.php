<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Email extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Reclassification_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

    public function assessment(){
        $this->load->model('Transcript_request_model');
        $trans_code = $this->input->post('trans_code');
        $trd_id = $this->input->post('trd_id');
        $trq_id = $this->input->post('trq_id');
        $grand_total = $this->input->post('grand_total');
        $staff_name = $this->input->post('staff_name');
        $status = 2;
        $email_data = new stdClass();
        $email_data->to = $this->input->post('email');

        $email_logs_id = $this->General_model->add_data('email_logs',["trans_code"=>$trans_code,"action"=>'assessment', 'staff_name'=>$this->session->userdata(SESSION_DATA)->fullname, 'datetime'=>date("Y-m-d H:i:s")]);

        $email_data->subject = "UPLB OTR/Certification Request Updates $trans_code";        
        $table_data = $this->Transcript_request_model->get_request_list_via_id($trq_id,0);
        $table_tbody = "";
        if($table_data){
            foreach($table_data as $val){
                $total = $val->quantity*$val->pages*$val->price;
                $per_page = $val->is_per_page?"per page":"per copy";
                $table_tbody.="<tr style='border:1px solid black;'>
                                <td style='border:1px solid black;'>$val->request</td>
                                <td style='border:1px solid black;'>$val->quantity</td>
                                <td style='border:1px solid black;'>$val->pages</td>
                                <td style='border:1px solid black;'>Php $val->price $per_page</td>
                                <td style='border:1px solid black;'>Php $total</td>
                                </tr>
                                ";
            }
            $email_data->message = "<b>Transaction Code:</b><span style='color:red;'> <b>$trans_code</b></span>
                                    <br><br>
                                    Good day!<br><br>
                                    Your request for Official Transcript of Records (OTR) and/or certifications has been received and assessed. Please see the table below for the computation of fees and total fees to be paid:
                                    <br>
                                    <table style='border:1px solid black; border-collapse: collapse;'>
                                        <tr style='border:1px solid black;'>
                                            <td style='border:1px solid black;'>
                                                <b>Document</b>
                                            </td>
                                            <td style='border:1px solid black;'>
                                                <b>Number of copies</b>
                                            </td>
                                            <td style='border:1px solid black;'>
                                                <b>
                                                Number of pages per copy
                                                <span  style='color:red;'>*</span>
                                                </b>
                                            </td>
                                            <td style='border:1px solid black;'>
                                                <b>Fees</b>
                                            </td>
                                            <td style='border:1px solid black;'>
                                                <b>Total Fees</b>
                                            </td>
                                        </tr>
                                        $table_tbody
                                        <tr style='border:1px solid black;'>
                                            <td colspan='4' 
                                            style='border:1px solid black; 
                                            text-align:right'>
                                                <b>Total Fees to be paid</b>
                                            </td>
                                            <td style='border:1px solid black;'>
                                                <b><span style='color:red;'>Php $grand_total</span></b>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <span  style='color:red;'>*</span>If your request includes your very first copy of your Official Transcript of Records (OTR), the number of pages per copy of your OTR indicated in the table will be just an estimate and is based on your educational background. The actual number of pages will be determined once your OTR is prepared. A staff member of the Transcript of Records Section will contact you via email should there be a discrepancy between the estimated and the actual number of pages.
                                    <br>
                                    <br>
                                    You may pay your fees using any of the following options:
                                    <br>
                                    1) Payment in-person at the <a href='https://maps.app.goo.gl/NNjRbhjfL2sMJada8'>UPLB Cashier’s Office</a> use fund code <b>9304811</b>; or
                                    <br>
                                    2) Online payment via <a href='https://www.lbp-eservices.com/egps/portal/index.jsp'>LANDBANK LINKBIZ</a>.
                                    <br>
                                    <br>
                                    To view a step-by-step guide to paying fees, please view this <a href='https://our.uplb.edu.ph/payment-options-for-requests-for-official-transcript-of-records-and-certifications/'>article</a>.
                                    <br>
                                    <br>
                                    Once payment has been settled, use this <a href='https://docs.google.com/forms/d/e/1FAIpQLSdgmQ1DKuMj_PqaBEqRcSSW59VZa7baGzbtb0mACmE9HyMQ-Q/viewform?usp=pp_url&entry.1296145994=$trans_code'>form</a> to submit proof of payment.Be sure to indicate <span style='color:red;'><b>$trans_code</b></span> as your Transaction Code.
                                    <br>
                                    <br>
                                    You will be sent an email acknowledging your payment once your payment is validated. Please also check your junk/spam mail folder in case our email is redirected there.
                                    <br>
                                    <br>
                                    This is an automatically-generated message; please <b>do not reply</b> to this email message.
                                    <br>
                                    <br>
                                    Should you have questions/concerns regarding your request, do not hesitate to contact us by sending email to transcript_our.uplb@up.edu.ph with the Subject: ATTN: Ms. $staff_name re OTR/Certification Request $trans_code.
                                    ";

            $json_data = new stdClass();

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
                $this->General_model->update(["status"=>$status,"assessment_date_sent"=>date("Y-m-d H:i:s")],$trd_id,"tr_details","id");
                $this->General_model->update(["is_sent"=>1],$email_logs_id,"email_logs","id");
                $json_data->error = 0;
                $json_data->message = "Assessment has been sent.";
                $json_data->trd_id = $trd_id;
            } else {
                // If it fails after all attempts
                $error = $this->email->print_debugger();
                $json_data->error = 1;
                $json_data->message = "Failed to send email. Try again later.";
                $json_data->trd_id = $trd_id;
            }
        }else{
            $json_data->error = 1;
            $json_data->message = "No request detected.";
            $json_data->trd_id = $trd_id;
        }

        echo json_encode($json_data);
    }


    public function payment_confirmation(){
        $this->load->model('Transcript_request_model');
        $trans_code = $this->input->post('trans_code');
        $trd_id = $this->input->post('trd_id');
        $trq_id = $this->input->post('trq_id');
        $staff_name = $this->input->post('staff_name');
        $grand_total = $this->input->post('grand_total');
        $status = 4;
        $email_data = new stdClass();
        $email_data->to = $this->input->post('email');

        $with_otr = $this->General_model->list_dynamic("tr_quantity",["trans_code"=>$trans_code, "rt_id"=>1, "quantity !="=>0]);
        if($with_otr){
            $otr_email = "Please note that for <b>OTR – First Copy</b> requests, status updates may be sent via separate email thread.";
        }
        //       add this line on line 174      


        $email_logs_id = $this->General_model->add_data('email_logs',["trans_code"=>$trans_code,"action"=>'payment_confirmation', 'staff_name'=>$this->session->userdata(SESSION_DATA)->fullname, 'datetime'=>date("Y-m-d H:i:s")]);

        $email_data->subject = "UPLB OTR/Certification Request Updates $trans_code";        
        $email_data->message = "
        <div style='font-family: Arial, sans-serif; color: #333; font-size: 14px; line-height: 1.6;'>
            <p><b>Transaction Code:</b> <span style='color:#d9534f; font-weight:bold;'>$trans_code</span></p>

            <p>Good day!</p>

            <p>Your payment has been <b>validated</b>. An email using this thread will be sent to you once your documents are ready for pickup.</p>

            <p>$otr_email</p>

            <p>Please regularly check both your <b>inbox</b> and your <b>junk/spam mail folder</b> in case our email is redirected there.</p>


            <p style='color:#555; font-size:13px;'>
                This is an <b>automatically generated message</b>; please <b>do not reply</b> to this email.
            </p>

            <p>
                Should you have any questions or concerns regarding your request, you may contact us at 
                <a href='mailto:transcript_our.uplb@up.edu.ph' style='color:#0d6efd; text-decoration:none;'>transcript_our.uplb@up.edu.ph</a><br>
                with the subject line:<br>
                <b>ATTN: Ms. $staff_name re OTR/Certification Request $trans_code</b>
            </p>

            <p style='margin-top:30px; color:#888; font-size:12px;'>
                — Office of the University Registrar, UPLB
            </p>
        </div>
        ";

        $json_data = new stdClass();

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
            $this->General_model->update(["status"=>$status],$trd_id,"tr_details","id");
            $json_data->error = 0;
            $json_data->message = "Payment has been verified!";
            $json_data->trd_id = $trd_id;
        } else {
            // If it fails after all attempts
            $error = $this->email->print_debugger();
            $json_data->error = 1;
            $json_data->message = "Failed to send email. Try again later.";
            $json_data->trd_id = $trd_id;
        }
        echo json_encode($json_data);        
    }

    public function release_schedule(){
        $this->load->model('Transcript_request_model');
        $cb_trq_id = $this->input->post("cb_trq_id");
        $trans_code = $this->input->post('trans_code');
        $trd_id = $this->input->post('trd_id');
        $trq_id = $this->input->post('trq_id');
        $staff_name = $this->input->post('staff_name');
        $grand_total = $this->input->post('grand_total');
        $status = 5;
        $email_data = new stdClass();
        $email_data->to = $this->input->post('email');
        $ready_by = $this->session->userdata(SESSION_DATA)->fullname;
        $email_logs_id = $this->General_model->add_data('email_logs',["trans_code"=>$trans_code,"action"=>'release_schedule', 'staff_name'=>$this->session->userdata(SESSION_DATA)->fullname, 'datetime'=>date("Y-m-d H:i:s")]);
        $this->db->trans_start(); // Start transaction
        foreach($cb_trq_id as $key=>$val){
            $this->General_model->update(["status"=>1,"ready_date"=>date("Y-m-d H:i:s"),"ready_by"=>$ready_by],$val,"tr_quantity","id");
        }
        $where = new stdClass();
        $where->trans_code = $trans_code;
        $where->status = 1;
        $table_data = $this->Transcript_request_model->list_requests($where);
        $table_tbody = "";
        if($table_data){
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
            $email_data->message = "<b>Transaction Code:</b><span style='color:red;'> <b>$trans_code</b></span>
                                    <br>
                                    <br>
                                    Good day!
                                    <br>
                                    <br>                                
                                    The documents you requested are ready for claiming. You may proceed to the UPLB OUR at the address below during office hours, Monday to Friday, 8am to 5pm (except holidays):
                                    <br>
                                    <br>
                                    <a href='https://maps.app.goo.gl/BuuX9UG7ijmXrfDs8'>UPLB OUR G/F CAS Annex I Building, UPLB, College, Laguna</a>
                                    <br>
                                    <br>
                                    See table below for the list of available document/s for release.
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
                                    Be sure to have your Transaction Code ready upon claiming your documents.
                                    <br>
                                    <br>
                                    In case you will not be able to claim your documents personally, you may send an authorize representative in your place and give your Transaction Code to your authorized representative. To ensure that your documents are released only to your authorized representative, please do not share your Transaction Code with anyone else other than your authorized representative.                                
                                    <br>
                                    <br>
                                    Your documents will be released once the Transaction Code provided has been verified.
                                    <br>
                                    <br>
                                    Documents unclaimed after one year will be shredded.
                                    <br>
                                    <br>
                                    This is an automatically-generated message; please <b>do not reply</b> to this email message.
                                    <br>
                                    <br>
                                    Should you have questions/concerns regarding your request, do not hesitate to contact us by sending email to transcript_our.uplb@up.edu.ph with the Subject: ATTN: Ms. $staff_name re OTR/Certification Request $trans_code.
                                    ";
            $json_data = new stdClass();
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
                $is_on_process = $this->Transcript_request_model->list_requests(["trans_code"=>$trans_code,"status"=>0,"quantity !="=>0]);
                if(!$is_on_process){
                    $this->General_model->update(["status"=>$status],$trd_id,"tr_details","id");
                }
                $json_data->error = 0;
                $json_data->message = "Document release schedule has been sent!";
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
            echo json_encode($json_data);        
        }
    }

    public function update_otr_status(){
        $this->load->model('Transcript_request_model');
        $otr_step = $this->input->post("otr_step");
        $next_step = $otr_step+1;
        $trq_id = $this->input->post("trq_id");
        $trd_id = $this->input->post("trd_id");
        $step_done = $this->input->post("step_done");
        $trans_code = $this->input->post('trans_code');
        $details = $this->Transcript_request_model->get_tr_details($trd_id)[0];
        $staff_name = $details->staff_name;
        $email_data = new stdClass();
        $email_data->to = $details->email;
        $ready_by = $this->session->userdata(SESSION_DATA)->fullname;
        $email_logs_id = $this->General_model->add_data('email_logs',["trans_code"=>$trans_code,"action"=>"update OTR Status to $step_done", 'staff_name'=>$this->session->userdata(SESSION_DATA)->fullname, 'datetime'=>date("Y-m-d H:i:s")]);
        $this->db->trans_start(); // Start transaction
        $this->General_model->update(["otr_steps"=>$next_step],$trq_id,"tr_quantity","id");
        switch($otr_step){
            case "0":
                $current_status = 'Your academic information has been retrieved from your records and is being prepared for typing into your OTR.';            
            break;
            case "1":
                $current_status = 'Your academic information has been typed into your OTR which is now undergoing checking and verification';
            break;
            case "2":
                $current_status = 'Your OTR has been checked and verified and is now ready for final printing.';
            break;
            case "3":
                $current_status = 'Your OTR has been printed and is awaiting the signature of the university registrar.';
            break;
        }
        $table_tbody = "";

        $email_data->subject = "UPLB update on OTR - First Copy Request $trans_code";        

        $email_data->message = "
        <b>Transaction Code:</b> <span style='color:red;'><b>$trans_code</b></span>
        <br><br>
        Good day!
        <br><br>
        We would like to inform you that the status of your <b>OTR - First Copy</b> request has been updated.
        <br><br>
        <b>Update:</b> <span style='color:blue;'>$current_status</span>
        <br><br>
        This is an automatically-generated message; please <b>do not reply</b> to this email.
        <br><br>
        Should you have any questions or concerns regarding your request, please contact us by sending an email to 
        <a href='mailto:transcript_our.uplb@up.edu.ph'>transcript_our.uplb@up.edu.ph</a> 
        with the subject line: <b>ATTN: Ms. $staff_name re OTR - First Copy $trans_code</b>.
        <br><br>
        Thank you for your attention.
        <br><br>
        Best regards,<br>
        Office of the University Registrar<br>
        University of the Philippines Los Baños
        ";

        $json_data = new stdClass();
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
            // $is_on_process = $this->Transcript_request_model->list_requests(["trans_code"=>$trans_code,"status"=>0,"quantity !="=>0]);
            // if(!$is_on_process){
            //     $this->General_model->update(["status"=>$status],$trd_id,"tr_details","id");
            // }
            $json_data->error = 0;
            $json_data->message = "OTR - First copy detail has been updated!";
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
        echo json_encode($json_data);        
    }

    public function id_release_schedule(){
        $this->load->model('ID_requests_model');
        $id_request_id = $this->input->post("id_request_id");
        $request_type = $this->input->post('request_type');
        $student_number = $this->input->post('student_number');
        $fname = $this->input->post('fname');
        $email = $this->input->post('email');
        $release_date = $this->input->post('release_date');

        $email_data = new stdClass();
        $email_data->to = $email;
        $email_logs_id = $this->General_model->add_data('id_email_logs',["id_request_id"=>$id_request_id,"action"=>'release_schedule', 'staff_name'=>$this->session->userdata(SESSION_DATA)->fullname, 'date_sent'=>date("Y-m-d H:i:s"),"release_date"=>date("Y-m-d",strtotime($release_date))]);
        $this->db->trans_start(); // Start transaction
        $this->General_model->update(["status"=>3,"ready_date"=>date("Y-m-d H:i:s")],$id_request_id,"id_requests","id");

        $email_data->subject = "UPLB ID Request Update";

        $email_data->message = '
        <!DOCTYPE html>
        <html>
        <body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;">

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
        <tr>
        <td align="center">

        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px solid #dddddd;border-radius:8px;">

        <tr>
        <td style="background:#00573F;color:#ffffff;padding:20px 30px;font-size:22px;font-weight:bold;">
        UPLB ID Request Update
        </td>
        </tr>

        <tr>
        <td style="padding:30px;font-size:15px;color:#333333;line-height:1.7;">

        <p style="margin-top:0;">Dear <strong>'.$fname.'</strong>,</p>

        <p>
        Good news! Your requested <strong>UPLB ID</strong> is now ready for release.
        </p>

        <div style="background:#f8f9fa;border-left:5px solid #FFB81C;padding:15px 20px;border-radius:4px;margin:25px 0;">
        <strong>Claiming Schedule</strong><br><br>
        Please claim your UPLB ID at Window 10 of the OUR during office hours <strong>starting '.$release_date.' onwards</strong>.
        </div>

        <table cellpadding="8" cellspacing="0" style="border-collapse:collapse;width:100%;margin:20px 0;">
        <tr>
        <td style="font-weight:bold;width:150px;">Office Hours</td>
        <td>Monday to Friday, 8:00 AM – 4:00 PM (except holidays)</td>
        </tr>
        <tr>
        <td style="font-weight:bold;">Location</td>
        <td>
        <strong>Window 10</strong><br>
        <a href="https://maps.app.goo.gl/BuuX9UG7ijmXrfDs8" style="color:#00573F;text-decoration:none;">
        UPLB Office of the University Registrar<br>
        Ground Floor, CAS Annex I Building<br>
        UPLB, College, Laguna
        </a>
        </td>
        </tr>
        </table>

        <p>
        Please bring any required identification or documents when claiming your UPLB ID.
        </p>

        <p>
        Thank you, and we look forward to serving you.
        </p>

        <p>
        Regards,<br>
        <strong>UPLB OUR</strong>
        </p>

        </td>
        </tr>

        <tr>
        <td style="background:#f4f4f4;padding:15px 30px;font-size:12px;color:#777777;text-align:center;border-top:1px solid #e5e5e5;">
        This is an automated email. Please do not reply to this message.
        </td>
        </tr>

        </table>

        </td>
        </tr>
        </table>

        </body>
        </html>';

            $json_data = new stdClass();
            $max_attempts = 2;
            $attempts = 0;
            $email_sent = false;
            while ($attempts < $max_attempts && !$email_sent) {
                try {

                    if ($this->id_send_email($email_data)) {
                        $email_sent = true;
                    }

                } catch (Exception $e) {
                    $this->db->trans_rollback();
                    log_message('error', 'Email Exception: ' . $e->getMessage());

                }

                $attempts++;
            }

            if ($email_sent) {
                $this->General_model->update(["is_sent"=>1],$email_logs_id,"id_email_logs","id");
                $json_data->error = 0;
                $json_data->message = "ID release schedule has been sent!";
                $this->db->trans_commit();
            } else {
                // If it fails after all attempts
                $error = $this->email->print_debugger();
                $json_data->error = 1;
                $json_data->message = "Failed to send email. Try again later.";
                $json_data->error_msgs = $error;
                $this->db->trans_rollback();
                $this->General_model->update(["attempts"=>3],$email_logs_id,"id_email_logs","id");
            }
            echo json_encode($json_data);        
    }  

    public function id_non_compliance(){
        $this->load->model('ID_requests_model');
        $id_request_id = $this->input->post("id_request_id");
        $checked_ids = $this->input->post('checked_ids');
        $student_number = $this->input->post('student_number');
        $picId = $this->input->post('picId');
        $sigId = $this->input->post('sigId');
        $fname = $this->input->post('fname');
        $lname = $this->input->post('lname');
        $mname = $this->input->post('mname');
        $college = $this->input->post('college');
        $birthday = $this->input->post('birthday');
        $address = $this->input->post('address');
        $transaction_id = $this->input->post('transaction_id');
        $rejection_count = $this->input->post('rejection_count');
        $email = $this->input->post('email');

        $non_compliance_text = "";     
           
        $num = 1;
        $first_text = "";
        $non_compliance_text .= "<ul style='margin:10px 0;padding-left:20px;'>";
        if($checked_ids){
            $first_text = "However, one or more of your submitted attachments did not meet the required specifications. To continue processing your request, please upload the corrected attachment(s) using the button below. If any of the information you submitted is incorrect, please review and update it before resubmitting.";

            $non_compliance_text .= "<strong>Attachments Requiring Correction</strong><br><br>";
            foreach ($checked_ids as $checked) {

                switch ($checked) {

                    case "id_picture":
                        $non_compliance_text .= "<li><strong>2×2 ID Picture</strong> – Upload a clear and recent ID picture with a white background in JPG format. Please do not wear glasses.</li>";
                        break;

                    case "signature":
                        $non_compliance_text .= "<li><strong>Electronic Signature</strong> – Upload a clear image of your signature on a white background in JPG format.</li>";
                        break;

                    case "form5":
                        $non_compliance_text .= "<li><strong>Form 5</strong> – Upload your Form 5 for the current semester or term.</li>";
                        break;

                    case "first_year_form5":
                        $non_compliance_text .= "<li><strong>Form 5 During First Year</strong> – Upload your Form 5 during your first year.</li>";
                        break;

                    case "proof_of_payment":
                        $non_compliance_text .= "<li><strong>Proof of Payment</strong> – Upload a valid proof of payment for the ID processing fee.</li>";
                        break;
                }
            }
        }else{
            $first_text = "However, there is an issue with the information you provided. To continue processing your request, please review and correct your inputs before resubmitting your request.";
            $non_compliance_text .= "<li>Please check and update your details</li>";
        }

        $non_compliance_text .= "</ul>";

        $email_data = new stdClass();
        $email_data->to = $email;
        $email_logs_id = $this->General_model->add_data('id_email_logs',["id_request_id"=>$id_request_id,"action"=>'non_compliance', 'staff_name'=>$this->session->userdata(SESSION_DATA)->fullname, 'date_sent'=>date("Y-m-d H:i:s")]);
        $this->db->trans_start();
        $this->General_model->update(
            ["status" => 4],
            $id_request_id,
            "id_requests",
            "id"
        );
        $params = [
            'usp' => 'pp_url',
            'entry.269127344' => $student_number,
            'entry.648354217' => $fname,
            'entry.1125433261' => $mname,
            'entry.1473304815' => $lname,
            'entry.546828452' => $college,
            'entry.1684490786' => $birthday,
            'entry.578970979' => $address,
            'entry.1380865011' => 'NEW: New first year undergraduate',
            'entry.545727886' => $transaction_id,
        ];

        $link = 'https://docs.google.com/forms/d/e/1FAIpQLSeUpDwtLXNOVRH7N5uTvSlrVQ6QKlrkSbw-FVGxr2j-qkl2xg/viewform?' . http_build_query($params);
        $email_data->subject = "UPLB ID Request Update";

        $email_data->message = '
        <!DOCTYPE html>
        <html>
        <body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;">

        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
        <tr>
        <td align="center">

        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px solid #dddddd;border-radius:8px;">

        <tr>
        <td style="background:#00573F;color:#ffffff;padding:20px 30px;font-size:22px;font-weight:bold;">
        UPLB ID Request Update
        </td>
        </tr>

        <tr>
        <td style="padding:30px;font-size:15px;color:#333333;line-height:1.7;">

        <p style="margin-top:0;">Dear <strong>'.$fname.'</strong>,</p>

        <p>
        Thank you for submitting your UPLB ID request.
        </p>

        <p>
        '.$first_text.'
        </p>

        <div style="background:#FFF8E6;border-left:5px solid #FFB81C;padding:15px 20px;border-radius:4px;margin:25px 0;">

            '.$non_compliance_text.'

        </div>

        <div style="text-align:center;margin:30px 0;">
            <a href="'.$link.'" 
            style="background:#00573F;color:#ffffff;text-decoration:none;padding:14px 28px;border-radius:6px;font-weight:bold;display:inline-block;">
                Update Your UPLB ID Request
            </a>
        </div>

        <p>
        Before submitting your new request, please verify that all attachments comply with the specified requirements.
        </p>

        <p>
        If you have any questions or need assistance, please email the UPLB OUR Records section at records_our.uplb@up.edu.ph.
        </p>

        <p>
        Thank you for your cooperation.</p>

        <p>
        Regards,<br>
        <strong>UPLB OUR</strong>
        </p>

        </td>
        </tr>

        <tr>
        <td style="background:#f4f4f4;padding:15px 30px;font-size:12px;color:#777777;text-align:center;border-top:1px solid #e5e5e5;">
        This is an automated email. Please do not reply to this message.
        </td>
        </tr>

        </table>

        </td>
        </tr>
        </table>

        </body>
        </html>';

            $json_data = new stdClass();
            $max_attempts = 2;
            $attempts = 0;
            $email_sent = false;
            while ($attempts < $max_attempts && !$email_sent) {
                try {

                    if ($this->id_send_email($email_data)) {
                        $email_sent = true;
                    $this->General_model->update(["rejection_count"=>$rejection_count],$id_request_id,"id_requests","id");
                    }

                } catch (Exception $e) {
                    $this->db->trans_rollback();
                    log_message('error', 'Email Exception: ' . $e->getMessage());

                }

                $attempts++;
            }

            if ($email_sent) {
                $this->General_model->update(["is_sent"=>1],$email_logs_id,"id_email_logs","id");
                $json_data->error = 0;
                $json_data->message = "ID non-compliance has been sent!";
                $this->db->trans_commit();
            } else {
                // If it fails after all attempts
                $error = $this->email->print_debugger();
                $json_data->error = 1;
                $json_data->message = "Failed to send email. Try again later.";
                $this->db->trans_rollback();
                $this->General_model->update(["attempts"=>3],$email_logs_id,"id_email_logs","id");
            }
            echo json_encode($json_data);   
    }


    public function id_disapprove(){
        $this->load->model('ID_requests_model');
        $id_request_id = $this->input->post("id_request_id");
        $student_number = $this->input->post('student_number');
        $fname = $this->input->post('fname');
        $email = $this->input->post('email');

        $email_data = new stdClass();
        $email_data->to = $email;
        $email_logs_id = $this->General_model->add_data('id_email_logs',["id_request_id"=>$id_request_id,"action"=>'disapproved', 'staff_name'=>$this->session->userdata(SESSION_DATA)->fullname, 'date_sent'=>date("Y-m-d H:i:s")]);
        $this->db->trans_start();
        $this->General_model->update(
            ["status" => 2],
            $id_request_id,
            "id_requests",
            "id"
        );

        $email_data->subject = "UPLB ID Request Update";

        $email_data->message = "
        Good day, $fname!

        <br><br>

        Please be informed that your UPLB ID request has been disapproved.

        <br><br>

        For further details regarding your request, you may contact the Records Section at 
        <a href='mailto:records_our.uplb@up.edu.ph'>records_our.uplb@up.edu.ph</a>.

        <br><br>

        Thank you.

        <br><br>

        This is an automatically generated message. Please do not reply to this email.
        ";


            $json_data = new stdClass();
            $max_attempts = 2;
            $attempts = 0;
            $email_sent = false;
            while ($attempts < $max_attempts && !$email_sent) {
                try {

                    if ($this->id_send_email($email_data)) {
                        $email_sent = true;
                    }

                } catch (Exception $e) {
                    $this->db->trans_rollback();
                    log_message('error', 'Email Exception: ' . $e->getMessage());

                }

                $attempts++;
            }

            if ($email_sent) {
                $this->General_model->update(["is_sent"=>1],$email_logs_id,"id_email_logs","id");
                $json_data->error = 0;
                $json_data->message = "ID disapproval has been sent!";
                $this->db->trans_commit();
            } else {
                // If it fails after all attempts
                $error = $this->email->print_debugger();
                $json_data->error = 1;
                $json_data->message = "Failed to send email. Try again later.";
                $this->db->trans_rollback();
                $this->General_model->update(["attempts"=>3],$email_logs_id,"id_email_logs","id");
            }
            echo json_encode($json_data);   
    }  

}