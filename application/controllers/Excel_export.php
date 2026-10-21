<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Excel_export extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Reclassification_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

    public function download_excel() {
        $batch_id = $this->input->post("batch_id");
        // // Retrieve all data (or filtered data) from your data source
        $data = $this->Reclassification_model->get_student_batch($batch_id); 

        /* Spreadsheet Init */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

           /* Excel Header */
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Academic Institution');
        $sheet->setCellValue('C1', 'Academic Career');
        $sheet->setCellValue('D1', 'Term');
        $sheet->setCellValue('E1', 'Academic Level - Term Start');
        $sheet->setCellValue('F1', 'First Name');
        $sheet->setCellValue('G1', 'Last Name');
        $sheet->setCellValue('H1', 'Classification');
        $key = 2;
        foreach($data as $val){
            $sheet->setCellValue('A'.$key, $val->sais_id);
            $sheet->setCellValue('B'.$key, "UPLB");
            $sheet->setCellValue('C'.$key, $val->career);
            $sheet->setCellValue('D'.$key, $val->term);
            $sheet->setCellValue('E'.$key, $val->code);
            $sheet->setCellValue('F'.$key, $val->first_name);
            $sheet->setCellValue('G'.$key, $val->last_name);
            $sheet->setCellValue('H'.$key, $val->classification);
            $key++;
        }

          /* Excel File Format */
        $writer = new Xlsx($spreadsheet);
        $filename = 'excel-report';
         ob_end_clean();
        $file_name = "batch_".$batch_id;
        $file_path = FCPATH . 'assets/phpspreadsheet_files/'.$file_name.'.xlsx';          
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        // echo json_data($file_name);
    }

    public function nf_time_sched() {
        $this->load->model('Nf_list_model', '', TRUE);
        $batch_id = $this->input->post("batch_id");
        // // Retrieve all data (or filtered data) from your data source
        $data = $this->Nf_list_model->get_nf_list($batch_id); 

        /* Spreadsheet Init */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

           /* Excel Header */
        $sheet->setCellValue('A1', 'Time Slot');
        $sheet->setCellValue('B1', 'Monday');
        $sheet->setCellValue('C1', 'Tuesday');
        $sheet->setCellValue('D1', 'Wednesday');
        $sheet->setCellValue('E1', 'Thursday');
        $sheet->setCellValue('F1', 'Friday');
        $sheet->setCellValue('G1', 'Saturday');
        $sheet->setCellValue('H1', 'Total');

        $sheet->setCellValue('A2', '7:00-7:30');
        $sheet->setCellValue('A3', '7:30-8:00');
        $sheet->setCellValue('A4', '8:00-8:30');
        $sheet->setCellValue('A5', '8:30-9:00');
        $sheet->setCellValue('A6', '9:00-9:30');
        $sheet->setCellValue('A7', '9:30-10:00');
        $sheet->setCellValue('A8', '10:00-10:30');
        $sheet->setCellValue('A9', '10:30-11:00');
        $sheet->setCellValue('A10', '11:00-11:30');
        $sheet->setCellValue('A11', '11:30-12:00');
        $sheet->setCellValue('A12', '12:00-12:30');
        $sheet->setCellValue('A13', '12:30-1:00');
        $sheet->setCellValue('A14', '1:00-1:30');
        $sheet->setCellValue('A15', '1:30-2:00');
        $sheet->setCellValue('A16', '2:00-2:30');
        $sheet->setCellValue('A17', '2:30-3:00');
        $sheet->setCellValue('A18', '3:00-3:30');
        $sheet->setCellValue('A19', '3:30-4:00');
        $sheet->setCellValue('A20', '4:00-4:30');
        $sheet->setCellValue('A21', '4:30-5:00');
        $sheet->setCellValue('A22', '5:00-5:30');
        $sheet->setCellValue('A23', '5:30-6:00');
        $sheet->setCellValue('A24', '6:00-6:30');
        $sheet->setCellValue('A25', '6:30-7:00');
        $time_ranges = [
            '7:00-7:30',
            '7:30-8:00',
            '8:00-8:30',
            '8:30-9:00',
            '9:00-9:30',
            '9:30-10:00',
            '10:00-10:30',
            '10:30-11:00',
            '11:00-11:30',
            '11:30-12:00',
            '12:00-12:30',
            '12:30-13:00',
            '13:00-13:30',
            '13:30-14:00',
            '14:00-14:30',
            '14:30-15:00',
            '15:00-15:30',
            '15:30-16:00',
            '16:00-16:30',
            '16:30-17:00',
            '17:00-17:30',
            '17:30-18:00',
            '18:00-18:30',
            '18:30-19:00'
        ];
        $counts = [];
        function check_time($check_start_time, $check_end_time, &$time_ranges, &$counts, $day){
            // Convert check times to timestamps
            $check_start_timestamp = strtotime($check_start_time);
            $check_end_timestamp = strtotime($check_end_time);

            // Loop through each time range and check if it falls within the given time range
            foreach ($time_ranges as $range) {
                // Split the range into start and end times
                list($range_start, $range_end) = explode('-', $range);
                
                // Convert the range times to timestamps
                $range_start_timestamp = strtotime($range_start);
                $range_end_timestamp = strtotime($range_end);
                   // Ensure the day is initialized in the counts array
                if (!isset($counts[$day])) {
                    $counts[$day] = array_fill_keys($time_ranges, 0);
                }
                // Check if the entire range is within the check range
                if (($check_start_timestamp <= $range_start_timestamp && $check_end_timestamp >= $range_start_timestamp) || ($check_start_timestamp <= $range_end_timestamp && $check_end_timestamp >= $range_end_timestamp)) {
                    // If the entire range is within the check range, increment the count
                    $counts[$day][$range]++;
                }
            }
        }
        
        function convert_time_to_military_front($time) {
            // Check if time already contains ':'
            if (strpos($time, ':') === false) {
                $time .= ':00'; // Add minutes if not present
            }
            
            // Determine if the time should be PM based on the hour
            list($hour, $minute) = explode(':', $time);
            if ($hour >= 1 && $hour <= 6) {
                $hour += 12; // Convert to PM if between 1 and 6
            }
            
            // Ensure 24-hour format
            if ($hour == 24) {
                $hour = 0;
            }
            // Format to military time
            return sprintf('%02d:%s', $hour, $minute);
        }

        function convert_time_to_military_back($time) {
            // Check if time already contains ':'
            if (strpos($time, ':') === false) {
                $time .= ':00'; // Add minutes if not present
            }
            
            // Determine if the time should be PM based on the hour
            list($hour, $minute) = explode(':', $time);
            if ($hour >= 1 && $hour <= 7) {
                $hour += 12; // Convert to PM if between 1 and 6
            }
            
            // Ensure 24-hour format
            if ($hour == 24) {
                $hour = 0;
            }
            // Format to military time
            return sprintf('%02d:%s', $hour, $minute);
        }


        foreach($data as $val){
            $time = explode("-", $val->time);
            if($val->time!=""){
                $check_start_time = (strpos($time[0], ':') === false) ? convert_time_to_military_front($time[0].":00") : convert_time_to_military_front($time[0]);
                $check_end_time = (strpos($time[1], ':') === false) ? convert_time_to_military_front($time[1].":00") : convert_time_to_military_back($time[1]);
                // echo $val->days." : ".$check_start_time ." - ". $check_end_time."<br>";
                if($val->days == 'Mon' || $val->days == 'MW'){
                    //set monday to excel column B
                    check_time($check_start_time, $check_end_time, $time_ranges, $counts, 'B');
                }
                if($val->days == 'Tues' || $val->days == 'TTh' || $val->days == 'TWThF'){
                    //set tuesday to excel column C
                    check_time($check_start_time, $check_end_time, $time_ranges, $counts, 'C');
                }
                if($val->days == 'Wed' || $val->days == 'TWThF' || $val->days == 'MW'|| $val->days == 'WF' ){
                    //set wednesday to excel column D
                    check_time($check_start_time, $check_end_time, $time_ranges, $counts, 'D');
                }
                if($val->days == 'Thurs' || $val->days == 'TWThF' || $val->days == 'TTh'){
                    //set thurtsday to excel column E
                    check_time($check_start_time, $check_end_time, $time_ranges, $counts, 'E');
                }
                if($val->days == 'Fri' || $val->days == 'TWThF' || $val->days == 'WF'){
                    //set friday to excel column F
                    check_time($check_start_time, $check_end_time, $time_ranges, $counts, 'F');
                }
                if($val->days == 'Sat'){
                    //set saturday to excel column G
                    check_time($check_start_time, $check_end_time, $time_ranges, $counts, 'G');
                }
            }
        }

        $row_start = 2;
        // foreach ($time_ranges as $key=>$range) {
        //     $row_num = $row_start+$key;
        //     if(isset($counts['B'][$range])){
        //         $sheet->setCellValue('B'.$row_num, $counts['B'][$range]);
        //     }
        //     if(isset($counts['C'][$range])){
        //         $sheet->setCellValue('C'.$row_num, $counts['C'][$range]);
        //     }
        //     if(isset($counts['D'][$range])){
        //         $sheet->setCellValue('D'.$row_num, $counts['D'][$range]);
        //     }
        //     if(isset($counts['E'][$range])){
        //         $sheet->setCellValue('E'.$row_num, $counts['E'][$range]);
        //     }
        //     if(isset($counts['F'][$range])){
        //         $sheet->setCellValue('f'.$row_num, $counts['F'][$range]);
        //     }
        //     if(isset($counts['G'][$range])){
        //         $sheet->setCellValue('g'.$row_num, $counts['G'][$range]);
        //     }
        // }
        // echo "<pre>".var_export($counts,true)."</pre>";
        // exit();
        //iterate to counts array to return all days
        foreach ($counts as $key=>$day) {
            $row_num = 2;
            //iterate all time in the day
            foreach($day as $time){
                //return value of each time use key of the day as excel column and row_num as row num
                $sheet->setCellValue($key.$row_num, $time);
                //increment row num
                $row_num++;
            }
        }
          /* Excel File Format */
        $writer = new Xlsx($spreadsheet);
        $filename = 'excel-report';
         ob_end_clean();
        $file_name = "batch_".$batch_id;
        $file_path = FCPATH . 'assets/phpspreadsheet_files/'.$file_name.'.xlsx';          
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        // echo json_data($file_name);
    }

    public function Equota() {
        $this->load->model('Equota_model', '', TRUE);
        $where = new stdClass();
        $where->year = $this->input->post("year");
        $where->sem = $this->input->post("sem");
        $data = $this->Equota_model->report($where);
        /* Spreadsheet Init */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

           /* Excel Header */
        $sheet->setCellValue('A1', 'Courses');
        $sheet->setCellValue('B1', 'College');
        $sheet->setCellValue('C1', 'Dept/Inst');
        $sheet->setCellValue('D1', '# of Slots');
        $key = 2;
        foreach($data as $val){
            $sheet->setCellValue('A'.$key, $val->code);
            $sheet->setCellValue('B'.$key, $val->col_code);
            $sheet->setCellValue('C'.$key, $val->dept_code);
            $sheet->setCellValue('D'.$key, $val->total);
            $key++;
        }

          /* Excel File Format */
        $writer = new Xlsx($spreadsheet);
        $filename = 'excel-report';
         ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="equota.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        // echo json_data($file_name);
    }

    public function dummy_students() {
        $this->load->model('Equota_model', '', TRUE);
        $where = new stdClass();
        $year = $this->input->post("year");
        $sem = $this->input->post("sem");
        $where->year = $year;
        $where->sem = $sem;
        $data = $this->Equota_model->dummy_students($where);
        /* Spreadsheet Init */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

           /* Excel Header */
        $key = 1;
        $num_start = 60000;
        foreach($data as $val){
            for($i=1;$i<=$val->equota;$i++){
                $stud_num =$num_start+$key;
                $sheet->setCellValue('A'.$key, "$year-$stud_num");
                $sheet->setCellValue('B'.$key, "$val->ap_code-$i");
                $sheet->setCellValue('C'.$key, "M");
                $sheet->setCellValue('D'.$key, "1");
                $sheet->setCellValue('E'.$key, "$val->ap_code-$val->year");
                $sheet->setCellValue('F'.$key, "$val->college_code");
                $sheet->setCellValue('G'.$key, ",");
                $sheet->setCellValue('H'.$key, ",");
                $sheet->setCellValue('I'.$key, ",");
                $sheet->setCellValue('J'.$key, ",");
                $sheet->setCellValue('K'.$key, ",");
                $key++;
            }
        }

          /* Excel File Format */
        $writer = new Xlsx($spreadsheet);
        $filename = 'excel-report';
         ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="equota.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        // echo json_data($file_name);
    }

    public function check_nf_sched(){
        $this->load->model('Equota_model', '', TRUE);
        $year = $this->input->post("year");
        $sem = $this->input->post("sem");
        $datetime = date("Y-m-d H:i:s");
        $upload_dir = "assets/uploads/nf_sched/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);  //create directory if not exist
        }

        $config['upload_path'] = $upload_dir;
        $config['allowed_types'] = 'xlsx|xls|csv';
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());
            print_r($error);
        } else {
            $data = $this->upload->data();
            $file_path = $upload_dir . $data['file_name'];

            $spreadsheet = IOFactory::load($file_path);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            //get all subject
            $nf_subjects = $this->General_model->list_dynamic('subjects',['status'=>0]);
            //convert all subject to array for use in in_array
            $nf_subjects_codes = array_column($nf_subjects, 'code');
            $subjects_list = array_map('strtoupper', $nf_subjects_codes);
            //set start of column to be checked
            $col_start = 1;
            $data = array();
            //for excel generation
            $spreadsheet1 = new Spreadsheet();
            $sheet = $spreadsheet1->getActiveSheet();
            $num = 1;
            $nf_sched = array();
            foreach ($sheetData as $key=>$row) {
                //implode subject code
                $subject_code = TRIM(strtoupper($row['A']));
                $section = TRIM($row['C']);

                // $class_no = TRIM($row['K']);
                $component = TRIM($row['B']);
                $capacity = TRIM($row['G']);
                //time
                $start = TRIM($row['D']);
                $end = TRIM($row['E']);
                //convert time to REGIST required format
                if (strpos($start, ':') !== false && strpos($end, ':')) {
                    list($start_hour, $start_min) = explode(':', $start);
                    list($end_hour, $end_min) = explode(':', $end);
                    if($start_min == "00"){
                        // Convert start time to 12-hour format without leading zeros
                        $start = date('g', strtotime($start));
                    }else{
                        $start = date('g:i', strtotime($start));
                    }
                    if($end_min == "00"){
                        // Convert end time to 12-hour format without leading zeros
                        $end = date('g', strtotime($end));
                    }else{
                        $end = date('g:i', strtotime($end));
                    }
                }else{
                    $start = "";
                    $end = "";
                }
                //set time
                $time=$start . '-' . $end;
                //set days to regist required format
                $days = TRIM($row['F']);
                $days_array = ["M"=>"Mon","T"=>"Tues","W"=>"Wed","TH"=>"Thurs","F"=>"Fri","S"=>"Sat"];
                // Check if the string is a single character representing a day
                if (isset($days_array[$days])) {
                    $days =  $days_array[$days];
                }
                if($days == "TWTHF"){
                    $days = "TWThF";
                }
                if($days == "TTH"){
                    $days = "TTh";
                }

                //section
                if(in_array($component,["LAB","RCT","CPT"])){
                    $catalog = $row['A'];
                    if (strpos($catalog, '.') !== false) {
                        //remove dash if there is
                        $section = str_replace('-', '', $section);
                    }else{
                        if (strpos($section, '-') === false) {
                            // Replace two characters from the right with a dash
                            $section = substr_replace($section, '-', -2, 0);
                        }
                    }

                }
                // echo $subject_code.'<br>';
                //check if created subject code is in the list of subject from the database
                if($key > $col_start && in_array($subject_code,$subjects_list)){
                    if($num > 0){
                        // if($subject_code == "AAE 10"){
                        //     echo $subject_code == "AAE 10 PIDO";
                        // }
                        $nf_sched[]= array("subject"=>$subject_code, 
                                        "capacity"=>$capacity, 
                                        "section"=>$section, 
                                        "time"=>$time, 
                                        "days"=>$days, 
                                        "year"=>$year, 
                                        "sem"=>$sem,
                                        "component"=>$component,
                                        "date_uploaded"=>$datetime,
                                        "sais_section"=>TRIM($row['C']),
                                        "sais_day"=>TRIM($row['F']),
                                        "sais_time"=>TRIM($row['D'])."-".TRIM($row['E']),
                                    );
                    }
                    $num++;
                }
            }
            $this->General_model->insert_batch('nf_sched',$nf_sched);
            $where = new stdClass();
            $where->year = $year;
            $where->sem = $sem;
            $where->date_uploaded = $datetime;
            $subject_cap = $this->Equota_model->check_nf_equota($where);
            unlink($file_path); // Delete the uploaded file
            $json_data = new stdClass();
            if($subject_cap){
                $json_data->error = 1;
                $json_data->data = $subject_cap;
            }else{
                $json_data->error = 0;           
            }
            echo json_encode($json_data);
        }
    }

    public function download_nf_sched(){
        $this->load->model('Equota_model', '', TRUE);
        /* Spreadsheet Init */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $year = $this->input->post("year");
        $sem = $this->input->post("sem");
        $date_uploaded = $this->input->post("date_uploaded");
        $where = new stdClass();
        $where->year=$year;
        $where->sem=$sem;
        $where->date_uploaded=$date_uploaded;
        $nf_data = $this->General_model->list_dynamic("nf_sched",$where);
        //get all subject
        $nf_subjects = $this->General_model->list_dynamic('subjects',['status'=>0]);
        //convert all subject to array for use in in_array
        $subjects_list = array_column($nf_subjects, 'code');
        //set start of column to be checked
        $start = 2;
        $data = array();
        $num = 1;
        $nf_sched = array();
        $sheet->setCellValue('A'.$num, "Subject");
        $sheet->setCellValue('B'.$num, "Section");
        $sheet->setCellValue('C'.$num, "Cap Enrl");
        $sheet->setCellValue('D'.$num, "Time");
        $sheet->setCellValue('E'.$num, "Days");
        $sheet->setCellValue('F'.$num, "TBA");
        $sheet->setCellValue('G'.$num, "TBA");
        $sheet->setCellValue('H'.$num, "TBA");
        $sheet->setCellValue('I'.$num, "SAIS Class No");
        $sheet->setCellValue('J'.$num, "SAIS Section");
        $sheet->setCellValue('K'.$num, "SAIS Day");
        $sheet->setCellValue('L'.$num, "SAIS Time");
        if($nf_data){
            foreach ($nf_data as $key=>$row) {
                $subject_code = $row->subject;
                $section = $row->section;
                $capacity = $row->capacity;
                $time = $row->time;
                $days = $row->days;
                $class_no = $row->class_no;
                $sais_section = $row->sais_section;
                $sais_day = $row->sais_day;
                $sais_time = $row->sais_time;
                //check if cretaed subject code is in the list of subject from the database
                if(in_array($subject_code,$subjects_list)){
                    $num++;
                    $sheet->setCellValue('A'.$num, "$subject_code");
                    $sheet->setCellValue('B'.$num, "$section");
                    $sheet->setCellValue('C'.$num, "$capacity");
                    $sheet->setCellValue('D'.$num, "$time");
                    $sheet->setCellValue('E'.$num, "$days");
                    $sheet->setCellValue('F'.$num, ",");
                    $sheet->setCellValue('G'.$num, ",");
                    $sheet->setCellValue('H'.$num, ",");
                    $sheet->setCellValue('I'.$num, "$class_no");
                    $sheet->setCellValue('J'.$num, "$sais_section");
                    $sheet->setCellValue('K'.$num, "$sais_day");
                    $sheet->setCellValue('L'.$num, "$sais_time");
                }
            }
            /* Excel File Format */
            $writer = new Xlsx($spreadsheet);
             ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="nf_sched.xlsx"'); 
            header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }

    public function download_nf_subj(){
        $this->load->model('Equota_model', '', TRUE);
        /* Spreadsheet Init */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $year = $this->input->post("year");
        $sem = $this->input->post("sem");
        $date_uploaded = $this->input->post("date_uploaded");
        $where = new stdClass();
        $where->year=$year;
        $where->sem=$sem;
        $where->date_uploaded=$date_uploaded;
        $nf_data = $this->Equota_model->check_nf_equota($where);
        $num = 1;
        $sheet->setCellValue('A'.$num, "Subject");
        $sheet->setCellValue('B'.$num, "Equota Requested");
        $sheet->setCellValue('C'.$num, "Capacity Created");
        $sheet->setCellValue('D'.$num, "Difference");
        if($nf_data){
            foreach ($nf_data as $key=>$row) {
                $subject_code = $row->subject;
                $capacity = $row->capacity==""?0:$row->capacity;
                $equota = $row->equota==""?0:$row->equota;
                $diff = $equota - $capacity;
                $num++;
                $sheet->setCellValue('A'.$num, "$subject_code");
                $sheet->setCellValue('B'.$num, "$equota");
                $sheet->setCellValue('C'.$num, "$capacity");
                $sheet->setCellValue('D'.$num, "$diff");
            }
            /* Excel File Format */
            $writer = new Xlsx($spreadsheet);
             ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="nf_sched.xlsx"'); 
            header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        }
    }
    
}
