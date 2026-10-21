<?php

Class Login_Database extends CI_Model {
		public function __construct()
		{
			parent::__construct();
			$this->load->database();
        	$this->db2 = $this->load->database('default', TRUE);    

		}

		// Read data using username and password
		public function get_usercredential($receivedata){
			
			$this->db2->select('a.*,b.position as bposition,c.divunit_abv as udiv, d.divunit_abv as uunit, ');
			$this->db2->from('users a');
			$this->db2->join('positions b','b.posidkey = a.position');
			$this->db2->join(ERDBDTRS.'.divunit c','c.divunit_id = a.division');
			$this->db2->join(ERDBDTRS.'.divunit d','d.divunit_id = a.unit');
			$this->db2->where('uid', $receivedata);
			$query = $this->db2->get('');
				
			if($query->num_rows() > 0)
			{
				$result = $query->result();
				return $result[0];
			}
			else
			{
				return false;
			}
		}

		public function login($data) {
		$condition = "username =" . "'" . $data['username'] . "' AND " . "password =" . "'" . $data['password'] . "'";
		$this->db2->select('*');
		$this->db2->from('users');
		$this->db2->where($condition);
		$this->db2->limit(1);
		$query = $this->db2->get();
		
		if ($query->num_rows() == 1) {
		return true;
		} else {
		return false;
		}
		}
		
		// Read data from database to show data in admin page
		public function read_user_information($username) {
		
		$condition = "username =" . "'" . $username . "'";
		$this->db2->select('*');
		$this->db2->from('users');
		$this->db2->where($condition);
		$this->db2->limit(1);
		$query = $this->db2->get();
		
		if ($query->num_rows() == 1) {
		return $query->result();
		} else {
		return false;
		}
		}
		
}

?>