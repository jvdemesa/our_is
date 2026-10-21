<?php  
	class Regist_model extends CI_Model{		
		var $column_order = array('a.rownum'); //set column field database for datatable orderable
		var $column_search = array(
			"a.student_number",
		    "a.fname",
		    "a.lname",
		    "a.mname",
		    "concat(lname,' ', fname)",
		    "concat(lname,', ', fname)",
		    );

		var $order = array('a.rownum' => 'DESC'); // default order   

		public function __construct(){
			parent::__construct();
			$this->load->database();
		}
		 
		public function _get_datatables_query(){ 	
			$this->db->select('a.*'); 
			$this->db->from('id_requests a'); 
			// $this->db->join('tr_details b','b.trans_code = a.trans_code','LEFT'); 
			$i = 0;
			if($this->input->post("status")!=""){
				$this->db->where('a.status',$this->input->post("status"));
			}
			if($this->input->post("request_type")!=""){
				$this->db->where('a.request_type',$this->input->post("request_type"));
			}
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
			$this->db->order_by('date_submitted', 'ASC');
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
			$this->db->from('id_requests a'); 
			$i = 0;
			$query = $this->db->get();
			return $query->num_rows();
		}   

		public function students($year,$sem){
			$this->db->select('a.student_no, a.name, a.max_units, c.code as subject_code'); 
			$this->db->from('regist_students a'); 
			$this->db->join('curriculum b','b.cy_id = a.cy_id','LEFT'); 
			$this->db->join('subjects c','c.id = b.subject_id','LEFT'); 
			$this->db->where('year',$year); 
			$this->db->where('sem',$sem); 
			$query = $this->db->get('');
			return $query->result();	
		}

		public function sections($year,$sem){
			$this->db->select('a.*,b.*, a.id as id'); 
			$this->db->from('regist_sections a'); 
			$this->db->join('regist_section_schedules b','b.section_group_name = a.section_group_name');
			$this->db->where('a.is_active',1); 
			$this->db->where('b.is_active',1);
			$this->db->where('year',$year); 
			$this->db->where('sem',$sem); 
			$query = $this->db->get('');
			return $query->result_array();	
		}

		public function list($where){
			$this->db->select('*'); 
			$this->db->from('regist_schedule_runs'); 
			$this->db->where('year',$where->year); 
			$this->db->where('sem',$where->sem); 
			$query = $this->db->get('');
			return $query->result_array();	
		}

		public function view_enlisted_subjects($id){
			$this->db->select('a.student_no, b.name,a.subject_code, a.section_name, d.schedule'); 
			$this->db->from('regist_enlistments a'); 
			$this->db->join('regist_students b','b.student_no = a.student_no');
			$this->db->join('regist_sections c','c.id = a.section_id',"left");
			$this->db->join('(select group_concat(day,"(",start_time,"-",end_time,")" SEPARATOR "<br>") as schedule, section_group_name from regist_section_schedules group by section_group_name) d','d.section_group_name = c.section_group_name',"left");
			$this->db->where('a.is_assigned',1); 
			$this->db->where('a.run_id',$id); 
			$this->db->order_by('student_no');  
			// $this->db->limit('10'); 
			$query = $this->db->get('');
			return $query->result_array();	
		}
	}
?>