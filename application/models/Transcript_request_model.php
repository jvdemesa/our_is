<?php  
	class Transcript_request_model extends CI_Model{		
		var $column_order = array('a.rownum'); //set column field database for datatable orderable
		var $column_search = array('a.email','a.fname','a.mname','a.lname','a.student_no','a.trans_code'); //set column field database for datatable searchable just firstname , lastname , address are searchable
		var $order = array('a.rownum' => 'DESC'); // default order   

		public function __construct(){
			parent::__construct();
			$this->load->database();
		}
		 
		public function _get_datatables_query(){ 	
			$this->db->select('
				MAX(a.fname) as fname,
				MAX(a.mname) as mname,
				MAX(a.lname) as lname,
				MAX(a.timestamp) as timestamp,
				MAX(a.email) as email,
				MAX(a.is_printed) as is_printed,
			    MAX(a.id) AS id,
			    MAX(a.trans_code) AS trans_code,
			    MAX(a.student_no) AS student_no,
			    MAX(a.status) AS status,
			    MAX(a.active_status) AS active_status,
			    MAX(a.assigned_to) AS assigned_to_id,
			    MAX(a.rownum) AS rownum,
			    GROUP_CONCAT(CONCAT("(<b>", b.quantity, "</b>) ", c.request) SEPARATOR "<HR>") AS request_lists,
			    CONCAT(MAX(u.first_name), " ", MAX(u.last_name)) AS assigned_to,
			    MAX(d.timestamp) AS payment_date,
			    DATE_FORMAT(MAX(e.date_approved), "%M %d&#44; %Y") AS date_approved,
			    GROUP_CONCAT(DISTINCT e.college) AS clearance_type,
			    MAX(CASE WHEN b.rt_id = 1 THEN 1 ELSE 0 END) AS has_otr,
			    MAX(CASE WHEN b.rt_id = 1 THEN b.otr_steps ELSE 0 END) AS otr_step,
			    MAX(CASE WHEN b.rt_id = 1 THEN b.id ELSE 0 END) AS trq_id,
			');
			$this->db->from('tr_details a'); 
			$this->db->join('tr_quantity b','b.trans_code = a.trans_code','LEFT'); 
			$this->db->join('transcript_request_type c','c.id = b.rt_id','LEFT'); 
			$this->db->join('tr_payments d','d.trans_code = a.trans_code','LEFT'); 
			$this->db->join('tr_clearance e','e.student_no = a.student_no','LEFT'); 
			$this->db->join('users u','u.uid = a.assigned_to','LEFT'); 
			$i = 0;
			// $this->db->where('a.date_created',date("Y-m-d"));
			// if(!in_array("1", $this->session->userdata(SESSION_DATA)->access)){
			// 	$this->db->where('a.uploaded_by',$this->session->userdata(SESSION_DATA)->uid);
			// }
			if($this->input->post("assigned_to")!=""){
				$this->db->where('a.assigned_to',$this->input->post("assigned_to"));
			}
			if($this->input->post("status")!=""){
				$this->db->where('a.status',$this->input->post("status"));
			}
			if($this->input->post("active_status")!=""){
				$this->db->where('a.active_status',$this->input->post("active_status"));
			}
			if ($this->input->post("is3days")==1) {
				$three_days_ago = date('Y-m-d H:i:s', strtotime('-3 days'));
			    $this->db->group_start();  // Starting the group of conditions
			    if ($this->input->post("status") == 0) {
			        $this->db->where('a.timestamp <', $three_days_ago);
			    } elseif ($this->input->post("status") == 3) {
			        $this->db->where('d.timestamp <', $three_days_ago);
			    } else {
			        $this->db->where('a.timestamp <', $three_days_ago);
			        $this->db->or_where('d.timestamp <', $three_days_ago);
			    }
			    $this->db->group_end();  // Ending the group of conditions
			}
			$this->db->group_by('a.trans_code');
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
			
			if (isset($_POST['order'])) {
			    // DataTables dynamic ordering
			    $order_column = $this->column_order[$_POST['order']['0']['column']];
			    $order_dir    = $_POST['order']['0']['dir'];

			    // Wrap non-grouped columns in MAX()
			    if (in_array($order_column, ['a.rownum','a.id','a.status','a.assigned_to'])) {
			        $this->db->order_by("MAX($order_column)", $order_dir, false);
			    } else {
			        $this->db->order_by($order_column, $order_dir);
			    }
			}
			else if (isset($this->order)) {
			    // Default ordering fallback
			    foreach ($this->order as $key => $val) {
			        if (in_array($key, ['a.rownum','a.id','a.status','a.assigned_to'])) {
			            $this->db->order_by("MAX($key)", $val, false);
			        } else {
			            $this->db->order_by($key, $val);
			        }
			    }
			}
		}
  

		function get_datatables(){
			$this->_get_datatables_query(); 
			if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']); 
			$query = $this->db->get();
			// echo $this->db->last_query();
			// exit();
			return $query->result();
		}

		function count_filtered(){
			$this->_get_datatables_query();
			$query = $this->db->get();
			return $query->num_rows();
		}

		public function count_all(){
			$this->db->from('tr_details a'); 
			$i = 0;
			$query = $this->db->get();
			return $query->num_rows();
		}   

		public function list_requests($where){
			$this->db->select('a.*,b.request, b.price, b.is_per_page'); 
			$this->db->from('tr_quantity a'); 
			$this->db->join('transcript_request_type b','b.id = a.rt_id'); 
			foreach($where as $key=>$val){
				$this->db->where('a.'.$key,$val);
			}
			$this->db->where('a.quantity !=',0);
			$this->db->where('a.pages !=',0);
			$query = $this->db->get('');
			return $query->result();	
		}   

		public function is_not_released($trans_code){
			$this->db->select('a.*,b.request, b.price, b.is_per_page'); 
			$this->db->from('tr_quantity a'); 
			$this->db->join('transcript_request_type b','b.id = a.rt_id'); 
			$this->db->where('a.trans_code',$trans_code);
			$this->db->where('a.quantity !=',0);
			$this->db->where('a.pages !=',0);
			$this->db->where('a.status <=',1);
			$query = $this->db->get('');
			return $query->result();	
		}   

		public function new_request($where){
			$this->db->select('a.*, DATE_FORMAT(a.release_date, "%b %d, %Y %h:%i:%s %p") as release_date, DATE_FORMAT(a.ready_date, "%b %d, %Y %h:%i:%s %p") as ready_date, b.request, b.price, b.is_per_page, c.purpose'); 
			$this->db->from('tr_quantity a'); 
			$this->db->join('transcript_request_type b','b.id = a.rt_id'); 
			$this->db->join('tr_purpose c','c.trans_code = a.trans_code','LEFT'); 
			foreach($where as $key=>$val){
				$this->db->where('a.'.$key,$val);
			}
			$this->db->where('a.quantity !=',0);
			$query = $this->db->get('');
			return $query->result();	
		}   

		public function get_request_list_via_id($id,$status){
			$this->db->select('a.*,b.request, b.price, b.is_per_page'); 
			$this->db->from('tr_quantity a'); 
			$this->db->join('transcript_request_type b','b.id = a.rt_id'); 
			$this->db->where_in('a.id',$id);
			$this->db->where('a.quantity !=',0);
			$this->db->where('a.pages !=',0);
			$this->db->where('a.status',$status);
			$query = $this->db->get('');
			return $query->result();	
		}   

		public function get_tr_details($id){
			$this->db->select('a.*,b.timestamp as payment_upload_date, b.file_link, c.purpose, u.first_name as staff_name, GROUP_CONCAT(DATE_FORMAT(e.date_approved, "%M %d&#44; %Y"),"-",e.college SEPARATOR "<HR>") as date_approved'); 
			$this->db->from('tr_details a'); 
			$this->db->join('tr_payments b','b.trans_code = a.trans_code','LEFT'); 
			$this->db->join('tr_purpose c','c.trans_code = a.trans_code','LEFT'); 
			$this->db->join('tr_clearance e','e.student_no = a.student_no','LEFT'); 
			$this->db->join('users u','u.uid = a.assigned_to','LEFT'); 
			$this->db->where('a.id',$id);
			$this->db->group_by('a.id');
			$query = $this->db->get('');
			return $query->result();	
		}   

		public function get_tr_details_trans_code($trans_code){
			$this->db->select('a.*,b.timestamp as payment_upload_date, b.file_link, c.purpose, u.first_name as staff_name, DATE_FORMAT(e.date_approved, "%M %d&#44; %Y") as date_approved'); 
			$this->db->from('tr_details a'); 
			$this->db->join('tr_payments b','b.trans_code = a.trans_code','LEFT'); 
			$this->db->join('tr_purpose c','c.trans_code = a.trans_code','LEFT'); 
			$this->db->join('tr_clearance e','e.student_no = a.student_no','LEFT'); 
			$this->db->join('users u','u.uid = a.assigned_to','LEFT'); 
			$this->db->where('a.trans_code',$trans_code);
			$query = $this->db->get('');
			return $query->result();	
		}   

		public function delete_trq($id){
			$this->db->where_in('id', $id);
			$this->db->update('tr_quantity',["is_deleted"=>1]);
			return ($this->db->affected_rows() > 0) ? TRUE : FALSE; 
		}   

		public function get_active_status($trans_code){
			$this->db->select('*');
			$this->db->from('tr_active_status');
			$this->db->where('trans_code',$trans_code);
			$this->db->order_by('id',"DESC");
			$query = $this->db->get();
			//echo $this->db->last_query();
			
			$result = $query->result();
			return $result;
		}	

		public function get_new_requests(){
			$this->db->select('count(*) as num'); 
			$this->db->from('tr_details a'); 
			if ($this->input->post("assigned_to") != "") {
			    $this->db->where('a.assigned_to', $this->input->post("assigned_to"));
			}

			if ($this->input->post("status") != "") {
			    $this->db->where('a.status', $this->input->post("status"));
			}

			if ($this->input->post("active_status") != "") {
			    $this->db->where('a.active_status', $this->input->post("active_status"));
			}

			$this->db->where('a.timestamp <', 'DATE_SUB(NOW(), INTERVAL 3 DAY)', FALSE);
			$this->db->where('a.status', "0");
			$query = $this->db->get();
			//echo $this->db->last_query();
			
			$result = $query->result();
			if($result){
				return $result[0]->num;
			}else{
				return 0;
			}
		}		

		public function get_paid(){
			$this->db->select('count(a.id) as num'); 
			$this->db->from('tr_details a'); 
			$this->db->join('(select MAX(id) as id, MAX(trans_code) as trans_code, MAX(rownum) as rownum, MAX(timestamp) as timestamp, MAX(email) as email, MAX(refnum) as refnum, MAX(file_link) as file_link, MAX(is_found) as is_found  from tr_payments group by trans_code) b', 'b.trans_code = a.trans_code', 'LEFT');

			if ($this->input->post("assigned_to") != "") {
			    $this->db->where('a.assigned_to', $this->input->post("assigned_to"));
			}

			if ($this->input->post("status") != "") {
			    $this->db->where('a.status', $this->input->post("status"));
			}

			if ($this->input->post("active_status") != "") {
			    $this->db->where('a.active_status', $this->input->post("active_status"));
			}

			$this->db->where('b.timestamp <', 'DATE_SUB(NOW(), INTERVAL 3 DAY)', FALSE);
			$this->db->where('a.status', "3");
			$query = $this->db->get();
			//echo $this->db->last_query();
			
			$result = $query->result();
			if($result){
				return $result[0]->num;
			}else{
				return 0;
			}
		}	

		public function look_up($lname){ 
			$this->db->select('*');
			$this->db->from('tr_old_records');
			$this->db->LIKE('fullname',$lname);
			$this->db->order_by('id',"DESC");
			$query = $this->db->get();
			//echo $this->db->last_query();
			
			$result = $query->result();
			return $result;
		}	
	}
?>