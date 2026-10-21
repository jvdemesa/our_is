<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '1024M');
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Transcript_imported_grades extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Transcript_import_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
		$this->load->model('Admin_model', '', TRUE);
		$this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'transcript_imported_grades/index';
		$this->data['list'] = $this->Admin_model->list('link');
        $this->data['term_list'] = $this->Transcript_import_model->get_terms();
		$this->load->view('template/admin', $this->data);
  	}

    public function upload_amis_grades()
    {
        $datetime = date("Y-m-d H:i:s");
        $upload_dir = "assets/uploads/amis_grades/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config['upload_path'] = $upload_dir;
        $config['allowed_types'] = 'xlsx|xls|csv';
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());
            print_r($error);
            return;
        }

        $data = $this->upload->data();
        $file_path = $upload_dir . $data['file_name'];
        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            $sheetData = [];
            $fh = fopen($file_path, 'r');
            $i = 0;
            while (($r = fgetcsv($fh)) !== false) {
                if ($r === [null]) continue; // skip blank lines
                $i++;
                $sheetData[$i] = [
                    'A'=>$r[0] ?? '', 'B'=>$r[1] ?? '', 'C'=>$r[2] ?? '', 'D'=>$r[3] ?? '',
                    'E'=>$r[4] ?? '', 'F'=>$r[5] ?? '', 'G'=>$r[6] ?? '', 'H'=>$r[7] ?? '',
                    'I'=>$r[8] ?? '', 'J'=>$r[9] ?? '', 'K'=>$r[10] ?? '', 'L'=>$r[11] ?? '',
                ];
            }
            fclose($fh);
        } else {
            $reader = IOFactory::createReaderForFile($file_path);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file_path);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, false, false, true);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $reader);
        }


        // Skip header (applies to both CSV and xlsx)
        $sheetData = array_slice($sheetData, 1);
        // Get existing students
        $existing_students = $this->db
            ->select('student_no')
            ->get('transcript_general_data')
            ->result_array();
        $existing_student_nos = array_flip(array_column($existing_students, 'student_no'));

        // Prepare structures
        $students_to_add = [];
        $grouped_data = [];
        $non_acads = ['HK 11', 'HK 12', 'NSTP 1', 'NSTP 2'];
        foreach ($sheetData as $row) {
            $student_no     = trim($row['A']);
            $college        = trim($row['B']);
            $fullname       = trim($row['C']);
            $degree_program = trim($row['D']);
            $term_id        = trim($row['E']);
            $course_no      = trim($row['F']);
            $long_title     = trim($row['G']);
            $credit         = trim($row['H']);
            $activity       = trim($row['I']);
            $grade          = $this->format_grade(trim($row['J']));
            $remarks        = trim($row['K']);
            $removal_grade  = $this->format_grade(trim($row['L']));
            $term        = convert_term_text(trim($row['E']));

            foreach ($non_acads as $code) { 
                if (stripos($course_no, $code) !== false) { 
                    $credit = "({$credit})"; 
                    break; 
                } 
            }
            // Add to new student list if not existing
            if (!isset($existing_student_nos[$student_no]) && !isset($students_to_add[$student_no])) {
                $students_to_add[$student_no] = [
                    'student_no' => $student_no,
                    'fullname' => $fullname,
                    'college' => $college,
                    'degree_program' => $degree_program
                ];
            }

            // Group by student_no + term_id
            $key = $student_no . '|' . $term_id;
            if (!isset($grouped_data[$key])) {
                $grouped_data[$key] = [
                    'student_no' => $student_no,
                    'term_id' => $term_id,
                    'subjects' => []
                ];
            }

            $grouped_data[$key]['subjects'][] = [
                'course_no' => $course_no,
                'long_title' => $long_title,
                'credit' => $credit,
                'activity' => $activity,
                'grade' => $grade,
                'remarks' => $remarks,
                'removal_grade' => $removal_grade,
                'college' => $college,
                'term' => $term
            ];
            // echo $college."<br>";
        }
        // exit();

        

        // Insert new students in chunks
        if (!empty($students_to_add)) {
            $chunks = array_chunk(array_values($students_to_add), 300);
            foreach ($chunks as $batch) {
                $this->General_model->insert_batch('transcript_general_data', $batch);
            }
        }

        // Process grouped data per student+term
        $insert_grades = [];
        $updated_grades_count =0 ;
        $this->db->trans_start(); // Start DB transaction
        foreach ($grouped_data as $group) {
            $sn = $group['student_no'];
            $tid = $group['term_id'];

            $course_checker = [];

            foreach ($group['subjects'] as $subj) {

                $course_no = trim($subj['course_no']);
                $merged = false;

                //&$existing points directly to the real element inside $course_checker
                foreach ($course_checker as &$existing) {

                    if ($existing['course_no'] === $course_no) {

                        if ($existing['grade'] == 'Inc.' && $subj['grade'] === 'Inc.') {

                            $rg1 = trim($existing['removal_grade']);
                            $rg2 = trim($subj['removal_grade']);

                            $latest_grade = null;

                            if (!empty($rg1) && !in_array($rg1, ['4','4.00'])) {
                                $latest_grade = $rg1;
                            }

                            if (!empty($rg2) && !in_array($rg2, ['4','4.00'])) {
                                $latest_grade = $rg2;
                            }

                            if ($latest_grade) {
                                $latest_grade = $this->format_grade($latest_grade);
                                $existing['removal_grade'] = "4.00/" . $latest_grade;
                            } else {
                                $existing['removal_grade'] = "4.00";
                            }

                            $merged = true;
                            break;
                        }
                    }
                }

                if (!$merged) {
                    $course_checker[] = $subj;
                }
            }

            //delete all existing grades for this student & term
            $this->General_model->delete("transcript_grades",["student_no"=>$sn,"term_id"=>$tid]);
            foreach($course_checker as $subj){
                // ➕ Queue insert
                $insert_grades[] = [
                    'student_no' => $sn,
                    'term_id' => $tid,
                    'course_no' => $subj['course_no'],
                    'long_title' => $subj['long_title'],
                    'credit' => $subj['credit'],
                    'activity' => $subj['activity'],
                    'grade' => $subj['grade'],
                    'remarks' => $subj['remarks'],
                    'removal_grade' => $subj['removal_grade'],
                    'college' => $subj['college']
                ];
            }
        }
            // echo $this->array_dump($insert_grades);
            // exit();

        // 5️⃣ Batch insert new grades
        if (!empty($insert_grades)) {
            $chunks = array_chunk($insert_grades, 500);
            foreach ($chunks as $batch) {
                $this->General_model->insert_batch('transcript_grades', $batch);
            }
        }

        $this->db->trans_complete(); // Commit if all succeed, rollback if any fail


        if ($this->db->trans_status() === FALSE) {
            echo json_encode([
                'inserted_students' => 0,
                'inserted_grades' => 0,
            ]);
        } else {
            echo json_encode([
                'inserted_students' => count($students_to_add),
                'inserted_grades' => count($insert_grades),
            ]);
        }


    }

