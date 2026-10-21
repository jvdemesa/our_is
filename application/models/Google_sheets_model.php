<?php
    class Google_sheets_model extends CI_Model
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function get_last_inserted(){
            $this->db->select('rownum, assigned_to');
            $this->db->from('tr_details');
            $this->db->limit(1);
            $this->db->ORDER_BY("rownum","DESC");
            $query = $this->db->get('');
            $result = $query->result();
            if(count($result)!=0){
                return $result[0]; 
            }
            else{
                return false;
            }       
        }

        public function get_last_payment(){
            $this->db->select('rownum');
            $this->db->from('tr_payments');
            $this->db->limit(1);
            $this->db->ORDER_BY("rownum","DESC");
            $query = $this->db->get('');
            $result = $query->result();
            if(count($result)!=0){
                return $result[0]; 
            }
            else{
                return false;
            }       
        }

        public function count_filtered(){
            $this->db->select('count(*)');
            $this->db->from('colleges');
            $this->_get_datatables_query();
            return $this->db->count_all_results();
        }

        public function count_all(){
            $this->db->select('count(*)');
            $this->db->from('colleges');
            return $this->db->count_all_results();
        }

        public function transcript_users(){
            $this->db->reset_query();
            $this->db->select('uid');
            $this->db->from('users');
            $this->db->where('on_leave',0);
            $this->db->where('section_id',3);
            $this->db->where('is_disabled',0);
            $this->db->ORDER_BY('uid',"ASC");
            $query = $this->db->get();

            // Initialize an array to store results with indexes
            $results_with_indexes = [];
            $index = 0;

            // Iterate over the query result and add indexes
            foreach ($query->result() as $row) {
                $results_with_indexes[$index] = $row->uid;
                $index++;
            }

            // Return the results with indexes
            return  $results_with_indexes;
        }

        public function all_transcript_users(){
            $this->db->select('uid');
            $this->db->from('users');
            // $this->db->where('on_leave',0);
            $this->db->where('section_id',3);
            $this->db->where('is_disabled',0);
            $this->db->ORDER_BY('uid',"ASC");
            $query = $this->db->get();

            // Initialize an array to store results with indexes
            $results_with_indexes = [];
            $index = 0;

            // Iterate over the query result and add indexes
            foreach ($query->result() as $row) {
                $results_with_indexes[$index] = $row->uid;
                $index++;
            }

            // Return the results with indexes
            return  $results_with_indexes;
        }
    }
?>

