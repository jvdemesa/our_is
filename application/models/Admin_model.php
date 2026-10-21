<?php
	class Admin_model extends CI_Model
	{
		var $column_order = array('a.campus_id','a.last_name','a.first_name','a.sais_id'); //set column field database for datatable orderable
		var $column_search = array('a.campus_id','a.last_name','a.first_name','a.sais_id'); //set column field database for datatable searchable just firstname , lastname , address are searchable
		var $order = array('a.campus_id' => 'ASC'); // default order   

		public function __construct()
		{
			parent::__construct();
		}

		public function list($table){
			$this->db->select('*');
			$this->db->from($table);
			//$this->db->order_by($table,'ASC');
			return $this->db->get()->result_object(); 
		}

		public function at_list($table){
			$this->db->select('*');
			$this->db->from($table);
			$this->db->where('status',0);
			//$this->db->order_by($table,'ASC');
			return $this->db->get()->result_object(); 
		}

		public function search_list($table,$where,$where_clause){
			$this->db->select('*');
			$this->db->from($table);
			$this->db->where($where_clause,$where);
			//$this->db->order_by($table,'ASC');
			return $this->db->get()->result_object(); 
		}

		public function category_list(){
			$this->db->select('*');
			$this->db->from('category');
			return $this->db->get()->result_object(); 
		}

		public function add($data,$table){
			$this->db->insert($table, $data);
			$insert_id = $this->db->insert_id();
   			return  $insert_id;
		}

		public function edit($data,$id,$table,$id_name){
			$this->db->where($id_name, $id); 
			$this->db->update($table, $data); 
			//echo $this->db->last_query();
		}

		public function delete($id,$table_id,$table){
			$this->db->where($table_id,$id);
			$this->db->delete($table); 
		}

		public function access_list($at_id){
			$this->db->select('a.*, b.link ');
			$this->db->from('access a');
			$this->db->join('link b','b.link_id = a.link_id');
			$this->db->where('a.at_id',$at_id);
			$this->db->order_by('a.a_id','DESC');
			$query = $this->db->get('');
			$result = $query->result();
			//echo $this->db->last_query();

			return $result; 
		}

		public function required_ad_to_list($ad_id){
			$this->db->select('a.*, b.id as doc_type_id, b.doc_t ');
			$this->db->from('required_ad_to a');
			$this->db->join('doc_type b','b.id = a.doc_type_id');
			$this->db->where('a.ad_id',$ad_id);
			$this->db->order_by('a.id','DESC');
			$query = $this->db->get('');
			$result = $query->result();
			//echo $this->db->last_query();

			return $result; 
		}

		public function links_list($search,$at_id){
			$this->db->select('a.*');
			$this->db->from('link a');
			if($at_id!=''){
			$this->db->join('(select at_id, link_id from access where at_id = '.$at_id.') b','b.link_id = a.link_id', 'left');
			$this->db->where('b.at_id IS NULL');
			}
			if($search!=''){
				$this->db->like('link',$search);
			}
			$this->db->limit('20');
			$query = $this->db->get('');
			$result = $query->result();
			//echo $this->db->last_query();

			return $result; 
		}

	    public function check($data,$table){
	        $this->db->select("count(*) as num");
	        $this->db->from($table);  
			foreach($data as $key=>$val){
	        	$this->db->where($key,$val);  
			}
			$query = $this->db->get();
			//echo $this->db->last_query();

			return $query->row()->num;
		}

		public function checker($where,$table){
			$this->db->select('*');
			$this->db->from($table);
			$this->db->where($table,$where);
			$query = $this->db->get('');
			//echo $this->db->last_query();
			$num = $query->num_rows();
			return $num;
		}

		public function check_col($where,$table, $col){
			$this->db->select('*');
			$this->db->from($table);
			$this->db->where($col,$where);
			$query = $this->db->get('');
			//echo $this->db->last_query();
			$num = $query->num_rows();
			return $num;
		}

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
	}
?>

