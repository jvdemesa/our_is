<?php
	class Pdf_generator_model extends CI_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function get_obr_main($dtsno){
			$this->db->select('a.*, b.funding_source as funding_source, b.id as funding_source_id, b.code, b.fund_cluster');
			$this->db->from('obr_main a');
			$this->db->join('funding_source b','b.id = a.funding_source');
			$this->db->where('dtsno',$dtsno);

			//$this->db->where('notif','1');
			//$this->db->order_by($table,'ASC');
			return $this->db->get()->row(); 
		}

		public function get_jo_mis($dtsno){
			$this->db->select('a.*,concat(b.fname," ",b.lname) as service_by, concat(c.fname," ",c.lname) as noted_by, b.signature as service_by_sig, b.img_type as service_by_img_type, c.signature as noted_by_sig, c.img_type as noted_by_img_type');
			$this->db->from('jo_mis a');
			$this->db->join(ERDBDTRS.'.users b','b.uid = a.service_by',"LEFT");
			$this->db->join(ERDBDTRS.'.users c','c.uid = a.noted_by',"LEFT");
			$this->db->where('dtsno',$dtsno);

			//$this->db->where('notif','1');
			//$this->db->order_by($table,'ASC');
			// 			$query = $this->db->get();
			// echo $this->db->last_query();
			// exit();
			return $this->db->get()->row(); 
		}

	    public function first_track_details($dtsno){
	        $this->db->select("dt.doc_t as doc_type, dt2.doc_t as sub_type,ft.doc_details, concat(u.fname,' ',u.lname) as fullname");
	        $this->db->from('first_track ft');  
	        $this->db->join('doc_type dt','dt.id = ft.type',"left");  
	        $this->db->join('doc_type dt2','dt2.id = ft.sub_type',"left");  
	        $this->db->join(ERDBDTRS.'.users u','u.uid = ft.dts_creatorid',"left");  
			$this->db->where('ft.dtsno',$dtsno);
			// $query = $this->db->get();
			//echo $this->db->last_query();
			return $this->db->get()->result_object(); 
		}


	}
?>

