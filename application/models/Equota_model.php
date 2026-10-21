<?php
	class Equota_model extends CI_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		public function list($where){
			$this->db->select('a.id, a.ap_id, a.equota, a.year, a.sem, a.is_finalized, ap.academic_program, ap.code as ap_code, c.college, c.code as college_code, cy.year as cy_year');
			$this->db->from('equota a');
			$this->db->join('curriculum_year cy','cy.ap_id = a.ap_id');
			$this->db->join('academic_program ap','ap.id = cy.ap_id');
			$this->db->join('colleges c','c.id = ap.college_id');
			$this->db->where('cy.status','0');
			$this->db->where('a.year',$where->year);
			$this->db->where('a.sem',$where->sem);
			$this->db->order_by('ap.academic_program',"ASC");
			$query = $this->db->get('');
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}

		public function nf_list($where){
			$this->db->select('year, sem, date_uploaded');
			$this->db->from('nf_sched');
			$this->db->where('year',$where->year);
			$this->db->where('sem',$where->sem);
			$this->db->group_by('date_uploaded');
			$this->db->order_by('date_uploaded','DESC');
			$query = $this->db->get('');
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}

		public function report($where){
			$this->db->select('a.code, sum(eq.equota) as total, d.code as dept_code, col.code as col_code');
			$this->db->from('subjects a');
			$this->db->join('curriculum c','c.subject_id = a.id');
			$this->db->join('curriculum_year cy','cy.id = c.cy_id');
			$this->db->join('equota eq','eq.ap_id = cy.ap_id');
			$this->db->join('departments d','d.id = a.department_id',"LEFT");
			$this->db->join('colleges col','col.id = d.college_id',"LEFT");
			$this->db->where('eq.year',$where->year);
			$this->db->where('eq.sem',$where->sem);
			$this->db->group_by('a.id');
			$this->db->order_by('col.code, d.code, a.code','ASC');
			$query = $this->db->get('');
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}

		public function check_nf_equota($where){
		$subquery = $this->db
		    ->select('a.code, SUM(eq.equota) as equota, nf.capacity, nf.id as eq_id, a.code as subject')
		    ->from('subjects a')
		    ->join('curriculum c', 'c.subject_id = a.id',"LEFT")
		    ->join('curriculum_year cy', 'cy.id = c.cy_id',"LEFT")
		    ->join('equota eq', 'eq.ap_id = cy.ap_id',"LEFT")
		    ->join('(SELECT id, subject, SUM(capacity) as capacity, date_uploaded FROM nf_sched WHERE date_uploaded = "'.$where->date_uploaded.'" AND component != "RCT" GROUP BY subject) nf', 'nf.subject = a.code',"LEFT")
		    ->where('eq.year', $where->year)
		    ->where('eq.sem', $where->sem)
		    ->group_by('a.id')
		    ->get_compiled_select();

		$query = $this->db
		    ->select('code, equota, capacity, eq_id, subject')
		    ->from("($subquery) subquery")
		    ->having('equota > IFNULL(capacity, 0)') // Filter the results based on the condition
		    ->get();
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}

		public function dummy_students($where){
			$this->db->select('ap.code as ap_code, cy.year as year, col.code as college_code, eq.equota, cy.id as cy_id');
			$this->db->from('equota eq');
			$this->db->join('academic_program ap','ap.id = eq.ap_id');
			$this->db->join('curriculum_year cy','cy.ap_id = eq.ap_id');
			$this->db->join('colleges col','col.id = ap.college_id');
			$this->db->where('eq.year',$where->year);
			$this->db->where('eq.sem',$where->sem);
			$this->db->order_by('col.code','ASC');
			$query = $this->db->get('');
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}

		public function list_ap(){
			$this->db->select('cy.year, cy.ap_id, ap.college_id');
			$this->db->from('curriculum_year cy');
			$this->db->join('academic_program ap','ap.id = cy.ap_id');
			$this->db->where('cy.status','0');
			$query = $this->db->get('');
			if($query->num_rows() > 0){
				return $query->result();
			}else{
				return false;
			}
		}

		public function delete_regist_section($year,$sem){
		    $sql = "
		        DELETE rs, rss, rstat
		        FROM regist_sections rs
		        JOIN regist_section_schedules rss
		            ON rs.section_group_name = rss.section_group_name
		        JOIN regist_section_status rstat
		            ON rs.sem = rstat.sem
		           AND rs.year = rstat.year
		        WHERE rs.sem = ?
		          AND rs.year = ?
		    ";

		    return $this->db->query($sql, [$sem, $year]);
		}
	}
?>

