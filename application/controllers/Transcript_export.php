<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
function insertLogo($sheet, $coordinate) {
    $drawing = new Drawing();
    $drawing->setName('UP Logo');
    $drawing->setDescription('UPLB');
    $drawing->setPath(FCPATH . 'assets/images/up-logo-black.png');
    $drawing->setCoordinates($coordinate);
    $drawing->setResizeProportional(false);
    $drawing->setHeight(92);
    $drawing->setWidth(93);
        // Optional offset to move inside the cell
    $drawing->setOffsetX(10);
    $drawing->setOffsetY(5);
    $drawing->setWorksheet($sheet); // Very important!
}

class Transcript_export extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->isLogin();
        $this->load->model('Transcript_import_model', '', TRUE);
        $this->load->model('General_model', '', TRUE);
        $this->load->helper(['file', 'download']);

    }

    public function Regular() {
        $student_no = $this->input->post('student_no');
        $personal_data = $this->Transcript_import_model->get_student_personal_data($student_no, 'tr_details');
        if (empty($personal_data['student_no'])) {
			$personal_data = $this->Transcript_import_model->get_student_personal_data($student_no, 'student_transcript_info');
        }
        $gen_data = $this->Transcript_import_model->get_student_details($student_no);
        $filePath = FCPATH . 'assets/phpspreadsheet_files/transcript.xls';
        // Load template
        $template = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $templateSheet = $template->getActiveSheet();

        // Create new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $totalLineCount = 0; // Initialize total line counter
        $rows = $this->Transcript_import_model->get_student_grades($student_no);
        $prev_term_id = "";
        $prev_college = "";
        $key = 23;
        foreach ($rows as $row) {
            if(TRIM($row['course_no']) == "GI ." || TRIM($row['course_no']) == "GI"){
                continue; // skip this row and go to the next one
            }
            // If new term, print term header row
            if ($row['term_id'] !== $prev_term_id) {
                $totalLineCount++;
                $prev_term_id = $row['term_id'];
            }
            // Print grade data
        
            if (in_array($row['course_no'], ["HK 12", "NSTP 1", "NSTP 2"]) && !empty($row['activity'])) {
                $long_title = $row['long_title'] . " (" . $row['activity'] . ")";
            } else {
                $long_title = $row['long_title'];
            }

            if($long_title == ""){
                $long_title = $row['course_no'];
            }
            // char width map (extend as needed)
            $charWidthMap = [
                'i' => 0.4, 'l' => 0.4, 't' => 0.5,
                'a' => 0.6, 'e' => 0.6, 'n' => 0.6, 'o' => 0.6,
                'm' => 0.8, 'w' => 0.9, 'W' => 1.0, 'M' => 1.0,
                ' ' => 0.4,
                // add more chars if you want more accuracy...
            ];

            $maxPixelWidth = 175; // tweak if needed

            // build long_title as before
            if (in_array($row['course_no'], ["HK 12", "NSTP 1", "NSTP 2"]) && !empty($row['activity'])) {
                $long_title = $row['long_title'] . " (" . $row['activity'] . ")";
            } else {
                $long_title = $row['long_title'];
            }
            if($long_title == ""){
                $long_title = $row['course_no'];
            }

            // get lines (word-aware wrapping)
            $lines = $this->splitTextToLines($long_title, $charWidthMap, $maxPixelWidth, 10 /*font size*/);

            // Add number of lines for this course to total
            $totalLineCount += count($lines);
        }
        /* === 1. Page Setup & Margins === */
        $sheet->getPageSetup()
            ->setOrientation($templateSheet->getPageSetup()->getOrientation())
            ->setPaperSize($templateSheet->getPageSetup()->getPaperSize())
            ->setScale($templateSheet->getPageSetup()->getScale())
            ->setFitToPage($templateSheet->getPageSetup()->getFitToPage())
            ->setFitToWidth($templateSheet->getPageSetup()->getFitToWidth())
            ->setFitToHeight($templateSheet->getPageSetup()->getFitToHeight());

        $sheet->getPageMargins()
            ->setTop($templateSheet->getPageMargins()->getTop())
            ->setBottom($templateSheet->getPageMargins()->getBottom())
            ->setLeft($templateSheet->getPageMargins()->getLeft())
            ->setRight($templateSheet->getPageMargins()->getRight())
            ->setHeader($templateSheet->getPageMargins()->getHeader())
            ->setFooter($templateSheet->getPageMargins()->getFooter());

        $sheet->getPageSetup()
            ->setFitToWidth(1)   // fit to 1 page wide
            ->setFitToHeight(0);

        /* === 2. Default Font === */
        $defaultFont = $template->getDefaultStyle()->getFont();
        $spreadsheet->getDefaultStyle()->getFont()
            ->setName($defaultFont->getName())
            ->setSize($defaultFont->getSize())
            ->setBold($defaultFont->getBold())
            ->setItalic($defaultFont->getItalic())
            ->setUnderline($defaultFont->getUnderline())
            ->getColor()->setRGB($defaultFont->getColor()->getRGB());

        /* === 3. Column Widths (A–H) === */
        foreach (range('A', 'H') as $col) {
            $width = $templateSheet->getColumnDimension($col)->getWidth();
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        /* === 4. Row Heights (1–10) === */
        for ($i = 1; $i <= 10; $i++) {
            $height = $templateSheet->getRowDimension($i)->getRowHeight();
            if ($height > -1) { // -1 means "default"
                $sheet->getRowDimension($i)->setRowHeight(14.5);
            }
        }

        $sheet->getRowDimension(2)->setRowHeight(20.5);
        $sheet->getRowDimension(3)->setRowHeight(20.5);
        $sheet->getRowDimension(5)->setRowHeight(17.5);
        $sheet->getRowDimension(18)->setRowHeight(15);
        $sheet->getRowDimension(19)->setRowHeight(20.5);


        // Remove gridlines
        $sheet->setShowGridlines(false);
        insertLogo($sheet, 'A1');

        $sheet->setCellValue('C2', 'OFFICE OF THE UNIVERSITY REGISTRAR');
        $sheet->getStyle('C2')->getFont()
        ->setName('Palatino Linotype')
        ->setSize(14)
        ->setBold(true);     

        $sheet->setCellValue('C3', 'UNIVERSITY OF THE PHILIPPINES LOS BAÑOS');
        $sheet->getStyle('C3')->getFont()
        ->setName('Palatino Linotype')
        ->setSize(14)
        ->setBold(true)
        ->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
        $sheet->mergeCells('A5:H5');  
        $sheet->setCellValue('A5', 'OFFICIAL TRANSCRIPT OF RECORDS');
        $sheet->getStyle('A5')->getFont()
            ->setName('Palatino Linotype')
            ->setSize(12);
        $sheet->getStyle('A5')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // $sheet->setCellValue('E7', 'Name:  '.$gen_data['fullname']);
        // $sheet->getStyle('E7')->getFont()
        // ->setName('Palatino Linotype')
        // ->setBold(true);

        //Setup to make part of text underline
        $richText = new RichText();
        $richText->createText('Name:  ');
        // use richtext to make part of text underline
        $underlined = $richText->createTextRun($gen_data['fullname']);
        $underlined->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10)
            ->setUnderline(Font::UNDERLINE_SINGLE)
            ->setBold(true);  // <-- make it bold
        $sheet->setCellValue('E7', $richText);
        $sheet->getStyle('E7')->getFont()
        ->setName('Palatino Linotype');

        $sheet->setCellValue('A8', 'Entrance Data:');
        $sheet->getStyle('A8')->getFont()
        ->setName('Palatino Linotype');
        //Setup to make part of text underline
        $richText = new RichText();
        $richText->createText('Student No. ');
        // use richtext to make part of text underline
        $student_no = $gen_data['student_no'];
        $formatted_student_no = substr($student_no, 0, 4) . '-' . substr($student_no, 4);
        $underlined = $richText->createTextRun($formatted_student_no);
        $underlined->getFont()
        ->setName('Palatino Linotype')
        ->setSize(10)
        ->setUnderline(Font::UNDERLINE_SINGLE);
        $sheet->setCellValue('E8', $richText);
        $sheet->getStyle('E8')->getFont()
        ->setName('Palatino Linotype');

        $richText = new RichText();
        // Normal text
        $richText->createText('Sex at birth: ');
        $sex = $richText->createTextRun($personal_data['sex']);
        $sex->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10)
            ->setUnderline(Font::UNDERLINE_SINGLE);
        // Set to cell
        $sheet->setCellValue('G8', $richText);
        $sheet->getStyle('G8')->getFont()
        ->setName('Palatino Linotype');


        if($personal_data['sem_admitted']==="1st Semester"){
            $sem_admitted ="First Semester";
        }
        if($personal_data['sem_admitted']==="2nd Semester"){
            $sem_admitted ="Second Semester";
        }

        $richText = new RichText();
        // Normal text
        $richText->createText('Date/Semester admitted: ');
        $admitted = $richText->createTextRun($sem_admitted . ', ' . $personal_data['ay_admitted']);
        $admitted->getFont()
                ->setName('Palatino Linotype')
                ->setSize(10)
                ->setUnderline(Font::UNDERLINE_SINGLE);
        // Set to cell
        $sheet->setCellValue('A9', $richText);
        $sheet->getStyle('A9')->getFont()
        ->setName('Palatino Linotype');

        $birthday = !empty($personal_data['birthday'])
            ? date("d M Y", strtotime($personal_data['birthday']))
            : '';
        $richText = new RichText();
        // Normal text
        $richText->createText('Date & Place of Birth: ');
        $dpob = $richText->createTextRun($birthday.' - '.$personal_data['pob']);
        $dpob->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10)
            ->setUnderline(Font::UNDERLINE_SINGLE);
        // Set to cell
        $sheet->setCellValue('E9', $richText);
        $sheet->getStyle('E9')->getFont()
        ->setName('Palatino Linotype');

        $richText = new RichText();
        // Normal text
        $richText->createText('Category: ');
        $category = $richText->createTextRun($personal_data['admitted_as']);
        $category->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10)
            ->setUnderline(Font::UNDERLINE_SINGLE);
        // Set to cell
        $sheet->setCellValue('A10', $richText);
        $sheet->getStyle('A10')->getFont()
        ->setName('Palatino Linotype');

        $richText = new RichText();
        // Normal text
        $richText->createText('Father\'s Name: ');
        $fathers_name = $richText->createTextRun($personal_data['fathers_name']);
        $fathers_name->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10)
            ->setUnderline(Font::UNDERLINE_SINGLE);
        // Set to cell
        $sheet->setCellValue('E10', $richText);
        $sheet->getStyle('E10')->getFont()
        ->setName('Palatino Linotype');

        $sheet->setCellValue('A11', 'Diploma/Title/Degree:');
        $sheet->getStyle('A11')->getFont()
        ->setName('Palatino Linotype');

        $richText = new RichText();
        // Normal text
        $richText->createText('Mother\'s Name: ');
        $mothers_name = $richText->createTextRun($personal_data['mothers_name']);
        $mothers_name->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10)
            ->setUnderline(Font::UNDERLINE_SINGLE);
        // Set to cell
        $sheet->setCellValue('E11', $richText);
        $sheet->getStyle('E11')->getFont()
        ->setName('Palatino Linotype');


        $sheet->setCellValue('A12', 'High School/College:');
        $sheet->getStyle('A12')->getFont()
        ->setName('Palatino Linotype');
        $sheet->setCellValue('E12', 'Degree/Title/Course');
        $sheet->getStyle('E12')->getFont()
        ->setName('Palatino Linotype');

        $sheet->setCellValue('A14', 'Date graduated/last attended:');
        $sheet->getStyle('A14')->getFont()
        ->setName('Palatino Linotype');

        $sheet->setCellValue('A15', 'NCEE rating:');
        $sheet->getStyle('A15')->getFont()
        ->setName('Palatino Linotype');
        $sheet->setCellValue('C15', 'Year taken:');
        $sheet->getStyle('C15')->getFont()
        ->setName('Palatino Linotype');

        $sheet->setCellValue('A16', 'S.O. No.:');
        $sheet->getStyle('A16')->getFont()
        ->setName('Palatino Linotype');
        $sheet->setCellValue('A17', 'Date:');
        $sheet->getStyle('A17')->getFont()
        ->setName('Palatino Linotype');

        $sheet->getStyle('A18:H18')->applyFromArray([
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ]);

        $sheet->mergeCells('A19:H19');  
        $sheet->setCellValue('A19', 'COLLEGIATE RECORDS');
        $sheet->getStyle('A19')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('A19')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A19:H19')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
        $sheet->getStyle('A20:H20')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
        $sheet->getStyle('B20')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
        $sheet->getStyle('C20')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);      
        $sheet->mergeCells('F20:G20');  
        $sheet->setCellValue('F20', 'Grades');
        $sheet->getStyle('F20')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('F20')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('F20')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
        $sheet->getStyle('H20')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 

        $sheet->mergeCells('A21:B21');  
        $sheet->setCellValue('A21', 'Course Number');
        $sheet->getStyle('A21')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('A21')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A21')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 


        $sheet->mergeCells('C21:E21');  
        $sheet->setCellValue('C21', 'Course Title');
        $sheet->getStyle('C21')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('C21')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C21')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 

        $sheet->getStyle('F21')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 

        $sheet->setCellValue('G21', 'Re-exam');
        $sheet->getStyle('G21')->getFont()
        ->setName('Palatino Linotype')
        ->setSize(7)
        ->setBold(true);
        $sheet->getStyle('G21')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
        $sheet->getStyle('G21')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 

        $sheet->getStyle('H21')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->getStyle('A22:H22')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->getStyle('B22')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->getStyle('E22')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->setCellValue('F22', 'Final');
        $sheet->getStyle('F22')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('F22')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('F22')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->setCellValue('G22', 'Completion');
        $sheet->getStyle('G22')->getFont()
        ->setName('Palatino Linotype')
        ->setSize(7)
        ->setBold(true);
        $sheet->getStyle('G22')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
        $sheet->getStyle('G22')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->setCellValue('H22', 'Credits');
        $sheet->getStyle('H22')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('F22')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('H22')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->getStyle('A23:H23')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);


        $sheet->mergeCells('E13:H13');  
        $sheet->mergeCells('E14:H14');  
        $sheet->mergeCells('E15:H15');  
        $sheet->mergeCells('E16:H16');  
        $sheet->mergeCells('E17:H17');  
        //add border e13 to h17
        $sheet->getStyle('E13:H17')->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,   // You can also use BORDER_MEDIUM or BORDER_THICK
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->setCellValue('E15', $personal_data['degree']);
        $sheet->getStyle('E15')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('E15')->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        //add DATA
        $rows = $this->Transcript_import_model->get_student_grades($student_no);
        $prev_term_id = "";
        $key = 23;
        $lastKey = array_key_last($rows); // get the last index/key
        $continue_page_data = [];
        $page_num_data = [];
        foreach ($rows as $row_key=>$row) {
            if(TRIM($row['course_no']) == "GI ." || TRIM($row['course_no']) == "GI"){
                continue; // skip this row and go to the next one
            }
            $sheet->getStyle("A$key:E$key")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B$key")->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);

            $sheet->getStyle("C$key")->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);

            $sheet->getStyle("E$key")->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
            $sheet->getStyle("F$key")->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
            $sheet->getStyle("F$key")->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
            $sheet->getStyle("G$key")->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]); 
            $sheet->getStyle("H$key")->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
            // If new college, print college header row
            $college = $row['college'] == "College of Arts and Science"?$row['college']."s":$row['college'];
            if ($college !== $prev_college) {
                $sheet->mergeCells("C$key:E$key");  
                $sheet->setCellValue('C' . $key, $college);
                $sheet->getStyle('C' . $key)
                    ->getFont()
                    ->setName('Palatino Linotype')
                    ->setSize(10)
                    ->setBold(true)
                    ->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);

                // Alignment must be set separately:
                $sheet->getStyle('C' . $key)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $key++;
            }

            // If new term, print term header row
            $term_id= $row['term_id'];
            if ($term_id !== $prev_term_id) {
                $sheet->setCellValue('A' . $key, convert_term_text($term_id));
                $sheet->getStyle('A' . $key)->getFont()
                    ->setName('Palatino Linotype')
                    ->setSize(10)
                    ->setBold(true)
                    ->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);

                    $sheet->getStyle("B$key")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("E$key")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("F$key")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("G$key")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);

                $key++;
            }


            if (in_array($row['course_no'], ["HK 12", "NSTP 1", "NSTP 2"]) && !empty($row['activity'])) {
                $long_title = $row['long_title'] . " (" . $row['activity'] . ")";
            } else {
                $long_title = $row['long_title'];
            }
            if($long_title == ""){
                $long_title = $row['course_no'];
            }
            // char width map (extend as needed)
            $charWidthMap = [
                'i' => 0.4, 'l' => 0.4, 't' => 0.5,
                'a' => 0.6, 'e' => 0.6, 'n' => 0.6, 'o' => 0.6,
                'm' => 0.8, 'w' => 0.9, 'W' => 1.0, 'M' => 1.0,
                ' ' => 0.4,
                // add more chars if you want more accuracy...
            ];

            // compute your actual max column pixel width:
            // Example: convert Excel width units to pixels roughly: pixels ≈ widthUnits * 7
            // If you stored column width e.g. 13.42578125:
            $maxPixelWidth = 175; // tweak multiplier if needed

            // build long_title as before
            if (in_array($row['course_no'], ["HK 12", "NSTP 1", "NSTP 2"]) && !empty($row['activity'])) {
                $long_title = $row['long_title'] . " (" . $row['activity'] . ")";
            } else {
                $long_title = $row['long_title'];
            }   
            if($long_title == ""){
                $long_title = $row['course_no'];
            }
            // get lines (word-aware wrapping)
            $lines = $this->splitTextToLines($long_title, $charWidthMap, $maxPixelWidth, 10 /*font size*/);

            // write each line into its own Excel row, keeping other columns aligned if needed
            $startKey = $key;
            foreach ($lines as $i => $line) {
                $curr_key = $startKey + $i;
                $sheet->setCellValue('C' . ($startKey + $i), $line);
                $sheet->getStyle('C' . ($startKey + $i))->getFont()->setName('Palatino Linotype')->setSize(10);
                // optionally style alignment for these cells
                $sheet->getStyle('C' . ($startKey + $i))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                //search2
                $sheet->getStyle("A$curr_key:E$curr_key")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("B$curr_key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->getStyle("C$curr_key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->getStyle("E$curr_key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("F$curr_key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("F$curr_key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("G$curr_key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 
                $sheet->getStyle("H$curr_key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
            }

            $sheet->setCellValue('A' . $key, $row['course_no']);
            $sheet->getStyle('A' . $key)->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10);

            $grade = $row['grade'];

            if (is_numeric($grade)) {
                $grade = (float)$grade;
                $sheet->setCellValue('F' . $key, $grade);
                // Force Excel to show two decimal places (e.g., 2.00, 2.50)
                $sheet->getStyle('F' . $key)
                    ->getNumberFormat()
                    ->setFormatCode('0.00');
            } else {
                // Keep non-numeric grades (e.g., S, INC)
                $sheet->setCellValue('F' . $key, $grade);
            }
            $removal_grade = $row['removal_grade'];

            if (is_numeric($removal_grade)) {
                $removal_grade = (float)$removal_grade;
                $sheet->setCellValue('G' . $key, $removal_grade);
                $sheet->getStyle('G' . $key)
                ->getNumberFormat()
                ->setFormatCode('0.00');
            }else{
                $sheet->setCellValue('G' . $key, $removal_grade);
            }

            if(empty($removal_grade)){
                if(in_array($grade, ["Drp","Inc.","4.00","5.00"])){
                    $credit = "-";
                }else{
                    $credit = $row['credit'];
                }
            }else{
                if(in_array($removal_grade, ["Drp","Inc.","4.00","5.00"])){
                    $credit = "-";
                }else{
                    $credit = $row['credit'];
                }
            }
            $sheet->setCellValue('H' . $key, $credit);
            $sheet->getStyle('F' . $key.':H'.$key)->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10);
            $sheet->getStyle('F' . $key.':H'.$key)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $prev_student_no = $student_no;
            $prev_term_id = $term_id;
            $prev_college = $college;
            // advance $key to the next empty row after written lines
            $key = $startKey + count($lines);

            $page_number = (int)ceil($key / 64);
            $position_in_page = ($key - 1) % 64 + 1;

            if (
                ($page_number === 1 && $position_in_page >= 54) ||
                ($page_number > 1 && $position_in_page >= 58)
            ) {
                // We're inside the footer area for this page
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                if($page_number == 1){
                    $sheet->mergeCells("A$key:B$key");  
                    $sheet->setCellValue("A$key", 'NSTP Serial No:');
                    $sheet->getStyle("H$key")->getFont()
                    ->setName('Palatino Linotype');
                    $sheet->getStyle("A$key")->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $sheet->getStyle("A$key:H$key")->applyFromArray([
                        'borders' => [
                            'top' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                            'bottom' => [
                                'borderStyle' => Border::BORDER_DOUBLE,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $key++;
                }
                $sheet->mergeCells("A$key:H$key");  
                $next_page = $page_number+1;
                $continue_page_data [] = ["cell"=>"A$key","next_page"=>$next_page];
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("A$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_DOUBLE,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $key++;
                //add remarks for page 1 only
                if($page_number==1){
                    $key_range = $key+2;
                    $sheet->getStyle("A$key:H$key")->applyFromArray([
                        'borders' => [
                            'top' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("B$key:B$key_range")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);

                    $sheet->getStyle("C$key:C$key_range")->applyFromArray([
                        'borders' => [
                            'left' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("A$key:A$key_range")->applyFromArray([
                        'borders' => [
                            'left' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("H$key:H$key_range")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->setCellValue("A$key", 'Remarks:');
                    $sheet->getStyle("A$key")->getFont()
                    ->setName('Palatino Linotype');
                    $sheet->getStyle("A$key")->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);

                    $key+=2;
                    $sheet->getStyle("A$key:H$key")->applyFromArray([
                        'borders' => [
                            'bottom' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $key++;
                }
                //if last course title occupy 2 rows remove 1 row before prepared by for page 1 only
                if($position_in_page > 54 && $page_number == 1){
                    $key++;
                }else{
                    //else add 2 rows before prepared by
                    $key +=2;
                }
                $sheet->setCellValue("A$key", 'Prepared by:');
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype');
                $sheet->setCellValue("D$key", 'Date:');
                $sheet->getStyle("D$key")->getFont()
                ->setName('Palatino Linotype');

                $key++;
                $sheet->setCellValue("A$key", 'Checked by:');
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype');
                $sheet->setCellValue("D$key", 'Date:');
                $sheet->getStyle("D$key")->getFont()
                ->setName('Palatino Linotype');

                $key++;
                $sheet->setCellValue("A$key", 'Issued by:');
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype');
                $sheet->setCellValue("D$key", 'Date:');
                $sheet->getStyle("D$key")->getFont()
                ->setName('Palatino Linotype');

                $sheet->mergeCells("E$key:H$key");  
                $sheet->setCellValue("E$key", 'MARGARITA CARMEN S. PATERNO');
                $sheet->getStyle("E$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);

                $sheet->getStyle("E$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);


                $key++;
                $sheet->mergeCells("E$key:H$key");  
                $sheet->setCellValue("E$key", 'University Registrar');
                $sheet->getStyle("E$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("E$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
                //------------------------add new Header-----------------//
                $sheet->setBreak("A$key", Worksheet::BREAK_ROW);
                $key++;
                $page_num_data[] = ["cell"=>"F$key", "next_page"=>$next_page];
                insertLogo($sheet, "A$key");
                $key++;
                $sheet->setCellValue("C$key", 'OFFICE OF THE UNIVERSITY REGISTRAR');
                $sheet->getStyle("C$key")->getFont()
                ->setName('Palatino Linotype')
                ->setSize(14)
                ->setBold(true);    

                $key++;
                $sheet->setCellValue("C$key", 'UNIVERSITY OF THE PHILIPPINES LOS BAÑOS');
                $sheet->getStyle("C$key")->getFont()
                ->setName('Palatino Linotype')
                ->setSize(14)
                ->setBold(true)
                ->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
                $key++;
                $key++;
                $sheet->mergeCells("A$key:H$key");  
                $sheet->setCellValue("A$key", 'OFFICIAL TRANSCRIPT OF RECORDS');
                $sheet->getStyle("A$key")->getFont()
                    ->setName('Palatino Linotype')
                    ->setSize(12);
                $sheet->getStyle("A$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $key++;
                $sheet->setCellValue("E$key", 'Name:'.$gen_data['fullname']);
                $sheet->getStyle("E$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);

                $key++;
                //Setup to make part of text underline
                $richText = new RichText();
                $richText->createText('Student No. ');
                // use richtext to make part of text underline
                $underlined = $richText->createTextRun($gen_data['student_no']);
                $underlined->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
                $sheet->setCellValue("E$key", $richText);
                $key++;
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("B$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("C$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);      
                $sheet->mergeCells("F$key:G$key");  
                $sheet->setCellValue("F$key", 'Grades');
                $sheet->getStyle("F$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("F$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("F$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("H$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 
                $key++;
                $sheet->mergeCells("A$key:B$key");  
                $sheet->setCellValue("A$key", 'Course Number');
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("A$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 


                $sheet->mergeCells("C$key:E$key");  
                $sheet->setCellValue("C$key", 'Course Title');
                $sheet->getStyle("C$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("C$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("C$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 

                $sheet->getStyle("F$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 

                $sheet->setCellValue("G$key", 'Re-exam');
                $sheet->getStyle("G$key")->getFont()
                ->setName('Palatino Linotype')
                ->setSize(7)
                ->setBold(true);
                $sheet->getStyle("G$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
                $sheet->getStyle("G$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 

                $sheet->getStyle("H$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $key++;
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->getStyle("B$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->getStyle("E$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->setCellValue("F$key", 'Final');
                $sheet->getStyle("F$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("F$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("F$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->setCellValue("G$key", 'Completion');
                $sheet->getStyle("G$key")->getFont()
                ->setName('Palatino Linotype')
                ->setSize(7)
                ->setBold(true);
                $sheet->getStyle("G$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
                $sheet->getStyle("G$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->setCellValue("H$key", 'Credits');
                $sheet->getStyle("H$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("F$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("H$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $key++;
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                // END Header
            }
            //add footer only at the end of loop
            if ($row_key === $lastKey) {
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->mergeCells("A$key:B$key");  
                $sheet->setCellValue("A$key", '');

                $sheet->getStyle("H$key")->getFont()
                ->setName('Palatino Linotype');
                $sheet->getStyle("A$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                $key++;
                $key++;
                $sheet->mergeCells("A$key:H$key");  
                $sheet->setCellValue("A$key", '\'End of Transcript: Cleared: XXXXXX');
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("A$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_DOUBLE,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $key++;
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $key +=2;
                $sheet->setCellValue("A$key", 'Prepared by:');
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype');
                $sheet->setCellValue("D$key", 'Date:');
                $sheet->getStyle("D$key")->getFont()
                ->setName('Palatino Linotype');

                $key++;
                $sheet->setCellValue("A$key", 'Checked by:');
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype');
                $sheet->setCellValue("D$key", 'Date:');
                $sheet->getStyle("D$key")->getFont()
                ->setName('Palatino Linotype');

                $key++;
                $sheet->setCellValue("A$key", 'Issued by:');
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype');
                $sheet->setCellValue("D$key", 'Date:');
                $sheet->getStyle("D$key")->getFont()
                ->setName('Palatino Linotype');

                $sheet->mergeCells("E$key:H$key");  
                $sheet->setCellValue("E$key", 'MARGARITA CARMEN S. PATERNO');
                $sheet->getStyle("E$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);

                $sheet->getStyle("E$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);


                $key++;
                $sheet->mergeCells("E$key:H$key");  
                $sheet->setCellValue("E$key", 'University Registrar');
                $sheet->getStyle("E$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("E$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);

                $key++;
            } 

        }
        $transcript_guide_page = $next_page+1;
        //page numbner
        foreach($page_num_data as $data){
            $sheet->setCellValue($data['cell'], 'Page '.$data['next_page'].' of '.$transcript_guide_page.' pages');
            $sheet->getStyle($data['cell'])->getFont()
            ->setName('Palatino Linotype');
        }
        //continue page cell text
        foreach($continue_page_data as $data){
            $sheet->setCellValue($data['cell'], "Continued on page ".$data['next_page'].". Transcript guide on page $transcript_guide_page.");
            $sheet->getStyle($data['cell'])->getFont()
            ->setName('Palatino Linotype');

        }

          /* Excel File Format */
        $writer = new Xlsx($spreadsheet);
        $filename = 'excel-report';
         ob_end_clean();
        $file_name = $gen_data['fullname']."-".$gen_data['student_no'];
        $file_path = FCPATH . 'assets/phpspreadsheet_files/'.$file_name.'.xlsx';          
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        // echo json_data($file_name);
    }

    public function download_grades(){
        $student_no = $this->input->post('student_no');
        $gen_data = $this->Transcript_import_model->get_student_details($student_no);
        $filePath = FCPATH . 'assets/phpspreadsheet_files/transcript.xls';
        // Load template

        $template = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $templateSheet = $template->getActiveSheet();

        // Create new spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Optional: set small margins
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setBottom(0);
        $sheet->getPageMargins()->setLeft(0);
        $sheet->getPageMargins()->setRight(0);
        //copy column width from template
        foreach (range('A', 'H') as $col) {
            $width = $templateSheet->getColumnDimension($col)->getWidth();
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $totalLineCount = 0; // Initialize total line counter
        $rows = $this->Transcript_import_model->get_student_grades($student_no);
        $prev_term_id = "";
        $prev_college = "";
        $key = 3;


        foreach ($rows as $row) {
            if(TRIM($row['course_no']) == "GI ." || TRIM($row['course_no']) == "GI"){
                continue; // skip this row and go to the next one
            }
            // If new term, print term header row
            if ($row['term_id'] !== $prev_term_id) {
                $totalLineCount++;
                $prev_term_id = $row['term_id'];
            }
            // Print grade data
        
            if (in_array($row['course_no'], ["HK 12", "NSTP 1", "NSTP 2"]) && !empty($row['activity'])) {
                $long_title = $row['long_title'] . " (" . $row['activity'] . ")";
            } else {
                $long_title = $row['long_title'];
            }

            if($long_title == ""){
                $long_title = $row['course_no'];
            }
            // char width map (extend as needed)
            $charWidthMap = [
                'i' => 0.4, 'l' => 0.4, 't' => 0.5,
                'a' => 0.6, 'e' => 0.6, 'n' => 0.6, 'o' => 0.6,
                'm' => 0.8, 'w' => 0.9, 'W' => 1.0, 'M' => 1.0,
                ' ' => 0.4,
                // add more chars if you want more accuracy...
            ];

            $maxPixelWidth = 175; // tweak if needed

            // build long_title as before
            if (in_array($row['course_no'], ["HK 12", "NSTP 1", "NSTP 2"]) && !empty($row['activity'])) {
                $long_title = $row['long_title'] . " (" . $row['activity'] . ")";
            } else {
                $long_title = $row['long_title'];
            }
            if($long_title == ""){
                $long_title = $row['course_no'];
            }

            // get lines (word-aware wrapping)
            $lines = $this->splitTextToLines($long_title, $charWidthMap, $maxPixelWidth, 10 /*font size*/);

            // Add number of lines for this course to total
            $totalLineCount += count($lines);
        }
        /* === 1. Page Setup & Margins === */


        //Setup to make part of text underline
        $richText = new RichText();
        $richText->createText('Name:  ');
        // use richtext to make part of text underline
        $underlined = $richText->createTextRun($gen_data['fullname']);
        $underlined->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10)
            ->setUnderline(Font::UNDERLINE_SINGLE)
            ->setBold(true);  // <-- make it bold
        $sheet->setCellValue('E1', $richText);
        $sheet->getStyle('E1')->getFont()
        ->setName('Palatino Linotype');


        //Setup to make part of text underline
        $richText = new RichText();
        $richText->createText('Student No. ');
        // use richtext to make part of text underline
        $student_no = $gen_data['student_no'];
        $formatted_student_no = substr($student_no, 0, 4) . '-' . substr($student_no, 4);
        $underlined = $richText->createTextRun($formatted_student_no);
        $underlined->getFont()
        ->setName('Palatino Linotype')
        ->setSize(10)
        ->setUnderline(Font::UNDERLINE_SINGLE);
        $sheet->setCellValue('E2', $richText);
        $sheet->getStyle('E2')->getFont()
        ->setName('Palatino Linotype');

        $sheet->mergeCells('A3:H3');  
        $sheet->setCellValue('A3', 'COLLEGIATE RECORDS');
        $sheet->getStyle('A3')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('A3')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:H3')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
        $sheet->getStyle('A4:H4')->applyFromArray([
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
        $sheet->getStyle('B4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
        $sheet->getStyle('C4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);      
        $sheet->mergeCells('F4:G4');  
        $sheet->setCellValue('F4', 'Grades');
        $sheet->getStyle('F4')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('F4')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('F4')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
        $sheet->getStyle('H4')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 

        $sheet->mergeCells('A5:B5');  
        $sheet->setCellValue('A5', 'Course Number');
        $sheet->getStyle('A5')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('A5')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 


        $sheet->mergeCells('C5:E5');  
        $sheet->setCellValue('C5', 'Course Title');
        $sheet->getStyle('C5')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('C5')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 

        $sheet->getStyle('F5')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 

        $sheet->setCellValue('G5', 'Re-exam');
        $sheet->getStyle('G5')->getFont()
        ->setName('Palatino Linotype')
        ->setSize(7)
        ->setBold(true);
        $sheet->getStyle('G5')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
        $sheet->getStyle('G5')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]); 

        $sheet->getStyle('H5')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->getStyle('A6:H6')->applyFromArray([
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->getStyle('B6')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->getStyle('E6')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->setCellValue('F6', 'Final');
        $sheet->getStyle('F6')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('F6')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('F6')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->setCellValue('G6', 'Completion');
        $sheet->getStyle('G6')->getFont()
        ->setName('Palatino Linotype')
        ->setSize(7)
        ->setBold(true);
        $sheet->getStyle('G6')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
        $sheet->getStyle('G6')->applyFromArray([
            'borders' => [
                'right' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        $sheet->setCellValue('H6', 'Credits');
        $sheet->getStyle('H6')->getFont()
        ->setName('Palatino Linotype')
        ->setBold(true);
        $sheet->getStyle('F6')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('H6')->applyFromArray([
            'borders' => [
                'left' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);
        //add DATA
        $rows = $this->Transcript_import_model->get_student_grades($student_no);
        $prev_term_id = "";
        $key = 7;
        $lastKey = array_key_last($rows); // get the last index/key
        $continue_page_data = [];
        $page_num_data = [];
        foreach ($rows as $row_key=>$row) {
            if(TRIM($row['course_no']) == "GI ." || TRIM($row['course_no']) == "GI"){
                continue; // skip this row and go to the next one
            }
            $sheet->getStyle("A$key:E$key")->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B$key")->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);

            $sheet->getStyle("C$key")->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);

            $sheet->getStyle("E$key")->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
            $sheet->getStyle("F$key")->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
            $sheet->getStyle("F$key")->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
            $sheet->getStyle("G$key")->applyFromArray([
                'borders' => [
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]); 
            $sheet->getStyle("H$key")->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK],
                    ],
                ],
            ]);
            // If new college, print college header row
            $college = $row['college'] == "College of Arts and Science"?$row['college']."s":$row['college'];
            if ($college !== $prev_college) {
                $sheet->mergeCells("C$key:E$key");  
                $sheet->setCellValue('C' . $key, $college);
                $sheet->getStyle('C' . $key)
                    ->getFont()
                    ->setName('Palatino Linotype')
                    ->setSize(10)
                    ->setBold(true)
                    ->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);

                // Alignment must be set separately:
                $sheet->getStyle('C' . $key)
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $key++;
            }

            // If new term, print term header row
            $term_id= $row['term_id'];
            if ($term_id !== $prev_term_id) {
                $sheet->setCellValue('A' . $key, convert_term_text($term_id));
                $sheet->getStyle('A' . $key)->getFont()
                    ->setName('Palatino Linotype')
                    ->setSize(10)
                    ->setBold(true)
                    ->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);

                    $sheet->getStyle("B$key")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("E$key")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("F$key")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);
                    $sheet->getStyle("G$key")->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => Color::COLOR_BLACK],
                            ],
                        ],
                    ]);

                $key++;
            }


            if (in_array($row['course_no'], ["HK 12", "NSTP 1", "NSTP 2"]) && !empty($row['activity'])) {
                $long_title = $row['long_title'] . " (" . $row['activity'] . ")";
            } else {
                $long_title = $row['long_title'];
            }
            if($long_title == ""){
                $long_title = $row['course_no'];
            }
            // char width map (extend as needed)
            $charWidthMap = [
                'i' => 0.4, 'l' => 0.4, 't' => 0.5,
                'a' => 0.6, 'e' => 0.6, 'n' => 0.6, 'o' => 0.6,
                'm' => 0.8, 'w' => 0.9, 'W' => 1.0, 'M' => 1.0,
                ' ' => 0.4,
                // add more chars if you want more accuracy...
            ];

            // compute your actual max column pixel width:
            // Example: convert Excel width units to pixels roughly: pixels ≈ widthUnits * 7
            // If you stored column width e.g. 13.42578125:
            $maxPixelWidth = 175; // tweak multiplier if needed

            // build long_title as before
            if (in_array($row['course_no'], ["HK 12", "NSTP 1", "NSTP 2"]) && !empty($row['activity'])) {
                $long_title = $row['long_title'] . " (" . $row['activity'] . ")";
            } else {
                $long_title = $row['long_title'];
            }   
            if($long_title == ""){
                $long_title = $row['course_no'];
            }
            // get lines (word-aware wrapping)
            $lines = $this->splitTextToLines($long_title, $charWidthMap, $maxPixelWidth, 10 /*font size*/);

            // write each line into its own Excel row, keeping other columns aligned if needed
            $startKey = $key;
            foreach ($lines as $i => $line) {
                $curr_key = $startKey + $i;
                $sheet->setCellValue('C' . ($startKey + $i), $line);
                $sheet->getStyle('C' . ($startKey + $i))->getFont()->setName('Palatino Linotype')->setSize(10);
                // optionally style alignment for these cells
                $sheet->getStyle('C' . ($startKey + $i))->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                //search2
                $sheet->getStyle("A$curr_key:E$curr_key")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("B$curr_key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->getStyle("C$curr_key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->getStyle("E$curr_key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("F$curr_key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("F$curr_key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("G$curr_key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 
                $sheet->getStyle("H$curr_key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

            if ($curr_key % 2 == 1) {
                $sheet->getStyle("A$curr_key:H$curr_key")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFEFEFEF'], // Light Gray
                    ],
                ]);
            }

            }

            $sheet->setCellValue('A' . $key, $row['course_no']);
            $sheet->getStyle('A' . $key)->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10);

            $grade = $row['grade'];

            if (is_numeric($grade)) {
                $grade = (float)$grade;
                $sheet->setCellValue('F' . $key, $grade);
                // Force Excel to show two decimal places (e.g., 2.00, 2.50)
                $sheet->getStyle('F' . $key)
                    ->getNumberFormat()
                    ->setFormatCode('0.00');
            } else {
                // Keep non-numeric grades (e.g., S, INC)
                $sheet->setCellValue('F' . $key, $grade);
            }
            $sheet->setCellValue('G' . $key, $row['removal_grade']);
            if(empty($row['removal_grade'])){
                if(in_array($grade, ["Drp","Inc.","4.00","5.00"])){
                    $credit = "-";
                }else{
                    $credit = $row['credit'];
                }
            }else{
                if(in_array($row['removal_grade'], ["Drp","Inc.","4.00","5.00"])){
                    $credit = "-";
                }else{
                    $credit = $row['credit'];
                }
            }
            $sheet->setCellValue('H' . $key, $credit);
            $sheet->getStyle('F' . $key.':H'.$key)->getFont()
            ->setName('Palatino Linotype')
            ->setSize(10);
            $sheet->getStyle('F' . $key.':H'.$key)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $prev_student_no = $student_no;
            $prev_term_id = $term_id;
            $prev_college = $college;
            // advance $key to the next empty row after written lines
            $key = $startKey + count($lines);

            $page_number = (int)ceil($key / 50);
            $position_in_page = ($key - 1) % 50 + 1;
            if($position_in_page >= 50){
                $sheet->setBreak("A$key", Worksheet::BREAK_ROW);

                $key++;
                $sheet->setCellValue("E$key", 'Name:'.$gen_data['fullname']);
                $sheet->getStyle("E$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);

                $key++;
                //Setup to make part of text underline
                $richText = new RichText();
                $richText->createText('Student No. ');
                // use richtext to make part of text underline
                $underlined = $richText->createTextRun($gen_data['student_no']);
                $underlined->getFont()->setUnderline(Font::UNDERLINE_SINGLE);
                $sheet->setCellValue("E$key", $richText);
                $key++;
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("B$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("C$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);      
                $sheet->mergeCells("F$key:G$key");  
                $sheet->setCellValue("F$key", 'Grades');
                $sheet->getStyle("F$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("F$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("F$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->getStyle("H$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 
                $key++;
                $sheet->mergeCells("A$key:B$key");  
                $sheet->setCellValue("A$key", 'Course Number');
                $sheet->getStyle("A$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("A$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 


                $sheet->mergeCells("C$key:E$key");  
                $sheet->setCellValue("C$key", 'Course Title');
                $sheet->getStyle("C$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("C$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("C$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 

                $sheet->getStyle("F$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 

                $sheet->setCellValue("G$key", 'Re-exam');
                $sheet->getStyle("G$key")->getFont()
                ->setName('Palatino Linotype')
                ->setSize(7)
                ->setBold(true);
                $sheet->getStyle("G$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
                $sheet->getStyle("G$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]); 

                $sheet->getStyle("H$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $key++;
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->getStyle("B$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->getStyle("E$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->setCellValue("F$key", 'Final');
                $sheet->getStyle("F$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("F$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("F$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->setCellValue("G$key", 'Completion');
                $sheet->getStyle("G$key")->getFont()
                ->setName('Palatino Linotype')
                ->setSize(7)
                ->setBold(true);
                $sheet->getStyle("G$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
                $sheet->getStyle("G$key")->applyFromArray([
                    'borders' => [
                        'right' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);

                $sheet->setCellValue("H$key", 'Credits');
                $sheet->getStyle("H$key")->getFont()
                ->setName('Palatino Linotype')
                ->setBold(true);
                $sheet->getStyle("F$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("H$key")->applyFromArray([
                    'borders' => [
                        'left' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                        'bottom' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $key++;
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
            }

            if ($row_key === $lastKey) {
                $sheet->getStyle("A$key:H$key")->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                $sheet->mergeCells("A$key:B$key");  
                $sheet->setCellValue("A$key", '');

                $sheet->getStyle("H$key")->getFont()
                ->setName('Palatino Linotype');
                $sheet->getStyle("A$key")->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

            } 
        }


          /* Excel File Format */
        $writer = new Xlsx($spreadsheet);
        $filename = 'excel-report';
         ob_end_clean();
        $file_name = $gen_data['fullname']."-".$gen_data['student_no'];
        $file_path = FCPATH . 'assets/phpspreadsheet_files/'.$file_name.'.xlsx';          
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        // echo json_data($file_name);
    }

    // inside class Transcript_export extends MY_Controller { ... }

    private function estimateTextWidth(string $text, array $charWidthMap, float $defaultWidth = 0.6, float $fontSize = 10): float
    {
        // approximate pixels per "width unit" (tweak if you want)
        $pixelPerUnit = 0.55 * $fontSize;

        // split into Unicode characters
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);

        $units = 0.0;
        foreach ($chars as $ch) {
            // prefer exact map key, otherwise lowercase fallback, otherwise default
            if (isset($charWidthMap[$ch])) {
                $factor = $charWidthMap[$ch];
            } else {
                $low = mb_strtolower($ch);
                $factor = $charWidthMap[$low] ?? $defaultWidth;
            }
            $units += (float) $factor;
        }

        return $units * $pixelPerUnit; // return estimated pixel width
    }

    private function splitTextToLines(string $text, array $charWidthMap, float $maxPixelWidth, float $fontSize = 10, float $defaultWidth = 0.6): array
    {
        // split on whitespace into words (keeps punctuation inside words)
        $words = preg_split('/\s+/u', trim($text));
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $test = ($current === '') ? $word : ($current . ' ' . $word);
            $testWidth = $this->estimateTextWidth($test, $charWidthMap, $defaultWidth, $fontSize);

            if ($testWidth > $maxPixelWidth) {
                if ($current === '') {
                    // single word longer than max — break the word by characters
                    $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
                    $part = '';
                    foreach ($chars as $ch) {
                        $try = $part . $ch;
                        $tryWidth = $this->estimateTextWidth($try, $charWidthMap, $defaultWidth, $fontSize);
                        if ($tryWidth > $maxPixelWidth) {
                            if ($part !== '') {
                                $lines[] = $part;
                            }
                            $part = $ch; // start new fragment
                        } else {
                            $part .= $ch;
                        }
                    }
                    if ($part !== '') {
                        // after breaking the long word, start a new current line with nothing
                        $lines[] = $part;
                        $current = '';
                    }
                } else {
                    // push current line and start new one with the word
                    $lines[] = $current;
                    $current = $word;
                }
            } else {
                $current = $test;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }


    public function readColumnWidths()
    {
        // Path to your template Excel file
        $filePath = FCPATH . 'assets/phpspreadsheet_files/transcript.xls';

        // Load the spreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Columns to read
        $columns = range('A', 'H');
        $widths = [];

        foreach ($columns as $col) {
            $widths[$col] = $sheet->getColumnDimension($col)->getWidth();
        }

         var_dump($widths);
    }

    public function clone() {
        $filePath = FCPATH . 'assets/phpspreadsheet_files/transcript.xls';

        // Load template
        $template = IOFactory::load($filePath);
        $templateSheet = $template->getActiveSheet();

        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ---- 1. Copy column widths ----
        foreach ($templateSheet->getColumnDimensions() as $col => $dimension) {
            $sheet->getColumnDimension($col)->setWidth($dimension->getWidth());
        }

        // ---- 2. Copy row heights ----
        foreach ($templateSheet->getRowDimensions() as $row => $dimension) {
            echo $row.":".$dimension->getRowHeight()."<br>";
            $sheet->getRowDimension($row)->setRowHeight($dimension->getRowHeight());
        }
        exit();
        // ---- 3. Copy merged cells ----
        foreach ($templateSheet->getMergeCells() as $mergedRange) {
            $sheet->mergeCells($mergedRange);
        }

        // ---- 4. Copy styles and values ----
        $highestRow = $templateSheet->getHighestRow();
        $highestCol = $templateSheet->getHighestColumn();
        $range = 'A1:' . $highestCol . $highestRow;

        // Copy styles
        $sheet->duplicateStyle($templateSheet->getStyle($range), $range);

        // Copy values (if needed)
        $sheet->fromArray(
            $templateSheet->rangeToArray($range, null, true, true, true),
            null,
            'A1'
        );

        // ---- 5. Copy page setup (margins, orientation, paper size) ----
        $sheet->getPageSetup()->setOrientation($templateSheet->getPageSetup()->getOrientation());
        $sheet->getPageSetup()->setPaperSize($templateSheet->getPageSetup()->getPaperSize());

        $sheet->getPageMargins()->setTop($templateSheet->getPageMargins()->getTop());
        $sheet->getPageMargins()->setBottom($templateSheet->getPageMargins()->getBottom());
        $sheet->getPageMargins()->setLeft($templateSheet->getPageMargins()->getLeft());
        $sheet->getPageMargins()->setRight($templateSheet->getPageMargins()->getRight());

        // ---- 6. Now insert your data ----
        $sheet->setCellValue('A10', 'Student ID 001');
        $sheet->setCellValue('B10', 'Juan Dela Cruz');
        $sheet->setCellValue('C10', 'BS Computer Science');

        // ---- 7. Output as new Excel ----
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = "cloned_template.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save('php://output');
        exit;
    }

    public function show_details(){
    $filePath = FCPATH . 'assets/phpspreadsheet_files/transcript.xls';

    // Load template
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();

    echo "=== PAGE SETUP ===\n";
    $setup = $sheet->getPageSetup();
    echo "Orientation: " . $setup->getOrientation() . "\n";
    echo "Paper Size: " . $setup->getPaperSize() . "\n";
    echo "Scale: " . $setup->getScale() . "\n";
    echo "Fit to Page: " . ($setup->getFitToPage() ? 'Yes' : 'No') . "\n";
    echo "Fit Width: " . $setup->getFitToWidth() . "\n";
    echo "Fit Height: " . $setup->getFitToHeight() . "\n";

    echo "\n=== PAGE MARGINS (in inches) ===\n";
    $margins = $sheet->getPageMargins();
    echo "Top: " . $margins->getTop() . "\n";
    echo "Bottom: " . $margins->getBottom() . "\n";
    echo "Left: " . $margins->getLeft() . "\n";
    echo "Right: " . $margins->getRight() . "\n";
    echo "Header: " . $margins->getHeader() . "\n";
    echo "Footer: " . $margins->getFooter() . "\n";

    echo "\n=== DEFAULT STYLES ===\n";
    $defaultStyle = $spreadsheet->getDefaultStyle()->getFont();
    echo "Default Font Name: " . $defaultStyle->getName() . "\n";
    echo "Default Font Size: " . $defaultStyle->getSize() . "\n";
    echo "Bold: " . ($defaultStyle->getBold() ? 'Yes' : 'No') . "\n";
    echo "Italic: " . ($defaultStyle->getItalic() ? 'Yes' : 'No') . "\n";
    echo "Underline: " . $defaultStyle->getUnderline() . "\n";
    echo "Color: " . $defaultStyle->getColor()->getRGB() . "\n";

    echo "\n=== COLUMN WIDTHS A–H ===\n";
    foreach (range('A','H') as $col) {
        echo $col . " : " . $sheet->getColumnDimension($col)->getWidth() . "\n";
    }

    echo "\n=== ROW HEIGHTS 1–10 ===\n";
    for ($i = 1; $i <= 10; $i++) {
        echo "Row $i : " . $sheet->getRowDimension($i)->getRowHeight() . "\n";
    }

    echo "\n=== SAMPLE STYLES (A1:H5) ===\n";
    foreach (range('A','H') as $col) {
        for ($row = 1; $row <= 5; $row++) {
            $coord = $col . $row;
            $style = $sheet->getStyle($coord)->getFont();
            echo "$coord -> Font: " . $style->getName() .
                 ", Size: " . $style->getSize() .
                 ", Bold: " . ($style->getBold() ? 'Yes' : 'No') .
                 ", Italic: " . ($style->getItalic() ? 'Yes' : 'No') .
                 ", Underline: " . $style->getUnderline() .
                 "\n";
        }
    }

    exit;
    }


    public function download_batch($batch_no = 1) {
        $batch_no = (int)$batch_no;
        if ($batch_no < 1) $batch_no = 1;

        $limit = 50000; // adjust batch size here
        $offset = ($batch_no - 1) * $limit;

        // count total records
        $total_rows = $this->db->count_all('transcript_grades');
        if ($offset >= $total_rows) {
            show_error("Batch number out of range. Total rows: $total_rows");
            return;
        }

        // fetch batch data
        $query = $this->db
            ->limit($limit, $offset)
            ->get('transcript_grades');

        $rows = $query->result_array();

        if (empty($rows)) {
            show_error("No data found for batch $batch_no");
            return;
        }

        // convert to CSV
        $csv_data = $this->array_to_csv($rows);

        $filename = "transcript_grades_batch_{$batch_no}.csv";
        force_download($filename, $csv_data);
    }

    // helper function to convert array to CSV string
    private function array_to_csv($array) {
        if (empty($array)) return '';

        $f = fopen('php://temp', 'w');
        fputcsv($f, array_keys($array[0])); // header

        foreach ($array as $row) {
            fputcsv($f, $row);
        }

        rewind($f);
        $csv = stream_get_contents($f);
        fclose($f);
        return $csv;
    }

    // optional: to show total batches available
    public function info() {
        $limit = 50000;
        $total_rows = $this->db->count_all('transcript_grades');
        $total_batches = ceil($total_rows / $limit);

        echo "Total rows: $total_rows<br>";
        echo "Batch size: $limit<br>";
        echo "Total batches: $total_batches<br><br>";

        for ($i = 1; $i <= $total_batches; $i++) {
            echo "<a href='" . site_url("transcript_backup/download_batch/$i") . "'>Download batch $i</a><br>";
        }
    }

}
