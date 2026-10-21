<?php  
	class Clients_model extends CI_Model{		
		var $column_order = array('a.id','a.ticket_number','a.called','a.status','a.priority'); //set column field database for datatable orderable
		var $column_search = array('a.id','a.ticket_number','a.called','a.status','a.priority'); //set column field database for datatable searchable just firstname , lastname , address are searchable
		var $order = array('a.status' => 'ASC','a.ticket_number'=>'ASC','a.priority'=>"DESC"); // default order   

		public function __construct(){
			parent::__construct();
			$this->load->database();
		}
		 
		public function _get_datatables_query(){ 			
			$this->db->from('queues a'); 
			$this->db->select('a.id, a.ticket_number, a.called, a.status, a.priority, a.time_created, c.id as window_id, a.time_called'); 
			$this->db->join('services b','b.id = a.service_id'); 
			$this->db->join('windows c','c.service_id = b.id'); 
			$i = 0;
			$this->db->where('a.date_created',date("Y-m-d"));
			$this->db->where('c.id',$this->session->userdata(SESSION_DATA)->window_id);
			foreach ($this->column_search as $item) { // loop column 
				if($_POST['search']['value']) // if datatable send POST for search
				{
					if($i===0) // first loop
					{
						$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						$this->db->like($item, $_POST['search']['value']);
					}
					else
					{
						$this->db->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->db->group_end(); //close bracket
				}
				$i++;
			}
			
			if(isset($_POST['order'])){ // here order processing
				$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
			} 

			else if(isset($this->order)){
				$order = $this->order;
				foreach($order as $key=>$val){
					$this->db->order_by($key, $val);
				}
			} 
		}  

		function get_datatables(){
			$this->_get_datatables_query(); 
			if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']); 
			$query = $this->db->get();
			return $query->result();
		}

		function count_filtered(){
			$this->_get_datatables_query();
			$query = $this->db->get();
			return $query->num_rows();
		}

		public function count_all(){
			$this->db->from('queues a');
			$this->db->join('services b','b.id = a.service_id'); 
			$this->db->join('windows c','c.service_id = b.id'); 
			$this->db->where('a.date_created',date("Y-m-d"));
			$this->db->where('c.id',$this->session->userdata(SESSION_DATA)->window_id);
			$query = $this->db->get();
			return $query->num_rows();
		}   

		public function get_lastclient(){  
			$this->db->select('qclientid'); 
			$this->db->from('queues');   
			$this->db->order_by('queueid', 'DESC'); 
			$this->db->LIMIT(1); 
			$query= $this->db->get();
			$result = $query->result();   
			return $result[0]->qclientid; 
		}
		public function get_lastnumber($today,$serviceid,$priority){  
			$this->db->select('ticket_number'); 
			$this->db->from('queues');
			$this->db->where('service_id',$serviceid);
			$this->db->where('date_created',$today);   
			$this->db->where('priority',$priority);   
			$this->db->order_by('id', 'DESC'); 
			$this->db->LIMIT(1); 
			$query= $this->db->get();
			$result = $query->result(); 
			if($result){
				return $result[0]->ticket_number; 
			}else{
				return false;
			}
		}

		public function get_counterid($serviceid){  
			$this->db->select('counterid'); 
			$this->db->from('counters');
			$this->db->where('counters.assignedservice',$serviceid);   
			$query= $this->db->get();
			$result = $query->result();   
			return $result[0]->counterid; 
		}

		public function get_countername($serviceid){  
			$this->db->select('countername'); 
			$this->db->from('counters');
			$this->db->where('counters.assignedservice',$serviceid);   
			return $this->db->get()->result_object();    
		}

		public function get_clients_today(){
			$this->db->select('a.id, a.priority, a.status');
			$this->db->from('queues a');
			$this->db->join('services b','b.id = a.service_id'); 
			$this->db->join('windows c','c.service_id = b.id'); 
			$i = 0;
			$this->db->where('a.date_created',date("Y-m-d"));
			$this->db->where('c.id',$this->session->userdata(SESSION_DATA)->window_id);
			// $this->db->where('a.status','0');
			// $this->db->where('a.priority',$priority);
			return $this->db->get()->result_object();    
		}

		public function get_queue($queue_id){
			$this->db->select('a.*, c.description as window, a.window_id as window_owner, c.id as window_id, b.description as service');
			$this->db->from('queues a');
			$this->db->join('services b','b.id = a.service_id'); 
			$this->db->join('windows c','c.service_id = b.id'); 
			$i = 0;
			$this->db->where('a.id',$queue_id);
			$query= $this->db->get();
			$result = $query->result();   
			return $result[0]; 
		}

		public function call_client($priority){
			$this->db->select('a.*, c.description as window, a.window_id as window_owner, c.id as window_id, b.description as service');
			$this->db->from('queues a');
			$this->db->join('services b','b.id = a.service_id'); 
			$this->db->join('windows c','c.service_id = b.id'); 
			$i = 0;
			if($priority != 0){
				$this->db->where('a.priority',$priority);
			}
			$this->db->where('a.called','0');
			$this->db->where('a.date_created',date("Y-m-d"));
			$this->db->where('c.id',$this->session->userdata(SESSION_DATA)->window_id);
			//order by ticket number ASC if the same then order by priority DESC
			$this->db->order_by('a.ticket_number ASC, priority DESC','',false);
			$query= $this->db->get();
			$result = $query->result();  
			if($result){
				return $result[0]; 
			}else{
				return false;
			}
		}

		public function update_time_finished(){
			$this->db->where('called', 1); 
			$this->db->where('status', 0); 
			$this->db->where('user_id', $this->session->userdata(SESSION_DATA)->uid); 
			$this->db->where('date_created', date("Y-m-d")); 
			$this->db->where('time_finished is null', '',false); 
			$this->db->order_by('id', 'DESC'); 
			$this->db->limit('1'); 
			$this->db->update('queues', ['time_finished'=>date("H:i:s"), "status"=>2]); 
			return ($this->db->affected_rows() > 0) ? TRUE : FALSE; 
		}

		public function update_missed_call(){
			$this->db->where('called', 1); 
			$this->db->where('status', 0); 
			$this->db->where('user_id', $this->session->userdata(SESSION_DATA)->uid); 
			$this->db->where('date_created', date("Y-m-d")); 
			$this->db->where('time_finished is null', '',false); 
			$this->db->order_by('id', 'DESC'); 
			$this->db->limit('1'); 
			$this->db->update('queues', ["status"=>1]); 
			return ($this->db->affected_rows() > 0) ? TRUE : FALSE; 
		}

		public function get_last_call(){
			$this->db->select('IF(priority =1,CONCAT("P",ticket_number),ticket_number) as ticket_number');
			$this->db->from('queues');
			$this->db->where('called', 1); 
			$this->db->where('user_id', $this->session->userdata(SESSION_DATA)->uid); 
			$this->db->where('date_created', date("Y-m-d")); 
			$this->db->order_by('time_called', 'DESC'); 
			$this->db->limit('1'); 
			$query= $this->db->get();
			$result = $query->result();  
			if($result){
				return $result[0]; 
			}else{
				return false;
			}
		}

		public function call_latest(){
			$this->db->select('IF(priority =1,CONCAT("P",ticket_number),ticket_number) as ticket_number, call_count, id');
			$this->db->from('queues');
			$this->db->where('called', 1); 
			$this->db->where('status', 0); 
			$this->db->where('user_id', $this->session->userdata(SESSION_DATA)->uid); 
			$this->db->where('date_created', date("Y-m-d")); 
			$this->db->where('time_finished is null', '',false); 
			$this->db->order_by('id', 'DESC'); 
			$this->db->limit('1'); 
			$query= $this->db->get();
			$result = $query->result();  
			if($result){
				return $result[0]; 
			}else{
				return false;
			}
		}
	}
?>