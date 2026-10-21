<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Regist extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Regist_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
 		// $this->load->model('Reclassification_model');
		$this->load->model('Admin_model', '', TRUE);
		// $this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'regist/index';
		$this->data['list'] = $this->Admin_model->list('link');
		$this->load->view('template/admin', $this->data);
  	}

    public function get_list() {
        $this->load->model('Regist_model', '', TRUE);
        $where = new stdClass();
        $where->year = $this->input->post("year");
        $where->sem = $this->input->post("sem");
        $data = $this->Regist_model->list($where);
        echo json_encode($data);
    }

	public function generate_enlistments(){
        $year = $this->input->post("year");
        $sem = $this->input->post("sem");
        $students = $this->Regist_model->students($year,$sem);
        $sections_raw = $this->Regist_model->sections($year,$sem);
        // echo $this->array_dump($sections_raw);
        $sections = [];

        foreach ($sections_raw as $row) {

            $id = (int)$row['id'];

            // ==========================================
            // CREATE SECTION ONCE
            // ==========================================

            if (!isset($sections[$id])) {

                $sections[$id] = [

                    'id' => $id,

                    'subject' => $row['subject'],

                    'section_name' => $row['section_name'],

                    'max_capacity' => (int)$row['max_capacity'],

                    'section_group_name' => $row['section_group_name'],

                    'is_gi' => (int)$row['is_gi'],

                    'days' => []
                ];
            }

            // ==========================================
            // ADD SCHEDULE ENTRY
            // ==========================================

            if (
                $row['start_time'] !== "00:00:00"
                && !empty($row['day'])
            ) {

                $sections[$id]['days'][] = [

                    'day' => $row['day'],

                    'start_time' => $row['start_time'],

                    'end_time' => $row['end_time']
                ];
            }
        }
        $conflicts = [];
        foreach ($sections as $idA => $sectionA) {
            foreach ($sections as $idB => $sectionB) {
                if ((int)$idA >= (int)$idB) continue; // avoid duplicate/self comparisons

                if ($this->isConflict($sectionA, $sectionB)) {
                     $conflicts[] = [$idA, $idB];
                }
            }
        }
        $data = [
            "students" => array_map(function($s){
                return [
                    "student_no"   => $s->student_no,
                    "name"         => $s->name,
                    "max_units"    => (int)$s->max_units,
                    "subject_code" => $s->subject_code
                ];
            }, $students),
            "sections" => $sections,
            "conflicts" => $conflicts
        ];
        // $this->array_dump($sections);

        $jsonInput = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Save JSON to a file
        $inputFile = "C:\\laragon\\www\\our_is\\input.json";
        file_put_contents($inputFile, $jsonInput);

        $python = "C:\\Users\\demes\\AppData\\Local\\Programs\\Python\\Python313\\python.exe";
        $script = "C:\\laragon\\www\\our_is\\solver.py";

        // Redirect file into Python stdin
        $cmd = "\"$python\" \"$script\" < \"$inputFile\" 2>&1";
        $output = shell_exec($cmd);
        $result = json_decode($output, true);

        $run_data = [
            'sem' => $sem,
            'year' => $year,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => 1
        ];    
        $this->db->trans_start();
            $run_id = $this->General_model->add_data("regist_schedule_runs",$run_data);

            $insert_data = [];
            $enlisted_count = 0;
            $not_enlisted_count = 0;
            foreach ($result["students"] as $student_no => $subjects) {

                foreach ($subjects as $row) {
                    if ($row['assigned']) {
                        $enlisted_count++;
                    } else {
                        $not_enlisted_count++;
                    }

                    $insert_data[] = [
                        'run_id'       => $run_id,
                        'student_no'   => $student_no,
                        'section_id'   => $row['section_id'] ?? null,
                        'subject_code' => $row['subject_code'],
                        'section_name' => $row['section_name'] ?? null,
                        'is_assigned'  => $row['assigned'],
                        'message'      => $row['message'],
                    ];
                }
            }
            $total_slots_used = 0;
            $total_slots_unused = 0;
            foreach ($result["section_statistics"] as $key => $row) {
                $total_slots_used += $row['used_slots'];
                $total_slots_unused += $row['remaining_slots'];
                $insert_section[] = [
                    'run_id'            => $run_id,
                    'section_id'        => $row['section_id'] ?? null,
                    'total_slots'       => $row['total_slots'],
                    'used_slots'        => $row['used_slots'] ?? null,
                    'remaining_slots'   => $row['remaining_slots'] ?? 0,
                    'subject_code'      => $row['subject_code'] ?? null,
                    'is_gi'             => $row['is_gi'] ?? null,
                ];
            }
            $this->General_model->insert_batch("regist_enlistments",$insert_data);
            $this->General_model->insert_batch("regist_section_utilization",$insert_section);
        $this->db->trans_complete();
        $insert_status = $this->db->trans_status();
        if ($this->db->trans_status() === FALSE) {

            echo json_encode([
                "status" => "error",
                "message" => "Transaction failed"
            ]);

            return;
        }else{
            $this->General_model->update(["enlisted" => $enlisted_count,
                "not_enlisted" => $not_enlisted_count,
                "total_slots_used" => $total_slots_used,
                "total_slots_unused" => $total_slots_unused],
                $run_id,
                "regist_schedule_runs",
                "id");

            echo json_encode([
                "status" => "success",
                "run_id" => $run_id,
                "enlisted" => $enlisted_count,
                "not_enlisted" => $not_enlisted_count,
                "total_slots_used" => $total_slots_used,
                "total_slots_unused" => $total_slots_unused,
                "enrollment_rate" => round(($enlisted_count / ($enlisted_count + $not_enlisted_count)) * 100, 2)
            ]);
        }
   	}

    private function isConflict($sectionA, $sectionB) {
        foreach ($sectionA['days'] as $dayA) {
            foreach ($sectionB['days'] as $dayB) {
                if ($dayA['day'] === $dayB['day']) {
                    $startA = strtotime($dayA['start_time']);
                    $endA   = strtotime($dayA['end_time']);
                    $startB = strtotime($dayB['start_time']);
                    $endB   = strtotime($dayB['end_time']);
                    if ($startA < $endB && $startB < $endA) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function view_enlisted_subjects(){
        $id = $this->input->post("id");
        $enlisted_subj = $this->Regist_model->view_enlisted_subjects($id);
        echo json_encode($enlisted_subj);
    }
}