// TEMPORARY: remove after testing
//to test access http://localhost/our_is/Transcript_imported_grades/compare_csv/test.xlsx/test.csv
// public function compare_csv($xlsx_name, $csv_name)
// {
//     set_time_limit(0);
//     ini_set('memory_limit', '1024M');

//     $dir = "assets/uploads/amis_grades/";

//     // old: original xlsx loader
//     $old = IOFactory::load($dir . $xlsx_name)->getActiveSheet()->toArray(null, true, true, true);

//     // new: your CSV loader (no array_slice, so row numbers line up)
//     $new = [];
//     $fh = fopen($dir . $csv_name, 'r');
//     $i = 0;
//     while (($r = fgetcsv($fh)) !== false) {
//         if ($r === [null]) continue;
//         $i++;
//         $new[$i] = [
//             'A'=>$r[0] ?? '', 'B'=>$r[1] ?? '', 'C'=>$r[2] ?? '', 'D'=>$r[3] ?? '',
//             'E'=>$r[4] ?? '', 'F'=>$r[5] ?? '', 'G'=>$r[6] ?? '', 'H'=>$r[7] ?? '',
//             'I'=>$r[8] ?? '', 'J'=>$r[9] ?? '', 'K'=>$r[10] ?? '', 'L'=>$r[11] ?? '',
//         ];
//     }
//     fclose($fh);

