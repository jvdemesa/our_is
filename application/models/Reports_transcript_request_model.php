<?php
	class Reports_transcript_request_model extends CI_Model
	{

		var $column_search = array('a.certififcation', 'b.sub_services', 'c.description'); //set column field database for datatable searchable 

		public function __construct()
		{
			parent::__construct();
		}
		public function _get_datatables_query(){
			$this->db->join("(SELECT
			    trans_code,
			    rt_id,
			    SUM(quantity) AS quantity,pages, ready_date
			FROM tr_quantity
			WHERE quantity <> 0
			  AND pages <> 0
			GROUP BY trans_code, rt_id) b","a.trans_code = b.trans_code","LEFT");
			$this->db->join("tr_purpose c","a.trans_code = c.trans_code","LEFT");
			$this->db->join("(select * from tr_payments group by trans_code order by timestamp ASC) d","a.trans_code = d.trans_code","LEFT");
			$this->db->join("transcript_request_type e","e.id = b.rt_id","LEFT");
			$this->db->join("users u","a.assigned_to = u.uid","LEFT");
			$this->db->join("
			(
			    SELECT trans_code, MIN(datetime) AS datetime
			    FROM email_logs
			    WHERE action = 'payment_confirmation'
			      AND is_sent = '1'
			    GROUP BY trans_code
			) el",
			"el.trans_code = a.trans_code",
			"LEFT");
        	$this->db->WHERE("b.quantity !=","0");
        	$this->db->WHERE("b.pages !=","0");
        	$this->db->WHERE("b.pages !=",null);
	        $i=0;

	        if($this->input->post("request_type")){
	        	$this->db->group_start();
	        	foreach($this->input->post("request_type") as $key=>$val){
	        		if($key==0){
			        	$this->db->WHERE("b.rt_id",$val);
		        	}else{
		        		$this->db->OR_WHERE("b.rt_id",$val);
		        	}
	        	}
	        	$this->db->group_end();

	      //   	$this->db->where("
							// a.trans_code IN (
							//     SELECT trans_code
							//     FROM tr_quantity
							//     WHERE quantity <> 0
							//       AND pages <> 0
							//     GROUP BY trans_code
							//     HAVING COUNT(DISTINCT rt_id) = 1
							//        AND MAX(rt_id) = $val
							// )", null, false);
	        }

	        if($this->input->post("uid")){
	        	$this->db->WHERE("a.assigned_to",$this->input->post("uid"));
	        }
	        if($this->input->post("degree")){
	        	$this->db->LIKE("a.degree",$this->input->post("degree"));
	        }
	        if($this->input->post("status")){
	        	$this->db->group_start();
	        	foreach($this->input->post("status") as $key=>$val){
	        		if($key==0){
		        		$this->db->WHERE("a.status",$val);
		        	}else{
		        		$this->db->OR_WHERE("a.status",$val);
		        	}
	        	}
	        	$this->db->group_end();
	        }
	        if($this->input->post("request_date")){
	        	$date = explode(" - ",$this->input->post("request_date"));
	        	$date_from_timestamp = strtotime($date[0]);
				$date_from = date("Y-m-d", $date_from_timestamp);
	        	$date_to_timestamp = strtotime($date[1]);
				$date_to = date("Y-m-d", $date_to_timestamp);

	        	$this->db->WHERE('a.timestamp >=', $date_from);
				$this->db->WHERE('a.timestamp <=', $date_to);
	        }
		}

		public function get_list(){
			$this->db->select("
			    a.student_no,
			    a.degree,
			    a.trans_code,
			    a.timestamp AS request_date,
			    CONCAT(a.lname, ', ', a.fname, ' ', a.mname) AS client_name,
			    a.assessment_date_sent,
			    a.status,
			    CONCAT(u.last_name, ', ', u.first_name) AS assigned_to,

			    GROUP_CONCAT(
			        CONCAT(e.request, ' (', b.quantity, ')')
			        SEPARATOR '<hr>'
			    ) AS request_type,

			    SUM(b.quantity) AS quantity,
			    b.pages,
			    b.ready_date,
			    c.purpose,
			    d.timestamp AS payment_date,
			    el.datetime AS payment_confirmation_time
			");

			$this->db->from('tr_details a');
			$this->_get_datatables_query();
			$this->db->GROUP_BY("a.id");
			// $query = $this->db->get();
			// echo $this->db->last_query();
			// exit();
			return $this->db->get()->result_object(); 
		}

		public function count_filtered(){
			$this->db->select('COUNT(*)');

			
			$this->db->from('queues a');
	        $this->_get_datatables_query();
			return $this->db->count_all_results();
		}

		public function count_all(){
			$this->db->select('count(*)');
			$this->db->from('queues');
			return $this->db->count_all_results();
		}

		public function daily_client_count(){
			$this->db->select('DATE(a.timestamp) AS date_created, COUNT(a.id) as total_today');
			$this->db->from('tr_details a');
			$this->db->join('tr_quantity b','b.trans_code = a.trans_code','LEFT');
        	$date = explode(" - ",$this->input->post("request_date"));
        	$date_from_timestamp = strtotime($date[0]);
			$date_from = date("Y-m-d", $date_from_timestamp);
        	$date_to_timestamp = strtotime($date[1]);
			$date_to = date("Y-m-d", $date_to_timestamp);
			$this->db->where('DATE(a.timestamp) >=', $date_from);
			$this->db->where('DATE(a.timestamp) <=', $date_to);
        	$this->db->WHERE("b.quantity !=","0");
        	$this->db->WHERE("b.pages !=","0");

	        if($this->input->post("request_type")){
	        	$this->db->group_start();
	        	foreach($this->input->post("request_type") as $key=>$val){
	        		if($key==0){
			        	$this->db->WHERE("b.rt_id",$val);
		        	}else{
		        		$this->db->OR_WHERE("b.rt_id",$val);
		        	}
	        	}
	        	$this->db->group_end();
	        }

	        if($this->input->post("status")){
	        	$this->db->group_start();
	        	foreach($this->input->post("status") as $key=>$val){
	        		if($key==0){
		        		$this->db->WHERE("a.status",$val);
		        	}else{
		        		$this->db->OR_WHERE("a.status",$val);
		        	}
	        	}
	        	$this->db->group_end();
	        }
	        	        
	        if($this->input->post("uid")){
	        	$this->db->WHERE("a.assigned_to",$this->input->post("uid"));
	        }
			$this->db->group_by('DATE(a.timestamp)');
			$this->db->order_by('DATE(a.timestamp)');
			$query = $this->db->get();
			$queuesResult = $query->result_array();
			// Get the first day and last day of the current month
			$firstDayOfMonth = $date_from;
			$lastDayOfMonth = $date_to;

			// Generate an array of all days 
			$daysOfMonth = [];
			$currentDate = $firstDayOfMonth;
			while ($currentDate <= $lastDayOfMonth) {
			    $daysOfMonth[] = $currentDate;
			    $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
			}

			// Create an associative array with day_of_month as keys
			$reqPerDay = [];
			foreach ($queuesResult as $row) {
			    $reqPerDay[$row['date_created']] = $row;
			}

			// initialize data array
			$data = [];
			foreach ($daysOfMonth as $key=>$day) {

				//add date to the array
				$data[$key]["date"] = date(("M d, Y"),strtotime($day));
				if(isset($reqPerDay[$day])){
					$data[$key]["total_today"] = $reqPerDay[$day]["total_today"];
				}else{
					$data[$key]["total_today"] = 0;
				}
			}
			return $data; 
		}
	}
?>

