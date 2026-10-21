<?php
	class User_login_model extends CI_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function get_user_data(){
			$this->db->select('is_restricted,window_id');
			$this->db->from("users");
			$this->db->where("uid",$this->session->userdata(SESSION_DATA)->uid);
			return $this->db->get()->row(); 
		}

		public function verify($username){
			$this->db->select('a.*,b.link as home_page');
			$this->db->from('users a');
			$this->db->join('link b', 'b.link_id = a.home_page','LEFT');
			$this->db->where('username', $username);
			$this->db->limit(1);

			$query = $this->db->get();
						
			if($query->num_rows() == 1)
			{
				$result = $query->result();
				return $result[0];
			}
			else
			{
				return false;
			}
		}

			
		public function verify_via_email($email){
			$this->db->select('a.*,b.link as home_page');
			$this->db->from('users a');
			$this->db->join('link b', 'b.link_id = a.home_page','LEFT');
			$this->db->where('a.email', $email);
			$this->db->limit(1);

			$query = $this->db->get();
						
			if($query->num_rows() == 1)
			{
				$result = $query->result();
				return $result[0];
			}
			else
			{
				return 0;
			}
		}		

		public function validate_email($email){
			$this->db->select('uid');
			$this->db->from('users');
			$this->db->where('email', $email);
			$this->db->limit(1);

			$query = $this->db->get();
						
			if($query->num_rows() == 1)
			{
				$result = $query->result();
				return $result[0]->uid;
			}
			else
			{
				return 0;
			}
		}		

		public function get_links($at_id){
			$this->db->select('b.label, b.link');
			$this->db->from('access a');
			$this->db->join('link b','b.link_id = a.link_id');
			foreach($at_id as $val){
				$this->db->or_where('a.at_id',$val);
			}
			$query = $this->db->get();
			//echo $this->db->last_query();
			
			$result = $query->result();
			return $result;
		}			
	}
?>

