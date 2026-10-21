<?php
	class Admin_subjects_model extends CI_Model
	{

		var $column_search = array('a.subject','a.code'); //set column field database for datatable searchable 

		public function __construct()
		{
			parent::__construct();
		}
		public function _get_datatables_query(){
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

		public function get_list(){
			$this->db->select('a.*');
			$this->db->from('subjects a');
	        $this->_get_datatables_query();
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	    
			return $this->db->get()->result_object(); 
		}

		public function count_filtered(){
			$this->db->select('count(a.*)');
			$this->db->from('subjects a');
	        $this->_get_datatables_query();
			return $this->db->count_all_results();
		}

		public function count_all(){
			$this->db->select('count(*)');
			$this->db->from('subjects');
			return $this->db->count_all_results();
		}
	}
?>

