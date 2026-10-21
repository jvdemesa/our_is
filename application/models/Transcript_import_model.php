<?php  
	class Transcript_import_model extends CI_Model{		
		var $column_order = array('a.fullname'); //set column field database for datatable orderable
		var $column_search = array('a.fullname','a.student_no'); //set column field database for datatable searchable just firstname , lastname , address are searchable
		var $order = array('a.fullname' => 'ASC'); // default order   

		public function __construct(){
			parent::__construct();
			$this->load->database();
		}
		 
		public function _get_datatables_query(){ 	
			$this->db->select('a.fullname, a.student_no, a.college, a.degree_program'); 
			$this->db->from('transcript_general_data a'); 
			// $this->db->join('transcript_grades b','b.student_no = a.student_no'); 
			$i = 0;
			// $this->db->where('a.date_created',date("Y-m-d"));
			// if(!in_array("1", $this->session->userdata(SESSION_DATA)->access)){
			// 	$this->db->where('a.uploaded_by',$this->session->userdata(SESSION_DATA)->uid);
			// }
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

			// else if(isset($this->order)){
			// 	$order = $this->order;
			// 	foreach($order as $key=>$val){
			// 		$this->db->order_by($key, $val);
			// 	}
			// } 
		}  

		function get_datatables(){
			$this->_get_datatables_query(); 
			if($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']); 
			$query = $this->db->get();
			// echo $this->db->last_query();
			return $query->result();
		}

		function count_filtered(){
		    $this->_get_datatables_query();
		    return $this->db->count_all_results();
		}

		public function count_all(){
		    return $this->db->count_all('transcript_general_data');
		} 


		public function get_student_grades($student_no)
		{
		    $this->db->select('b.term_id, b.course_no, b.long_title, b.grade, b.removal_grade, b.credit, b.activity, b.remarks, t.description as term_desc, b.term, b.college');
		    $this->db->from('transcript_grades b');
		    $this->db->join('terms t','t.term_id = b.term_id','LEFT');
		    $this->db->where('b.student_no',$student_no);
		    $this->db->order_by('b.student_no ASC, b.term_id ASC');
		    $query = $this->db->get('');
		    return $query->result_array();
		} 

		public function get_student_details($student_no)
		{
		    $this->db->select('a.fullname, a.college, a.student_no, a.degree_program');
		    $this->db->from('transcript_general_data a');
		    $this->db->where('a.student_no',$student_no);
		    $query = $this->db->get('');
		    if ($query->num_rows() > 0) {
			    return $query->row_array();
			} else {
			    return null;
			}
		} 

		public function get_student_personal_data($student_no,$table='tr_details')
		{
		    $row = [
			    'sem_admitted'  => '',
			    'ay_admitted'   => '',
			    'admitted_as'   => '',
			    'sex'           => '',
			    'birthday'      => '',
			    'pob'           => '',
			    'fathers_name'  => '',
			    'mothers_name'  => '',
			    'degree'        => ''
			];
		    $this->db->select('a.sem_admitted, a.ay_admitted, a.admitted_as, a.sex, a.birthday, a.pob, a.fathers_name, a.mothers_name, a.degree');
		    $this->db->from($table . ' a');
			$formatted_student_no = substr($student_no, 0, 4) . '-' . substr($student_no, 4);
			$this->db->where('a.student_no', $formatted_student_no);
		    $query = $this->db->get('');
		    if ($query->num_rows() > 0) {
			    $row = $query->row_array();
			}
		    return $row;
		} 

		public function get_student_grades_chunked($limit, $offset)
		{
		    $this->db->select('a.fullname, a.college, a.student_no, a.degree_program,
		                       b.term_id, b.course_no, b.long_title, b.grade, b.removal_grade, 
		                       b.credit, b.activity, b.remarks');
		    $this->db->from('transcript_general_data a');
		    $this->db->join('transcript_grades b', 'b.student_no = a.student_no', 'LEFT');
		    $this->db->order_by('a.student_no ASC, b.term_id ASC');
		    $query = $this->db->get('', $limit, $offset);
		    return $query->result_array();
		} 


		public function count_all_grades()
		{
		    return $this->db->count_all('transcript_grades');
		}

		public function get_terms(){
			$this->db->select('term_id'); 
			$this->db->from('transcript_grades'); 
			$this->db->group_by('term_id');
			$query = $this->db->get();
			return $query->result();
		}
	}
?>
