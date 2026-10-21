<?php  
	class Transcript_clearance_model extends CI_Model{		
		var $column_order = array('a.fullname'); //set column field database for datatable orderable
		var $column_search = array('a.fullname','a.student_no'); //set column field database for datatable searchable just firstname , lastname , address are searchable
		var $order = array('a.fullname' => 'ASC'); // default order   

		public function __construct(){
			parent::__construct();
			$this->load->database();
		}
		 
		public function _get_datatables_query(){ 	
			$this->db->select('a.*, DATE_FORMAT(a.date_approved, "%M %d&#44; %Y") as date_approved'); 
			$this->db->from('tr_clearance a'); 
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
			$this->db->from('tr_clearance a'); 
			$i = 0;
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

		public function last_date_approved(){
			$this->db->select('date_approved'); 
			$this->db->from('tr_clearance'); 
			$this->db->order_by('date_approved',"DESC"); 
			$query = $this->db->get('');
			$result = $query->result();
			if(count($result)!=0){
				return $result[0]->date_approved; 
			}
			else{
				return false;
			}		
		} 

		public function insert_batch_if_not_exists($data, $table, $unique_column) {
		    // Step 1: Extract unique column values from the data to be inserted
		    $values_to_insert = array_column($data, $unique_column);

		    // Step 2: Fetch existing values from the database
		    $this->db->select($unique_column);
		    // $this->db->where_in($unique_column, $values_to_insert);
		    $existing_values_query = $this->db->get($table);
		    $existing_records = array_column($existing_values_query->result_array(), $unique_column);
			 // Convert existing records to an associative array for faster lookup
 			$existing_records = array_flip($existing_records);
		    // Step 3: Filter out data that already exists in the database
		    $filtered_data = array_filter($data, function($row) use ($existing_records, $unique_column) {
		        return !isset($existing_records[$row[$unique_column]]);
		    });
		    $inserted_count = 0;
		    // Step 4: Insert the filtered data using insert_batch
		    if (!empty($filtered_data)) {
		        $this->db->insert_batch($table, $filtered_data);
		        $inserted_count = count($filtered_data);
		    }
		    return $inserted_count;
		}
	}
?>