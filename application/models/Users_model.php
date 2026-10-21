<?php
	class Users_model extends CI_Model
	{

		var $column_search = array('first_name','last_name','middle_name','concat(last_name,", ",first_name," ",middle_name)','concat(first_name,", ",last_name)'); //set column field database for datatable searchable 

		public function __construct()
		{
			parent::__construct();
		}

		public function add_users($data,$table){		
			$this->db->insert($table, $data); 
			$insert_id = $this->db->insert_id();
		   return  $insert_id;		
		}

		public function _get_datatables_query(){
			if($this->input->post("access")){
            	$this->db->LIKE("access", $this->input->post("access"));
			}
	        $i=0;
	        foreach ($this->column_search as $item) // loop column 
	        {
	        	$search = explode(' ', $_POST['search']['value']);
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
		}

		public function get_users_list(){
			$this->db->select('a.*');
			$this->db->from('users a');
	        $this->_get_datatables_query();
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	    
			return $this->db->get()->result_object(); 
		}

		public function count_all(){
			$this->db->select('count(*)');
			$this->db->from('users');
	        $this->_get_datatables_query();
			return $this->db->count_all_results();
		}

		public function edit_users($data,$uid){
			$this->db->where('uid', $uid); 
			$this->db->update('users', $data); 
			//echo $this->db->last_query();exit();
		}

		public function users_details_json($uid){
		$query = $this->db->select("*")
		->from('users')
		->where('uid',$uid)
		->get();
		return $query->row();
		}
	}
?>

