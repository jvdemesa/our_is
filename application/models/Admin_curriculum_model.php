<?php
	class Admin_curriculum_model extends CI_Model
	{

		var $column_search = array('d.year','c.academic_program','c.code', 'b.college', 'b.code', 'e.code'); //set column field database for datatable searchable 

		public function __construct()
		{
			parent::__construct();
		}
		public function _get_datatables_query(){
			$this->db->join("curriculum_year d","d.id = a.cy_id");
			$this->db->join("academic_program c","c.id = d.ap_id");
			$this->db->join("colleges b","b.id = c.college_id");
			$this->db->join("subjects e","e.id = a.subject_id");
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
	        $this->db->group_by('a.cy_id');
		}

		public function get_list(){
			$this->db->select('d.id, d.year, d.ap_id, a.cy_id, GROUP_CONCAT(e.code SEPARATOR ";<BR>") as subjects,GROUP_CONCAT(e.id SEPARATOR ",") as subject_ids, b.college, b.code as college_code, c.academic_program, c.code as ap_code, d.status');
			$this->db->from('curriculum a');
	        $this->_get_datatables_query();
	        $this->db->group_by('a.cy_id');
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	    
			return $this->db->get()->result_object(); 
		}

		public function count_filtered(){
			$this->db->select('a.id');
			$this->db->from('curriculum a');
	        $this->_get_datatables_query();
			$query = $this->db->get('');
			//echo $this->db->last_query();
			$num = $query->num_rows();
			return $num;
		}

		public function count_all(){
			$this->db->select('count(*)');
			$this->db->from('curriculum_year');
			return $this->db->count_all_results();
		}
	}
?>

