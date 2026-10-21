<?php
	class Transcript_tcg_model extends CI_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		var $column_order = array('a.campus_id','a.last_name','a.first_name','a.sais_id'); //set column field database for datatable orderable
		var $column_search = array('a.campus_id','a.last_name','a.first_name','a.sais_id'); //set column field database for datatable searchable just firstname , lastname , address are searchable
		var $order = array('a.campus_id' => 'ASC'); // default order   

		public function _get_datatables_query(){ 	
			$this->db->select('a.id, a.campus_id, a.last_name, a.first_name, a.middle_name,a.term, a.sais_id'); 
			$this->db->from('students a'); 
			$i = 0;
			// $this->db->where('a.date_created',date("Y-m-d"));
			$this->db->group_by('a.campus_id');
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
			$this->db->from('students a'); 
			$i = 0;
			// $this->db->where('a.date_created',date("Y-m-d"));
			$query = $this->db->get();
			return $query->num_rows();
		} 

		public function get_students(){
			$this->db->select("*, concat(last_name,' ',first_name,' ', middle_name) as fullname");
			$this->db->from("students");
			$this->db->WHERE("id <","1010");
			$query = $this->db->get();
			return $query->result();
		}
	}
?>