//     $diffs = [];
//     $count = 0;
//     foreach ($old as $rowNo => $row) {
//         foreach ($row as $col => $val) {
//             $n = $new[$rowNo][$col] ?? null;
//             if (trim((string)$val) !== trim((string)$n)) {
//                 $count++;
//                 if (count($diffs) < 30) {
//                     $diffs[] = "row $rowNo col $col: xlsx=[$val] csv=[$n]";
//                 }
//             }
//         }
//     }

//     header('Content-Type: application/json');
//     echo json_encode([
//         'xlsx_rows' => count($old),
//         'csv_rows'  => count($new),
//         'total_diffs' => $count,
//         'samples' => $diffs,
//     ], JSON_PRETTY_PRINT);
// }

    public function upload_sais_grades()
    {
        $datetime = date("Y-m-d H:i:s");
        $upload_dir = "assets/uploads/amis_grades/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $config['upload_path'] = $upload_dir;
        $config['allowed_types'] = 'xlsx|xls|csv';
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());
            print_r($error);
            return;
        }

        $data = $this->upload->data();
        $file_path = $upload_dir . $data['file_name'];

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file_path);
        // $spreadsheet = IOFactory::load($file_path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        // Get existing students
        $existing_students = $this->db
            ->select('student_no')
            ->get('transcript_general_data')
            ->result_array();

        $existing_student_nos = array_column($existing_students, 'student_no');

        $students = [];
        $current_student = [];
        $current_term = '';
        $current_college = '';
        $reading_grades = false;

        for ($row = 1; $row <= $highestRow; $row++) {
            $colA = trim($sheet->getCell('A' . $row)->getValue());
            $colB = trim($sheet->getCell('C' . $row)->getValue());
            $colC = trim($sheet->getCell('F' . $row)->getValue());
            $colD = trim($sheet->getCell('G' . $row)->getValue());
            $colE = trim($sheet->getCell('H' . $row)->getValue());

            $line = trim($colA . $colB . $colC . $colD . $colE);
            if ($line === '') continue;

            // ✅ Start of a new student
            if (stripos($colA, 'SAIS ID') === 0) {
                if (!empty($current_student)) {
                    $students[] = $current_student;
                }

                $current_student = [
                    'fullname' => '',
                    'student_no' => '',
                    'degree_program' => '',
                    'college' => '',
                    'nstp_no' => '',
                    'subjects' => []
                ];
                $current_term = '';
                $current_college = '';
                $reading_grades = false;
                continue;
            }

            // ✅ Extract student info
            if (stripos($colA, 'STUDENT NUMBER') === 0) {

                $current_student['student_no'] = trim(str_replace(['STUDENT NUMBER', ':'], '', $colA));
                continue;
            }
            if (stripos($colA, 'STUDENT FULL NAME') === 0) {
                $current_student['fullname'] = trim(str_replace(['STUDENT FULL NAME', ':'], '', $colA));
                continue;
            }
            if (stripos($colA, 'DEGREE PROGRAM') === 0) {
                $current_student['degree_program'] = trim(str_replace(['DEGREE PROGRAM', ':'], '', $colA));
                continue;
            }
            if (stripos($colA, 'NSTP#') === 0) {
                $current_student['nstp_no'] = trim(str_replace(['NSTP#', ':'], '', $colA));
                continue;
            }

            // ✅ College line
            if (stripos($colA, 'College of') === 0) {
                $current_college = $colA;
                $current_student['college'] = $current_college;
                continue;
            }

            // ✅ Detect term
            if (preg_match('/Semester/i', $colA) || preg_match('/Midyear/i', $colA) || preg_match('/Mid year/i', $colA)) {
                $current_term = trim($colA);
                $reading_grades = false; // reset
                continue;
            }

            // ✅ Start reading grades after the header row “Subject”
            if (stripos($colA, 'Subject') === 0) {
                $reading_grades = true;
                continue;
            }

            // ✅ Collect grade rows
            if ($reading_grades && !empty($colA) && !empty($colB) ) {

                // Skip header rows or summaries
                if (stripos($colA, 'Subject') !== false) continue;
                    $current_student['subjects'][] = [
                        'college'=>$current_college,
                        'term_id'=>convert_term_code($current_term),
                        'term' => $current_term,
                        'course_no' => $colA,
                        'long_title' => $colB,
                        'grade' => $this->format_grade($colC),
                        'removal_grade' => $colD,
                        'credit' => $colE,
                        'activity' => ''
                    ];
            }

        }

        $transcript_general_data = [];
        $transcript_grades = [];
        $update_count = 0;
        // Loop through each parsed student
        foreach ($students as $student) {
            if (empty($student['student_no'])) continue;

            $sn = $student['student_no'];

            // Check if student already exists in general data
            if (!in_array($sn, $existing_student_nos)) {
                $transcript_general_data[] = [
                    'fullname' => $student['fullname'],
                    'student_no' => $sn,
                    'degree_program' => $student['degree_program'],
                    'college' => $student['college'],
                    'nstp_no' => $student['nstp_no']
                ];
            }else{
                $update_general_data = [
                    'fullname' => $student['fullname'],
                    'student_no' => $sn,
                    'degree_program' => $student['degree_program'],
                    'college' => $student['college'],
                    'nstp_no' => $student['nstp_no']
                ];
                $this->db->where([
                    'student_no' => $sn
                ])->update('transcript_general_data', $update_general_data);
            }

            // ✅ Query existing grades *only for this student*
            $this->db->select('student_no, term_id, course_no, grade, removal_grade, remarks');
            $this->db->where('student_no', $sn);
            $existing_records = $this->db->get('transcript_grades')->result_array();

            // Build map for fast lookup
            $existing_map = [];
            foreach ($existing_records as $r) {
                $existing_map[$r['term_id']][$r['course_no']] = $r;
            }

            // Now process all subjects for this student
            foreach ($student['subjects'] as $subj) {
                $tid = $subj['term_id'];
                $cn = $subj['course_no'];
                $existing = $existing_map[$tid][$cn] ?? null;

                if ($existing) {
                    // 🔁 Update logic for existing subject
                    $update_data = [
                        'grade' => $subj['grade'],
                        'remarks' => $subj['remarks'] ?? null
                    ];

                    if (strtoupper($existing['grade']) == 'INC') {
                        if ($existing['removal_grade'] != 4.00 || $existing['removal_grade'] != 4) {
                            $update_data['removal_grade'] = $subj['removal_grade'];
                        } else {
                            $update_data['removal_grade'] = '4.00/' . $subj['removal_grade'];
                        }
                    }

                    $this->db->where([
                        'student_no' => $sn,
                        'term_id' => $tid,
                        'course_no' => $cn
                    ])->update('transcript_grades', $update_data);
                    $update_count++;
                } else {
                    // ➕ Queue new insert
                    $transcript_grades[] = [
                        'student_no' => $sn,
                        'term_id' => $tid,
                        'term' => $subj['term'],
                        'course_no' => $cn,
                        'long_title' => $subj['long_title'],
                        'grade' => $subj['grade'],
                        'removal_grade' => $subj['removal_grade'],
                        'credit' => $subj['credit'],
                        'activity' => $subj['activity'],
                        'college' => $subj['college']
                    ];
                }
            }
        }

        // ✅ Batch insert in chunks
        function batch_insert_safe($model, $table, $data, $batch_size = 500) {
            $chunks = array_chunk($data, $batch_size);
            foreach ($chunks as $chunk) {
                $model->insert_batch($table, $chunk);
            }
        }

        if (!empty($transcript_general_data)) {
            batch_insert_safe($this->General_model, 'transcript_general_data', $transcript_general_data);
        }
        if (!empty($transcript_grades)) {
            batch_insert_safe($this->General_model, 'transcript_grades', $transcript_grades);
        }

        echo json_encode([
            'students_inserted' => count($transcript_general_data),
            'grades_inserted' => count($transcript_grades),
            'update_count' => $update_count,
        ]);
    }


   	public function get_imported_grades(){
        $list = $this->Transcript_import_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
        $row = array();
        $row[] = $val->fullname;
        $row[] = $val->student_no;
        $row[] = $val->degree_program;
        $row[] = $val->college;
        $row[] = "<center>
                    <div class='btn-group'>
                    <button student_no='$val->student_no' class='btn btn-primary btn-sm download_excel tool_tip' title='Download OTR in Excel Format'>
                        <i class='fa fa-download'></i>
                    </button>
                    <button student_no='$val->student_no' class='btn btn-success btn-sm download_grades tool_tip' title='Download Grades in Excel Format'>
                        <i class='fa fa-file-excel-o'></i>
                    </button>
                    </div>
                </center>";
        // $row[] = $val->term_id;
        // $row[] = $val->course_no;
        // $row[] = $val->grade."|".$val->removal_grade;
        // $row[] = $val->credit;

        $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Transcript_import_model->count_all(),
                        "recordsFiltered" => $this->Transcript_import_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
   	}

    public function download_excel() {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);
        /* Spreadsheet Init */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

           /* Excel Header */
        $sheet->setCellValue('A1', '');
        $sheet->setCellValue('B1', 'College');
        $sheet->setCellValue('C1', 'Space1');
        $sheet->setCellValue('D1', 'Long Title');
        $sheet->setCellValue('E1', 'Space2');
        $sheet->setCellValue('F1', 'Space3');
        $sheet->setCellValue('G1', 'Grade');
        $sheet->setCellValue('H1', 'Removal Grade');
        $sheet->setCellValue('I1', 'Credit');
        $sheet->setCellValue('J1', 'activity');

        $key = 2;
        $limit = 1000;
        $offset = 0;

        $prev_student_no = '';
        $prev_term_id = '';

        while (true) {
            $rows = $this->Transcript_import_model->get_student_grades_chunked($limit, $offset);
            if (empty($rows)) break;

            foreach ($rows as $row) {
                $student_no = $row['student_no'];

                // If this is a new student, print their info
                if ($student_no !== $prev_student_no) {
                    if ($prev_student_no !== '') $key++; // spacing after previous student
                    $sheet->setCellValue('A' . $key, $row['fullname']);
                    $key++;
                    $sheet->setCellValue('A' . $key, $row['student_no']);
                    $key++;
                    $sheet->setCellValue('A' . $key, $row['degree_program']);
                    $key++;
                    $prev_term_id = ''; // reset term grouping
                }

                // If new term, print term header row
                if ($row['term_id'] !== $prev_term_id) {
                    $sheet->setCellValue('A' . $key, $row['term_id']);
                    $sheet->setCellValue('B' . $key, $row['college']);
                    $key++;
                }

                // Print grade data
                $sheet->setCellValue('B' . $key, $row['course_no']);
                $sheet->setCellValue('D' . $key, $row['long_title']);
                $sheet->setCellValue('G' . $key, $row['grade']);
                $sheet->setCellValue('H' . $key, $row['removal_grade']);
                $sheet->setCellValue('I' . $key, $row['credit']);
                $sheet->setCellValue('J' . $key, $row['activity']);

                $prev_student_no = $student_no;
                $prev_term_id = $row['term_id'];
                $key++;
            }

            $offset += $limit;
            $spreadsheet->garbageCollect();
        }

          /* Excel File Format */
        ob_end_clean();
        $file_name = "student_grades";
        $file_path = FCPATH . 'assets/phpspreadsheet_files/'.$file_name.'.xlsx';          
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        // echo json_data($file_name);
    }

    private function format_grade($grade) {
        if (is_numeric($grade)) {
            return number_format((float)$grade, 2, '.', '');
        } else {
            if($grade == "DRP"){
                $grade = "Drp";
            }elseif($grade == "INC"){
                $grade = "Inc.";
            }
            return $grade;
        }
    }
}
