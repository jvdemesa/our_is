<?php
	class Reports_transcript_model extends CI_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function get_income_list(){
			$this->db->select('a.trans_code, a.timestamp as request_date, CONCAT(a.fname," ",a.lname) as requestor, c.request, b.quantity, b.pages, c.price, DATE_FORMAT(b.release_date, "%M %d&#44; %Y %h:%i") as release_date, b.released_to, b.released_by ');
			$this->db->from('tr_details a');
			$this->db->join('tr_quantity b', 'b.trans_code = a.trans_code');
			$this->db->join('transcript_request_type c', 'c.id = b.rt_id');
			$this->db->where('b.quantity !=', "0"); 
			$this->db->where('b.pages !=', "0"); 
			$this->db->where('a.status >=', "4"); 

			if($this->input->post("request_id")!=""){
				$this->db->where('b.rt_id', $this->input->post("request_id")); 
			}
			if($this->input->post("request_date")!=""){
				$request_date = explode(" - ",$this->input->post("request_date"));
				$this->db->where('a.timestamp >=', $request_date[0]." 00:00:00"); 
				$this->db->where('a.timestamp <=', $request_date[1]." 23:59:59"); 
			}
			$query = $this->db->get();
			// echo $this->db->last_query();
			return $query->result();
		}
	}


