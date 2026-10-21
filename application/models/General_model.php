<?php
	class General_model extends CI_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		public function get_one_row($table,$id_name,$id){
			$this->db->select('*');
			$this->db->from($table);
			if($id!=''){
			$this->db->where($id_name,$id);
			}
			$query = $this->db->get('');
			$result = $query->result();
			if(count($result)!=0){
				return $result[0]; 
			}
			else{
				return false;
			}		
		}

		public function get_user_details($id){
			$this->db->select('a.*, p.position, os.official_station as official_station');
			$this->db->from(ERDBDTRS.".users a");
			$this->db->join(ERDBDTRS.".positions p","p.posidkey = a.position");
			$this->db->join(ERDBDTRS.".official_station os","os.id = a.official_station");
			$this->db->where("uid",$id);
			$query = $this->db->get('');
			$result = $query->result();
			if(count($result)!=0){
				return $result[0]; 
			}
			else{
				return false;
			}		
		}
		public function list($table){
			$this->db->select('*');
			$this->db->from($table);
			$query = $this->db->get('');
			$result = $query->result();
			return $result; 
		}

		public function get_cs_unit_head($dtsno){
			$this->db->select('*');
			$this->db->from("concerned_signatories");
			$this->db->where("uid != ","0");
			$this->db->where("signatory_type","6");
			$this->db->where("dtsno",$dtsno);
			$query = $this->db->get('');
			$result = $query->result();
			if(count($result)!=0){
				return $result[0]; 
			}
			else{
				return false;
			}		
		}

		public function list_dynamic($table,$where,$order_by=null){
			$this->db->select('*');
			$this->db->from($table);
			foreach($where as $key=>$val){
				$this->db->where($key,$val);
			}
			if($order_by!= null){
				$this->db->order_by($order_by,"ASC");
			}
			$query = $this->db->get('');
			// echo $this->db->last_query();
			// exit();
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}

		public function count_where($table,$where){
			$this->db->select('*');
			$this->db->from($table);
			foreach($where as $key=>$val){
				$this->db->where($key,$val);
			}
			return $this->db->count_all_results();
		}

		public function delete($table,$where){
			if($where){
				foreach($where as $key=>$val){
					$this->db->where($key,$val);
				}
			}
			$this->db->delete($table);
		}

		public function finished_docs(){
			$this->db->select('a.dtsno,a.first_trackid, b.doc_t as doc_type');
			$this->db->from('first_track a');
			$this->db->join('doc_type b','b.id = a.type');
			$this->db->where('a.archive','1');
			$query = $this->db->get('');
			//echo $this->db->last_query();
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}

		public function get_users($doc_type_id){
			// $this->db->select('uid, userid, idnum, fname, lname, mname, item_code_id, position, division, unit, employmentStatus, bday, address, email, username, userdp, is_disabled, privilege, access, home_page, TO_BASE64(signature) as signature, img_type');
			$this->db->select('uid, userid, idnum, fname, lname, mname, item_code_id, position, division, unit, employmentStatus, bday, address, email, username, userdp, is_disabled, privilege, access, home_page');
			$this->db->from(ERDBDTRS.".users");
			//if to, voucher, liquiadation of ca select only division employee
			if($this->session->userdata('logged_in')->uid != 1){
				$this->db->where("division",$this->session->userdata('logged_in')->div_id);
			$this->db->group_start();
			$this->db->where("is_included",1);
			$this->db->where("is_disabled",0);
			$this->db->or_where("uid",$this->session->userdata('logged_in')->uid);
			$this->db->group_end();
			}
			$query = $this->db->get('');
			$result = $query->result();
			return $result; 
		}

		public function get_users_limit($search,$doc_type_id){
			$this->db->select('uid, userid, idnum, concat(lname,", ",fname," ",mname) as fullname, fname, lname, mname, item_code_id, position, division, unit, employmentStatus, bday, address, email, username, userdp, status, privilege, access, home_page');
			$this->db->from(ERDBDTRS.".users");
			//if to, voucher, liquiadation of ca select only division employee
			if(in_array($doc_type_id,[5,2,107,115])){
				$this->db->where("division",$this->session->userdata('logged_in')->div_id);
			}
			if($search!=''){
				$this->db->like('concat(lname," ",fname," ",mname)',$search);
				$this->db->like('concat(lname,", ",fname," ",mname)',$search);
				$this->db->like('concat(fname," ",lname)',$search);
			}
			$this->db->limit(20);
			$query = $this->db->get('');
			// echo $this->db->last_query();
			$result = $query->result();
			return $result; 
		}

		public function where_list($table,$fieldname,$where){
			$this->db->select('*');
			$this->db->from($table);
			$this->db->where($fieldname,$where);
			$query = $this->db->get('');
			return ($query->num_rows())?$query->result():false;
		}

		public function division_list(){
			$this->db->select('*');
			$this->db->from(ERDBDTRS.'.divunit');
			$this->db->where('unit_parent', 0); 
			return $this->db->get()->result_object(); 
		}
		public function unit_list($divunit_id){
			$this->db->select('*');
			$this->db->from(ERDBDTRS.'.divunit');
			$this->db->where('unit_parent', $divunit_id); 
			$this->db->where('status', "0"); 
			return $this->db->get()->result_object(); 
		}

		public function link_tree(){
			$this->db->select('*');
			$this->db->from('link');
			$this->db->where('status',0);
			$this->db->order_by('arrangement','ASC');
			return $this->db->get()->result_object(); 
		}

		public function get_active_link($link){
			$this->db->select('link_id,parent_id');
			$this->db->from('link');
			$this->db->where('status',0);
			$this->db->where('link',$link);
			$this->db->order_by('arrangement','ASC');
			$result =$this->db->get()->row();
			$id = array();
			if($result){
				$id[] = $result->parent_id;
				$id[] = $result->link_id;
			}
			return $id;
		}
		
		public function fast_receive($dtsno){
			$data = new stdClass();
			$data->receiver = $this->session->userdata('logged_in')->userid;
			$data->received_time = date("Y-m-d H:i:s");

			$this->db->where('dtsno', $dtsno); 
			$this->db->where("receiver","");
			$this->db->where('to_unit',$this->session->userdata('logged_in')->unit_id); 
			$this->db->update('tracker', $data); 
			//echo $this->db->last_query();

			return ($this->db->affected_rows() > 0) ? TRUE : FALSE; 
		}
				
		public function add_data($table,$data){
			$this->db->insert($table, $data);
			// echo $this->db->last_query();
			return $this->db->insert_id();
		}
		
		public function insert_batch($table,$data){
		    if (empty($data)) return 0; // prevent errors on empty array

		    $this->db->insert_batch($table, $data);
		    return $this->db->affected_rows(); // returns number of rows inserted
		}

		public function update($data,$id,$table,$id_name){
			$this->db->where($id_name, $id); 
			$this->db->update($table, $data); 
			return ($this->db->affected_rows() > 0) ? TRUE : FALSE; 
		}

		public function update_where($data,$where,$table){
			foreach($where as $key=>$val){
				$this->db->where($key,$val);
			}
			$this->db->update($table, $data); 
			// echo $this->db->last_query();
			return ($this->db->affected_rows() > 0) ? TRUE : FALSE; 
		}

		public function get_signatories(){
			$this->db->select('a.designation, a.designation as designation_id, a.id, a.uid, b.fname, b.lname, b.mname, a.unit as unit_id,  a.division as div_id, d.designation as designation, d.id as designation_id');
			$this->db->from("signatories a");
			$this->db->join(ERDBDTRS.".users b","b.uid = a.uid");
			$this->db->join("designation d","d.id = a.designation");
			$this->db->where("is_active","1");
			$query = $this->db->get('');
			$result = $query->result();
			return $result; 
		}

		public function get_remarks_details($id){
			$this->db->select('a.*');
			$this->db->from("tev_checklist_remarks a");
			$this->db->where("a.id",$id);
			$query = $this->db->get('');
			$result = $query->result();
			return $result; 
		}
		public function get_cs($dtsno){
			$this->db->select('a.*,b.label');
			$this->db->from('concerned_signatories a');
			$this->db->join('ad_signatory b',"b.id = a.ads_id");
			$this->db->where('a.dtsno',$dtsno);
			$query = $this->db->get('');
			// echo $this->db->last_query();
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}
		public function quick_search($dtsno){
			$this->db->select('a.*, b.*, c.doc_t as doc_type, cc.doc_t as sub_type, d.divunit_abv as from_div, dd.divunit_abv as from_unit, e.divunit_abv as to_div, ee.divunit_abv as to_unit');
			$this->db->from("first_track a");
			$this->db->join("tracker b","b.dtsno = a.dtsno");
			$this->db->join("doc_type c","c.id = a.type");
			$this->db->join("doc_type cc","cc.id = a.sub_type", "LEFT");
			$this->db->join(ERDBDTRS.".divunit d","d.divunit_id = b.from_division");
			$this->db->join(ERDBDTRS.".divunit dd","dd.divunit_id = b.from_unit");
			$this->db->join(ERDBDTRS.".divunit e","e.divunit_id = b.to_division");
			$this->db->join(ERDBDTRS.".divunit ee","ee.divunit_id = b.to_unit");
			$this->db->where("a.dtsno",$dtsno);
			$this->db->where('b.tracker_id = (select MAX(tracker_id) from tracker where dtsno = a.dtsno)',"",false);
			$query = $this->db->get('');
			$result = $query->result();
			if(count($result)!=0){
				return $result[0]; 
			}
			else{
				return false;
			}		
		}

		public function list_approved_dv($dtsno,$funding_source_id = "01"){
			$this->db->select('a.*, c.approved_number, concat(u.fname," ", u.lname) as fullname, e.fund_cluster');
			$this->db->from("first_track a");
			if($dtsno == null){
				$this->db->join("(select * from linked_documents where doc_type_id = '117' AND is_deleted = 0) b","a.dtsno = b.linked_dtsno","left");
			}else{
				$this->db->join("(select * from linked_documents where doc_type_id = '117' AND linked_to != '{$dtsno}') b","a.dtsno = b.linked_dtsno","left");
			}
			$this->db->join("approved_document_number c","c.dtsno = a.dtsno");
			$this->db->join("(select * from doc_detail where fieldname_id = 9 ) d","d.dtsno = a.dtsno");
			$this->db->join(ERDBDTRS.".users u","u.uid = d.value");
			$this->db->join("obr_main e","e.dtsno = a.dtsno");
			$this->db->where("a.type",2);
			$this->db->where("a.is_approved",1);
			$this->db->where("b.linked_dtsno is NULL","",false);
			$this->db->where("e.funding_source_id",$funding_source_id);
			$this->db->order_by("fund_cluster");
			$this->db->group_by("a.dtsno");
			$query = $this->db->get('');
			$result = $query->result();
			return $result; 
		}

		public function get_not_yet_signed($dtsno){
			$this->db->select('*');
			$this->db->from('concerned_signatories');
			$this->db->where('dtsno',$dtsno);
			$this->db->where('is_signed',0);
			$this->db->where('uid !=',0);
			$this->db->where('signatory_type !=',8);//quick fix only for unsigned signatory for OBR
			$query = $this->db->get('');
			return ($query->num_rows() > 0) ? TRUE : FALSE; 
		}

		public function get_not_yet_signed_ab_aa($dtsno){
			$this->db->select('*');
			$this->db->from('concerned_signatories');
			$this->db->where('dtsno',$dtsno);
			$this->db->where('is_signed',0);
			$this->db->where_in('signatory_type',[7,5]);
			$query = $this->db->get('');
			return ($query->num_rows() > 0) ? TRUE : FALSE; 
		}

		public function get_copy_count($dtsno){
			$this->db->select('dtsno');
			$this->db->from('tracker');
			$this->db->where('dtsno',$dtsno);
			$this->db->group_by('copy_no');
			$query = $this->db->get('');
			return ($query->num_rows() > 0) ? $query->num_rows() : FALSE; 
		}

		public function get_approved_document_number(){
			$this->db->select('dtsno');
			$this->db->from('approved_document_number');
			$this->db->where('year',date("Y"));
			$this->db->group_start();
				$this->db->where('doc_type_id',"2");
				$this->db->or_where('doc_type_id',"130");
			$this->db->group_end();
			$query = $this->db->get('');
			$result = $query->result();
			return $result; 
		}
		public function get_last_track($dtsno){
			$this->db->select('*');
			$this->db->from('tracker');
			$this->db->where('dtsno',$dtsno);
			$this->db->order_by('tracker_id',"DESC");
			$this->db->LIMIT(1);
			$query = $this->db->get('');
			// echo $this->db->last_query();
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}

		public function for_receive($table,$where){
			$this->db->select('*');
			$this->db->from("tracker");
			$this->db->where("dtsno",$where->dtsno);
			$this->db->where("to_unit",$where->to_unit);
			$this->db->where("receiver","");
			$query = $this->db->get('');
			// echo $this->db->last_query();
			// exit();
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}
	}
?>

