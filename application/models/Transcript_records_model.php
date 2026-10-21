<?php  
	class Transcript_records_model extends CI_Model{		
		var $column_order = array('a.fullname'); //set column field database for datatable orderable
		var $column_search = array('a.fullname','a.filename'); //set column field database for datatable searchable just firstname , lastname , address are searchable
		var $order = array('a.fullname' => 'ASC'); // default order   

		public function __construct(){
			parent::__construct();
			$this->load->database();
		}
		 
		public function _get_datatables_query(){ 	
			$this->db->select('a.*'); 
			$this->db->from('tr_old_records a'); 
			$i = 0;
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
			$this->db->order_by('fullname', 'ASC');
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
			$this->db->from('tr_payments a'); 
			$i = 0;
			$query = $this->db->get();
			return $query->num_rows();
		}   

		public function list_requests($search,$limit){
			$this->db->select('*'); 
			$this->db->from('tr_details'); 
			if($search!=null){
				$this->db->like('trans_code',$search);
			}
			$this->db->limit($limit);
			$query = $this->db->get('');
			return $query->result();	
		}   

	}
?>