<?php  
	class Nf_list_model extends CI_Model{		
		var $column_order = array('a.batch_no'); //set column field database for datatable orderable
		var $column_search = array('a.batch_no','a.date'); //set column field database for datatable searchable just firstname , lastname , address are searchable
		var $order = array('a.id' => 'DESC'); // default order   

		public function __construct(){
			parent::__construct();
			$this->load->database();
		}
		 
		public function _get_datatables_query(){ 	
			$this->db->select('a.id, a.batch_no, CONCAT(b.first_name," ",b.last_name) as uploaded_by, a.date, a.time, count(c.id) as batch_total'); 
			$this->db->from('nf_list_batch a'); 
			$this->db->join('users b','b.uid = a.uploaded_by','LEFT'); 
			$this->db->join('nf_list c','c.batch_id = a.id','LEFT'); 
			$i = 0;
			// $this->db->where('a.date_created',date("Y-m-d"));
			if(!in_array("1", $this->session->userdata(SESSION_DATA)->access)){
				$this->db->where('a.uploaded_by',$this->session->userdata(SESSION_DATA)->uid);
			}
			$this->db->group_by('a.batch_no, a.uploaded_by');
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
			$this->db->from('nf_list_batch a'); 
			$this->db->join('users b','b.uid = a.uploaded_by','LEFT'); 
			$this->db->join('nf_list c','c.batch_id = a.id','LEFT'); 
			$i = 0;
			// $this->db->where('a.date_created',date("Y-m-d"));
			if(!in_array("1", $this->session->userdata(SESSION_DATA)->access)){
				$this->db->where('a.uploaded_by',$this->session->userdata(SESSION_DATA)->uid);
			}
			$query = $this->db->get();
			return $query->num_rows();
		}   

		public function get_student_batch($batch_id){
			$this->db->select('b.sais_id, a.campus_id, b.first_name, b.middle_name, b.last_name, a.degree, a.classification, a.classification, c.code, b.term, b.career'); 
			$this->db->from('students_raw a'); 
			$this->db->join('students b','b.campus_id = a.campus_id','LEFT'); 
			$this->db->join('classification c','c.classification = a.classification','LEFT'); 
			$this->db->where('a.batch_id',$batch_id);
			$this->db->group_by('a.campus_id');
			$this->db->order_by('b.sais_id',"ASC");
			$query = $this->db->get();
			return $query->result();
		}   

		public function get_nf_list($batch_id){
			$this->db->select('a.*, b.time, b.days'); 
			$this->db->from('nf_list a'); 
			$this->db->join('(select * from nf_sched group by subject, section) b','b.section = a.section AND b.subject = a.subject AND a.year = b.year AND a.sem = b.sem','LEFT'); 
			$this->db->where('a.batch_id',$batch_id);
			$query = $this->db->get();
			// echo $this->db->last_query();
			// exit();
			return $query->result();
		}   

		public function generate_batch_no(){
			$this->db->select('count(id) as batch_no'); 
			$this->db->from('nf_list_batch'); 
			$this->db->where('uploaded_by',$this->session->userdata(SESSION_DATA)->uid);
			$query = $this->db->get('');
			$result = $query->result();
			if(count($result)!=0){
				return $result[0]->batch_no+1; 
			}
			else{
				return 1;
			}		
		}   
	}
?>