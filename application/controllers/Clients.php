<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Clients_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

	public function get_txt($filename){
		$arrContextOptions=array(
		    "ssl"=>array(
		        "verify_peer"=>false,
		        "verify_peer_name"=>false,
		    ),
		);  
		$path = base_url("assets/new_queue/");
		return file_get_contents($path.$filename,false, stream_context_create($arrContextOptions));
	}

	public function new_clients(){
		header("Content-Type: text/event-stream");
		header("Cache-Control: no-cache");
		header("Connection: keep-alive");
		session_write_close();
		$data = new stdClass();
		$data->total = 0;
		$data->new_regular = 0;
		$data->new_priority = 0;
		$data->total_regular = 0;
		$data->total_priority = 0;
		$count = 0;
		while (1) { // 1 is always true, so repeat the while loop forever
			$clients_today = $this->Clients_model->get_clients_today();
			$count_new_regular = 0;
			$count_new_priority = 0;
			$count_total_regular = 0;
			$count_total_priority = 0;
			if($clients_today){
				foreach($clients_today as $val){
					if($val->status == 0 && $val->priority == 0){$count_new_regular++;}
					if($val->status == 0 && $val->priority == 1){$count_new_priority++;}
					if($val->priority == 0){$count_total_regular++;}
					if($val->priority == 1){$count_total_priority++;}
				}
				if($data->new_regular != $count_new_regular || $data->new_priority != $count_new_priority || $data->total_regular!=$count_total_regular || $data->total_priority != $count_total_priority)
				{
					$data->total = $count_total_regular + $count_total_priority;
					$data->new_regular = $count_new_regular;
					$data->new_priority = $count_new_priority;
					$data->total_regular = $count_total_regular;
					$data->total_priority = $count_total_priority;
					echo 'data:'.json_encode($data)."\n\n";
				}
			}else{
				//return data if total is 0 on 1st iteration
				if($count == 0){
				echo 'data:'.json_encode($data)."\n\n";
				}
			}
			if (ob_get_level() > 0) {
				// flush the output buffer and send echoed messages to the browser
				while(ob_get_level() > 0) {
					ob_end_flush();
				}
			}

			flush();
			// break the loop if the client aborted the connection (closed the page)
			if(connection_aborted()) {
				break;
			}
			//increment count
			$count++;
			// sleep for 5 second before running the loop again
			sleep(1);
		}
	}

	public function check_total(){
		$total = $this->input->post('total');
		//if current totla number of client id not equal to the total number of client in session
		if($total != $this->session->userdata('total_client')){
			//update the total number of client in session to the current total number of client
			$this->session->set_userdata('total_client',$total);
			//return true to trigger notification of new client
			echo json_encode(1);
		}else{
			//return false
			echo json_encode(0);
		}
	}

	public function services(){
		$this->isAllowed();
		$this->data['collapse'] = true;
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'clients/services.php';
		$this->data['links'] = $this->linkGenerator();
		$this->data['services'] = $this->General_model->list_dynamic('services',[]);
		$this->load->view('template/admin', $this->data);
	}

	public function add_queue(){
		$service_id = $this->input->post('service_id');
		$default_number = $this->input->post('default_number');
		$priority = $this->input->post('priority')==null?0:1;
		$today = date('Y-m-d');
		$time = date('H:i:s');
		$newticket = $this->Clients_model->get_lastnumber($today,$service_id,$priority); 
		$data = new stdCLass();
		$data->service_id = $service_id;
		$data->priority = $priority==null?0:$priority;
		$data->date_created = $today;
		$data->time_created = $time;
		if($newticket){
			$data->ticket_number = $newticket+1;
		}else{
			$data->ticket_number = $default_number+1;
		}
		$queue_id = $this->General_model->add_data('queues',$data);
		if($priority == 1){
			$data->ticket_number = "P".$data->ticket_number;
		}
		$json_data = new stdClass();
		$json_data->ticket_number = $data->ticket_number;
		$json_data->queue_id = $queue_id;
		$json_data->priority = $priority;
		echo json_encode($json_data);
	}

	public function print_ticket()
	{ 	
		$this->data['content'] = 'clients/print_ticket'; 
		// $json_input = $this->input->raw_input_stream;
		$json_input = $this->input->post("jsonData");

        // Decode the JSON data into a PHP associative array
        $data = json_decode($json_input,true); 	
		$queue_id=$data['queue_id'];   
		$this->data['data'] = $this->Clients_model->get_queue($queue_id);     
		$this->load->view('template/print_head', $this->data);
	}  

	public function call(){
		$this->isAllowed();
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['content'] = 'clients/call.php';
		$this->data['links'] = $this->linkGenerator();
		$this->data['services'] = $this->General_model->list_dynamic('services',[]);
		$this->load->view('template/admin', $this->data);
	}

	public function call_client(){
		$queue_id = $this->input->post('queue_id');
		$window_id = $this->session->userdata(SESSION_DATA)->window_id;
		$user_id = $this->session->userdata(SESSION_DATA)->uid;
		$queue_data = $this->Clients_model->get_queue($queue_id);
		$json_data = new stdCLass();
		$path = (FCPATH)."assets/new_queue/";
		$filename = $this->session->userdata(SESSION_DATA)->window_id.".txt"; 	

		if($queue_data){

			if(!file_exists($path.$filename)){
				$contents = ($queue_data->priority==1)?"P".$queue_data->ticket_number:$queue_data->ticket_number;
				fopen($path.$filename, 'w');
				sleep(1);
				file_put_contents($path.$filename, $contents); 
			}else{
				$text_file = $this->get_txt($filename);
				$db = ($queue_data->priority==1)?"P".$queue_data->ticket_number:$queue_data->ticket_number;
				if($db == $text_file){
					$contents = $db.' ';
				}else{
					$contents = $db;
				}
				file_put_contents($path.$filename, $contents); 
			}	
			$time_called = date('H:i:s');
			if($queue_data->called == 1){
				if($queue_data->window_owner != $this->session->userdata(SESSION_DATA)->window_id){
					$json_data->message = "Ticket Number Already Called by Window ".$queue_data->window;
					$json_data->error = 1;
				}else{
					$call_count = $queue_data->call_count+1;
					$data = ['call_count'=>$call_count, 'time_last_called'=>$time_called];
					$json_data->message = "Ticket Number is called again.";
					$json_data->error = 0;
					$json_data->time_called = $queue_data->time_called;
					$update = $this->General_model->update($data,$queue_id,'queues','id');
				}
			}else{
				$json_data->message = "Ticket Number is called";
				$json_data->error = 0;
				$json_data->time_called = $time_called;
				$data = ['called'=>1,'user_id'=>$user_id, 'window_id'=>$window_id, 'time_called'=>$time_called,'time_last_called'=>$time_called,'call_count'=>1];
				$update = $this->General_model->update($data,$queue_id,'queues','id');
			}
		}
		echo json_encode($json_data);
	}

	public function call_next(){
		$priority = $this->input->post('priority');
		$window_id = $this->session->userdata(SESSION_DATA)->window_id;
		$user_id = $this->session->userdata(SESSION_DATA)->uid;
		$queue_data = $this->Clients_model->call_client($priority);
		$last_data = $this->Clients_model->update_time_finished();
		$json_data = new stdCLass();
		$path = (FCPATH)."assets/new_queue/";
		$filename = $this->session->userdata(SESSION_DATA)->window_id.".txt"; 	

		if($queue_data){
			$ticket_number = ($queue_data->priority==1)?"P".$queue_data->ticket_number:$queue_data->ticket_number;
			if(!file_exists($path.$filename)){
				$contents = $ticket_number;
				fopen($path.$filename, 'w');
				sleep(1);
				file_put_contents($path.$filename, $contents); 
			}else{
				$text_file = $this->get_txt($filename);
				$db = ($queue_data->priority==1)?"P".$queue_data->ticket_number:$queue_data->ticket_number;
				if($db == $text_file){
					$contents = $db.' ';
				}else{
					$contents = $db;
				}
				file_put_contents($path.$filename, $contents); 
			}	
			$time_called = date('H:i:s');
			$call_count = 1;
			$data = ['called'=>1,'user_id'=>$user_id, 'window_id'=>$window_id, 'time_called'=>$time_called,'time_last_called'=>$time_called,'call_count'=>1];
			$json_data->message = "Ticket Number $ticket_number is called.";
			$json_data->error = 0;
			$json_data->ticket_number = $ticket_number;
			$update = $this->General_model->update($data,$queue_data->id,'queues','id');
		}else{
			$json_data->error = 1;
			$json_data->message = "No more client available.";
		}
		echo json_encode($json_data);
	}

	public function call_again(){
		$queue_id = $this->input->post('queue_id');
		$queue_data = $this->Clients_model->get_queue($queue_id);
		$call_count = $queue_data->call_count+1;
		$data = ['time_last_called'=>date("H:i:s"),'call_count'=>$call_count];
		$update = $this->General_model->update($data,$queue_id,'queues','id');

		$path = (FCPATH)."assets/new_queue/";
		$filename = $this->session->userdata(SESSION_DATA)->window_id.".txt";
		if(!file_exists($path.$filename)){
			$contents = "0";
			fopen($path.$filename, 'w');
			sleep(1);
			file_put_contents($path.$filename, $contents); 
		}
		else{
			$contents = $this->get_txt($filename).' ';
			file_put_contents($path.$filename, $contents); 
		}
			
		echo json_encode($update);
	}

	public function call_latest(){
		$queue_data = $this->Clients_model->call_latest();
		$json_data = new stdClass();
		if($queue_data){
			$call_count = $queue_data->call_count+1;
			$data = ['time_last_called'=>date("H:i:s"),'call_count'=>$call_count];
			$update = $this->General_model->update($data,$queue_data->id,'queues','id');

			$path = (FCPATH)."assets/new_queue/";
			$filename = $this->session->userdata(SESSION_DATA)->window_id.".txt";
			if(!file_exists($path.$filename)){
				$contents = "0";
				fopen($path.$filename, 'w');
				sleep(1);
				file_put_contents($path.$filename, $contents); 
			}
			else{
				$contents = $this->get_txt($filename).' ';
				file_put_contents($path.$filename, $contents); 
			}
			$json_data->error = 0;
			$json_data->ticket_number = $queue_data->ticket_number;
		}else{
			$json_data->error = 1;
		}
		echo json_encode($json_data);
	}

	public function missed(){
		$queue_id = $this->input->post('queue_id');
		$data = ['status'=>1];
		if($queue_id == 0){
			$update = $this->Clients_model->update_missed_call();
		}else{
			$update = $this->General_model->update($data,$queue_id,'queues','id');
		}
		echo json_encode($update);
	}

	public function break(){
		$last_data = $this->Clients_model->update_time_finished();
		echo json_encode($last_data);
	}

	public function served(){
		$queue_id = $this->input->post('queue_id');
		$data = ['status'=>2, "time_finished"=>date('H:i:s')];
		$update = $this->General_model->update($data,$queue_id,'queues','id');
			
		echo json_encode($update);
	}

	public function get_last_call(){
		$data = $this->Clients_model->get_last_call();
			
		echo json_encode($data);
	}

	public function get_client_list() {
    	$this->load->model('Users_model');
    	$this->load->model('General_model');
        $list = $this->Clients_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
        	switch($val->status){
        		case '1':
        			$status = 'Missed';
    			break;
    			case "2":
    				$status = 'Served';
				break;
				default:
					$status = 'Queued';
				break;
        	}
        	$ticket_number = ($val->priority == 1)?"P".$val->ticket_number:$val->ticket_number;
        $row = array();
        $row[] = $ticket_number;
        $row[] = $val->time_created;
        // $row[] = $val->priority==1?"<span><i class='fa fa-check text-danger'></i></span>":"<span><i class='fa fa-close'></i></span>";
        $call = $val->called==1?"<span><i class='fa fa-check'></i></span>":"<span><i class='fa fa-close'></i></span>";
        $call .= " ".$val->time_called;
        $row[] = $call;
				$row[] = $status;
				if($val->status != 2){
    		$actions="<center>
							<button class='btn btn-primary btn-sm call_btn' 
							queue_id='$val->id'
							ticket_number='$val->ticket_number'
							time_created='$val->time_created'
							>
								<span>
									<i class='fa fa-bullhorn'></i>
								</span>
							</button>
						</center>";
				}else{
					$actions='';
				}
				$row[] = $actions;
				$row[] = $val->priority;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Clients_model->count_all(),
                        "recordsFiltered" => $this->Clients_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
	}

	public function get_client_list_priority() {
    	$this->load->model('Users_model');
    	$this->load->model('General_model');
		$list = $this->Clients_model->get_datatables();
		$data = array();
		foreach ($list as $val) {
			switch($val->status){
				case '1':
					$status = 'Missed';
				break;
				case "2":
					$status = 'Served';
			break;
			default:
				$status = 'Queued';
			break;
			}
		$row = array();
		$row[] = "P".$val->ticket_number;
		$row[] = $val->time_created;
		// $row[] = $val->priority==1?"<span><i class='fa fa-check text-danger'></i></span>":"<span><i class='fa fa-close'></i></span>";
		$call = $val->called==1?"<span><i class='fa fa-check'></i></span>":"<span><i class='fa fa-close'></i></span>";
		$call .= " ".$val->time_called;
		$row[] = $call;
			$row[] = $status;
			if($val->status != 2){
			$actions="<center>
						<button class='btn btn-primary btn-sm call_btn' 
						queue_id='$val->id'
						ticket_number='P$val->ticket_number'
						time_created='$val->time_created'
						>
							<span>
								<i class='fa fa-bullhorn'></i>
							</span>
						</button>
					</center>";
			}else{
				$actions='';
			}
			$row[] = $actions;
		   $data[] = $row;
		}

		$output = array(
		               "draw" => $_POST['draw'],
		               "recordsTotal" => $this->Clients_model->count_all(),
		               "recordsFiltered" => $this->Clients_model->count_filtered(),
		               "data" => $data,
		       );
		//output to json format
		echo json_encode($output);
  	}

    public function forbidden()
	{
		$this->load->view('errors/index.html');
	} 
}