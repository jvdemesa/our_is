<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;

class Transcript_tcg extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->isLogin();
		$this->load->model('Clients_model', '', TRUE);
		$this->load->model('General_model', '', TRUE);
	}

  	public function index(){
 		// $this->load->model('Dashboard_model');
		$this->load->model('Admin_model', '', TRUE);
		// $this->isAllowed();	
		$this->data['userdata'] = $this->session->userdata(SESSION_DATA);
		$this->data['links'] = $this->linkGenerator();
		$this->data['content'] = 'Transcript_tcg/index';
		$this->data['list'] = $this->Admin_model->list('link');
		$this->load->view('template/admin', $this->data);
  	}

   	public function get_lists(){
    	$this->load->model('Transcript_tcg_model');
    	$this->load->model('General_model');
        $list = $this->Transcript_tcg_model->get_datatables();
        $data = array();
        foreach ($list as $val) {
	        $row = array();
	        $row[] = $val->sais_id;
	        $row[] = $val->campus_id;
	        $row[] = $val->term;
	        $row[] = $val->last_name;
	        $row[] = $val->first_name;
	        $row[] = $val->middle_name;
            $data[] = $row;
        }
 
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Transcript_tcg_model->count_all(),
                        "recordsFiltered" => $this->Transcript_tcg_model->count_filtered(),
                        "data" => $data,
                );
        //output to json format
        echo json_encode($output);
   	}

    public function download_excel() {

    	$this->load->model('Transcript_tcg_model');
        $data = $this->Transcript_tcg_model->get_students(); 
        /* Spreadsheet Init */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

		$sheet->getColumnDimension('A')->setWidth(15); // Set column A width to 20
		$sheet->getColumnDimension('B')->setWidth(1.78); // Set column B width to 15
		$sheet->getColumnDimension('C')->setWidth(65); // Set column C width to 25
		$sheet->getColumnDimension('D')->setWidth(1.78); // Set column D width to 25
		$sheet->getColumnDimension('E')->setWidth(1.78); // Set column E width to 25
		$sheet->getColumnDimension('F')->setWidth(12); // Set column F width to 25
		$sheet->getColumnDimension('G')->setWidth(11); // Set column G width to 25
		$sheet->getColumnDimension('H')->setWidth(14); // Set column H width to 25

		// Merge cells from A1 to H1
		$sheet->mergeCells('A1:H1');
		// Set a value for the merged cell
        $sheet->setCellValue('A1', 'University of the Philippines Los Banos');
		// Apply bold styling
		$sheet->getStyle('A1')->getFont()->setBold(true);
		$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

		$row = 2;
		foreach($data as $key=>$val){
			// Merge cells
			$sheet->mergeCells("A$row:H$row");
			// Set a value for the merged cell
	        $sheet->setCellValue("A$row", "");

			$row++;
			// Merge cells
			$sheet->mergeCells("A$row:H$row");
			// Set a value for the merged cell
	        $sheet->setCellValue("A$row", "STUDENT NUMBER : $val->campus_id");

			$row++;
			// Merge cells
			$sheet->mergeCells("A$row:H$row");
			// Set a value for the merged cell
	        $sheet->setCellValue("A$row", "STUDENT FULL NAME  : $val->fullname");
	        
			$row++;
			// Merge cells
			$sheet->mergeCells("A$row:H$row");
			// Set a value for the merged cell
	        $sheet->setCellValue("A$row", "DEGREE PROGRAM : $val->acad_group");
	        
			$row++;
			// Merge cells
			$sheet->mergeCells("A$row:H$row");
			// Set a value for the merged cell
	        $sheet->setCellValue("A$row", "NSTP# :");
	        
			$row++;
			// Merge cells
			$sheet->mergeCells("A$row:H$row");
			// Set a value for the merged cell
	        $sheet->setCellValue("A$row", "COLLEGE NAME");
			// Apply bold styling
			$sheet->getStyle("A$row")->getFont()->setUnderline(true);
			$sheet->getStyle("A$row")->getAlignment()->setHorizontal('center');
			//new foreach for each semesters
			for($b=1;$b<3;$b++){
				$row++;
				// Merge cells
				$sheet->mergeCells("A$row:H$row");
				// Set a value for the merged cell
		        $sheet->setCellValue("A$row", "SEMESTER $b, SY");

				$row++;
		        $sheet->setCellValue("A$row", "Subject");
		        $sheet->setCellValue("B$row", "");
		        $sheet->setCellValue("C$row", "Course Title");
		        $sheet->setCellValue("D$row", "");
		        $sheet->setCellValue("E$row", "");
		        $sheet->setCellValue("F$row", "Final Grade");
		        $sheet->setCellValue("G$row", "Removal");
		        $sheet->setCellValue("H$row", "Units Taken");
		        //add BG color
				$sheet->getStyle("A$row:H$row")->getFill()
				    ->setFillType(Fill::FILL_SOLID)
				    ->getStartColor()->setARGB('D8ECF3');
			    //set borders
				$sheet->getStyle("A$row:H$row")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
				$sheet->getStyle("A$row:H$row")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
				$sheet->getStyle("A$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
				$sheet->getStyle("C$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
				$sheet->getStyle("F$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
				$sheet->getStyle("G$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
				$sheet->getStyle("H$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
				$sheet->getStyle("H$row")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);

				//foreach for courses/subjects
				for($a=0;$a<5;$a++){
					$row++;
			        $sheet->setCellValue("A$row", "");
			        $sheet->setCellValue("B$row", "");
			        $sheet->setCellValue("C$row", "");
			        $sheet->setCellValue("D$row", "");
			        $sheet->setCellValue("E$row", "");
			        $sheet->setCellValue("F$row", "");
			        $sheet->setCellValue("G$row", "");
			        $sheet->setCellValue("H$row", "");
				    //set borders
					$sheet->getStyle("A$row:H$row")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
					$sheet->getStyle("A$row:H$row")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
					$sheet->getStyle("A$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
					$sheet->getStyle("C$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
					$sheet->getStyle("F$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
					$sheet->getStyle("G$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
					$sheet->getStyle("H$row")->getBorders()->getLeft()->setBorderStyle(Border::BORDER_THIN);
					$sheet->getStyle("H$row")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
				}//end for loop courses/subjects
			}//end for loop semester

			$row++;
	    }

          /* Excel File Format */
        $writer = new Xlsx($spreadsheet);
        $filename = 'excel-report';
         ob_end_clean();
        $file_name = "TCG-".date("Y_m_d");
        $file_path = FCPATH . 'assets/phpspreadsheet_files/'.$file_name.'.xlsx'; 
	    if (isset($_GET['token'])) {
	        setcookie("fileDownloadComplete", $_GET['token'], time() + 60, "/");
	    }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
        // echo json_data($file_name);
    }
}