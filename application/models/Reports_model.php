<?php
	class Reports_model extends CI_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function count_created($where){
			$this->db->select('count(*) as num');
			$this->db->from('first_track');
			if($where->report_level == 1){
				$this->db->where('division', $this->session->userdata('logged_in')->div_id); 
			}
			if($where->report_level == 2){
				$this->db->where('unit', $this->session->userdata('logged_in')->unit_id); 
			}
			if($where->report_level == 3){
				$this->db->where('dts_creatorid', $this->session->userdata('logged_in')->uid); 
			}
			if($where->date_created != ""){
				$date = explode(" - ", $where->date_created);
				$from = $date[0];
				$to = $date[1];
				$this->db->where('date_created >= ', $from); 
				$this->db->where('date_created <= ', $to); 
			}
			//$this->db->order_by($table,'ASC');
			$query = $this->db->get();
			//echo $this->db->last_query();

			return $query->row()->num;
		}

		public function created_list($where){
			$this->db->select('first_track.doc_details, first_track.date_created , first_track.dtsno, doc_type.doc_t as doc_type, concat(u.fname," ", u.lname) as fullname');
			$this->db->from('first_track');
			$this->db->join('doc_type', 'doc_type.id = first_track.type');
			$this->db->join(ERDBDTRS.'.users u', 'u.uid = first_track.dts_creatorid');
			if($where->report_level == 1){
				$this->db->where('first_track.division', $this->session->userdata('logged_in')->div_id); 
			}
			if($where->report_level == 2){
				$this->db->where('first_track.unit', $this->session->userdata('logged_in')->unit_id); 
			}
			if($where->report_level == 3){
				$this->db->where('first_track.dts_creatorid', $this->session->userdata('logged_in')->uid); 
			}
			if($where->date_created != ""){
				$date = explode(" - ", $where->date_created);
				$from = $date[0];
				$to = $date[1];
				$this->db->where('date_created >= ', $from); 
				$this->db->where('date_created <= ', $to); 
			}
			return $this->db->get()->result_object(); 
		}

		public function count_received($where){
			$this->db->select('count(*) as num');
			$this->db->from('tracker');
			if($where->report_level == 1){
				$this->db->where('to_division', $this->session->userdata('logged_in')->div_id); 
			}
			if($where->report_level == 2){
				$this->db->where('to_unit', $this->session->userdata('logged_in')->unit_id); 
			}
			if($where->report_level == 3){
				$this->db->where('receiver', $this->session->userdata('logged_in')->uid); 
			}
			$this->db->where('receiver !=', "");
			if($where->date_created != ""){
				$date = explode(" - ", $where->date_created);
				$from = $date[0];
				$to = $date[1];
				$this->db->where('received_time >= ', $from); 
				$this->db->where('received_time <= ', $to); 
			}
			//$this->db->order_by($table,'ASC');
			$query = $this->db->get();
			//echo $this->db->last_query();

			return $query->row()->num;
		}

		public function received_list($where){
			$this->db->select('first_track.doc_details, first_track.date_created , first_track.dtsno, doc_type.doc_t as doc_type,  concat(u.fname," ", u.lname) as fullname, tracker.received_time');
			$this->db->from('tracker');
			$this->db->join('first_track','first_track.dtsno = tracker.dtsno');
			$this->db->join('doc_type', 'doc_type.id = first_track.type');
			$this->db->join(ERDBDTRS.'.users u', 'u.uid = tracker.receiver');
			if($where->report_level == 1){
				$this->db->where('to_division', $this->session->userdata('logged_in')->div_id); 
			}
			if($where->report_level == 2){
				$this->db->where('to_unit', $this->session->userdata('logged_in')->unit_id); 
			}
			if($where->report_level == 3){
				$this->db->where('receiver', $this->session->userdata('logged_in')->uid); 
			}
			$this->db->where('receiver !=', "");
			if($where->date_created != ""){
				$date = explode(" - ", $where->date_created);
				$from = $date[0];
				$to = $date[1];
				$this->db->where('received_time >= ', $from); 
				$this->db->where('received_time <= ', $to); 
			}
			return $this->db->get()->result_object(); 
		}

		public function count_forwarded($where){
			$this->db->select('count(*) as num');
			$this->db->from('tracker');
			if($where->report_level == 1){
				$this->db->where('from_division', $this->session->userdata('logged_in')->div_id); 
			}
			if($where->report_level == 2){
				$this->db->where('from_unit', $this->session->userdata('logged_in')->unit_id); 
			}
			if($where->report_level == 3){
				$this->db->where('from_person', $this->session->userdata('logged_in')->uid); 
			}
			if($where->date_created != ""){
				$date = explode(" - ", $where->date_created);
				$from = $date[0];
				$to = $date[1];
				$this->db->where('date_forwarded >= ', $from); 
				$this->db->where('date_forwarded <= ', $to); 
			}
			//$this->db->order_by($table,'ASC');
			$query = $this->db->get();
			//echo $this->db->last_query();

			return $query->row()->num;
		}

		public function forwarded_list($where){
			$this->db->select('first_track.doc_details, first_track.date_created , first_track.dtsno, doc_type.doc_t as doc_type, concat(u.fname," ", u.lname) as fullname, tracker.date_forwarded');
			$this->db->from('tracker');
			$this->db->join('first_track','first_track.dtsno = tracker.dtsno','LEFT');
			$this->db->join('doc_type', 'doc_type.id = first_track.type','LEFT');
			$this->db->join(ERDBDTRS.'.users u', 'u.uid = tracker.from_person');
			if($where->report_level == 1){
				$this->db->where('from_division', $this->session->userdata('logged_in')->div_id); 
			}
			if($where->report_level == 2){
				$this->db->where('from_unit', $this->session->userdata('logged_in')->unit_id); 
			}
			if($where->report_level == 3){
				$this->db->where('from_person', $this->session->userdata('logged_in')->uid); 
			}
			if($where->date_created != ""){
				$date = explode(" - ", $where->date_created);
				$from = $date[0];
				$to = $date[1];
				$this->db->where('date_forwarded >= ', $from); 
				$this->db->where('date_forwarded <= ', $to); 
			}			
			return $this->db->get()->result_object(); 
		}
		public function count_acted($where){
			$this->db->select('count(*) as num');
			$this->db->from('tracker');
			if($where->report_level == 1){
				$this->db->where('to_division', $this->session->userdata('logged_in')->div_id); 
			}
			if($where->report_level == 2){
				$this->db->where('to_unit', $this->session->userdata('logged_in')->unit_id); 
			}
			if($where->report_level == 3){
				$this->db->where('actioner', $this->session->userdata('logged_in')->uid); 
			}
			$this->db->where('action_done !=', ""); 
			if($where->date_created != ""){
				$date = explode(" - ", $where->date_created);
				$from = $date[0];
				$to = $date[1];
				$this->db->where('action_time >= ', $from); 
				$this->db->where('action_time <= ', $to); 
			}
			//$this->db->order_by($table,'ASC');
			$query = $this->db->get();
			//echo $this->db->last_query();

			return $query->row()->num;
		}

		public function acted_list($where){
			$this->db->select('first_track.doc_details, first_track.date_created , first_track.dtsno, doc_type.doc_t as doc_type,concat(u.fname," ", u.lname) as fullname, tracker.action_time');
			$this->db->from('tracker');
			$this->db->join('first_track','first_track.dtsno = tracker.dtsno','LEFT');
			$this->db->join('doc_type', 'doc_type.id = first_track.type','LEFT');
			$this->db->join(ERDBDTRS.'.users u', 'u.uid = tracker.actioner');
			if($where->report_level == 1){
				$this->db->where('to_division', $this->session->userdata('logged_in')->div_id); 
			}
			if($where->report_level == 2){
				$this->db->where('to_unit', $this->session->userdata('logged_in')->unit_id); 
			}
			if($where->report_level == 3){
				$this->db->where('actioner', $this->session->userdata('logged_in')->uid); 
			}
			$this->db->where('action_done !=', ""); 
			if($where->date_created != ""){
				$date = explode(" - ", $where->date_created);
				$from = $date[0];
				$to = $date[1];
				$this->db->where('action_time >= ', $from); 
				$this->db->where('action_time <= ', $to); 
			}		
			return $this->db->get()->result_object(); 
		}

		public function ratings($where){
			$this->db->select('first_track.dtsno,DATE_FORMAT(first_track.date_created,"%M %d,%Y") as date_created, a.divunit_abv as division, b.divunit_abv as unit, rating.*, ROUND(((rating.rate1+rating.rate2+rating.rate3+rating.rate4+rating.rate5+rating.rate6)/6),2) as average, t.received_time, t.action_time, 
			@diff:=ABS( UNIX_TIMESTAMP(t.received_time) - UNIX_TIMESTAMP(t.action_time) ) as total_seconds, 
    		CAST(@days := IF(@diff/86400 >= 1, floor(@diff / 86400 ),0) AS SIGNED) as days, 
    		CAST(@hours := IF(@diff/3600 >= 1, floor((@diff:=@diff-@days*86400) / 3600),0) AS SIGNED) as hours, 
    		CAST(@minutes := IF(@diff/60 >= 1, floor((@diff:=@diff-@hours*3600) / 60),0) AS SIGNED) as minutes, 
    		CAST(@diff-@minutes*60 AS SIGNED) as seconds ');
			$this->db->from('first_track');
			$this->db->join('rating','rating.dtsno = first_track.dtsno');
			$this->db->join(ERDBDTRS.'.divunit a','a.divunit_id = first_track.division');
			$this->db->join(ERDBDTRS.'.divunit b','b.divunit_id = first_track.unit');
			$this->db->join('tracker t','t.dtsno = first_track.dtsno AND to_unit = "71"');
			if(isset($where->from)){
				$this->db->where('first_track.date_created >=', $where->from); 
				$this->db->where('first_track.date_created <=', $where->to); 
			}
			return $this->db->get()->result_object(); 
		}

		public function holidays(){
			$this->db->select('date');
			$this->db->from('holidays');
			$result = $this->db->get()->result_array();
			$arr = array_map (function($value){
			    return $value['date'];
			} , $result);
			return $arr; 
		}
	}


