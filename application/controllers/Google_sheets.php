<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Google\Client;
//to make it work with new spreedsheet share the spreadsheet to our-service-account@subtle-proxy-503107-v1.iam.gserviceaccount.com

class Google_sheets extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('cookie');	
		$this->isLogin();
	}
	public function index(){
		$client = new Google_Client();
        $client->setClientId('90118768418-4rjtl20bnr1ijaa8fqhc0ah1ft529qjs.apps.googleusercontent.com');
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri('http://localhost/our_is/Google_sheets');
        $client->setAccessType('offline');
		$client->setPrompt('consent');
		$client->addScope(Google_Service_Sheets::SPREADSHEETS);
		$client->addScope(Google_Service_Drive::DRIVE);
		$client->setIncludeGrantedScopes(true);
		
		$file = (FCPATH)."assets/token/google_sheets_transcript.json";

		if (!isset($_GET['code'])) {
		    $authUrl = $client->createAuthUrl();
		    echo "<h1>To authorize this application click <a href='$authUrl'>HERE</a></h1>";
		} else {
		    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
		    $client->setAccessToken($token);

		    // Save the token to a file
			if(!file_exists($file)){
				fopen($file, 'w');
			    file_put_contents($file, json_encode($client->getAccessToken(),JSON_PRETTY_PRINT));
			}else{
			    file_put_contents($file, json_encode($client->getAccessToken(),JSON_PRETTY_PRINT));
			}
		    echo "Authentication successful. Token saved.";
		     echo "<script>window.close();</script>";
		}
	}
	
public function update_data()
{
    /**
     * Generate the local claiming/reference code.
     */
    function generate_trans_code($length = 6)
    {
        $bytes = random_bytes($length * 2);

        $string = substr(
            str_replace(['+', '/', '='], '', base64_encode($bytes)),
            0,
            $length
        );

        return $string;
    }

    /**
     * Convert a Google Sheet row into an associative array using headers.
     */
    function make_assoc_row($headers, $row)
    {
        $row = array_pad($row, count($headers), '');

        $result = [];

        foreach ($headers as $index => $header) {
            $result[$header] = isset($row[$index])
                ? $row[$index]
                : '';
        }

        return $result;
    }

    /**
     * Read a whole sheet and return:
     *
     * [
     *     'headers' => [...],
     *     'rows'    => [
     *         ['RequestID' => ..., ...],
     *         ...
     *     ]
     * ]
     */
    function read_relational_sheet($service, $spreadsheetId, $sheetName)
    {
        $range = $sheetName . '!A:ZZ';

        $response = $service->spreadsheets_values->get(
            $spreadsheetId,
            $range
        );

        $values = $response->getValues();

        if (empty($values)) {
            return [
                'headers' => [],
                'rows' => []
            ];
        }

        $headers = array_map(
            'trim',
            $values[0]
        );

        $rows = [];

        for ($i = 1; $i < count($values); $i++) {

            // Ignore completely empty rows.
            $hasData = false;

            foreach ($values[$i] as $cell) {
                if (trim((string) $cell) !== '') {
                    $hasData = true;
                    break;
                }
            }

            if (!$hasData) {
                continue;
            }

            $rows[] = make_assoc_row(
                $headers,
                $values[$i]
            );
        }

        return [
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    /**
     * Get a trimmed value from an associative array.
     */
    function row_value($row, $key, $default = '')
    {
        if (!isset($row[$key])) {
            return $default;
        }

        return trim((string) $row[$key]);
    }

    /**
     * Convert a value to a boolean/integer flag.
     */
    function yes_no_flag($value)
    {
        $value = strtolower(trim((string) $value));

        return in_array(
            $value,
            ['yes', 'true', '1', 'agree'],
            true
        ) ? 1 : 0;
    }

    /**
     * Convert a date value into MySQL YYYY-MM-DD.
     */
    function parse_mysql_date($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // Already YYYY-MM-DD.
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        $timestamp = strtotime($value);

        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d', $timestamp);
    }

    /**
     * Parse semester information stored as JSON.
     *
     * Expected structure:
     *
     * {
     *     "semester": "1st Semester",
     *     "academic_year": "2025-2026"
     * }
     */
	function parse_semester_json($value)
	{
	    $result = [
	        'term' => '',
	        'schoolYear' => '',
	        'year' => ''
	    ];

	    if (trim((string) $value) === '') {
	        return $result;
	    }

	    $decoded = json_decode($value, true);

	    if (!is_array($decoded)) {
	        return $result;
	    }

	    $result['term'] =
	        isset($decoded['term'])
	            ? trim((string) $decoded['term'])
	            : '';

	    $result['schoolYear'] =
	        isset($decoded['schoolYear'])
	            ? trim((string) $decoded['schoolYear'])
	            : '';

	    $result['year'] =
	        isset($decoded['year'])
	            ? trim((string) $decoded['year'])
	            : '';

	    return $result;
	}

    /**
     * Parse school_history JSON.
     */
    function parse_school_history($value)
    {
        if (trim((string) $value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded)
            ? $decoded
            : [];
    }

    /**
     * Parse school_history JSON.
     */
    function parse_cross_registration($value)
    {
        if (trim((string) $value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded)
            ? $decoded
            : [];
    }

    $this->load->model('General_model', '', TRUE);
    $this->load->model('Google_sheets_model', '', TRUE);


    /*
     * ---------------------------------------------------------
     * GOOGLE CLIENT
     * ---------------------------------------------------------
     */

    $client = new Google_Client();

    $client->setApplicationName('Our IS');

    $client->setAuthConfig(
        (FCPATH) . 'assets/credentials/service-account.json'
    );

    $client->setScopes([
        Google_Service_Sheets::SPREADSHEETS,
        Google_Service_Drive::DRIVE
    ]);

    $service = new Google_Service_Sheets($client);

    $json_data = new stdClass();

    $json_data->with_data = false;
    $json_data->error = 0;


    /*
     * ---------------------------------------------------------
     * GET SYNC CONFIGURATION
     * ---------------------------------------------------------
     */

    $sheets_data = $this->General_model->list_dynamic(
        "tr_details_sheets_data",
        [
            "status" => 0,
            "type" => 1
        ]
    );

    if (empty($sheets_data)) {

        $json_data->error = 'Sync configuration not found.';

        echo json_encode($json_data);
        return;
    }


    $sync = $sheets_data[0];

    /*
     * IMPORTANT:
     *
     * last_rownum now refers to the ROW NUMBER
     * in the Requests sheet.
     *
     * Header = row 1
     * First request = row 2
     */

    $last_rownum = (int) $sync->last_rownum;

    $last_assigned_to = $sync->last_assigned_to;

    $spreadsheetId = $sync->spreadsheetId;

    /*
     * tab_name should now contain the Requests sheet name.
     */
    $requests_sheet_name = $sync->tab_name;


    /*
     * These should preferably be stored in the
     * tr_details_sheets_data table.
     *
     * If not available, change these to the actual
     * Google Sheet tab names.
     */

    $request_items_sheet_name =
        !empty($sync->request_items_tab_name)
            ? $sync->request_items_tab_name
            : 'RequestItems';

    $request_information_sheet_name =
        !empty($sync->request_information_tab_name)
            ? $sync->request_information_tab_name
            : 'RequestInformation';

    $attachments_sheet_name =
        !empty($sync->attachments_tab_name)
            ? $sync->attachments_tab_name
            : 'Attachments';


    /*
     * ---------------------------------------------------------
     * READ THE FOUR RELATIONAL SHEETS
     * ---------------------------------------------------------
     */

    try {

        $requests_sheet = read_relational_sheet(
            $service,
            $spreadsheetId,
            $requests_sheet_name
        );

        $request_items_sheet = read_relational_sheet(
            $service,
            $spreadsheetId,
            $request_items_sheet_name
        );

        $request_information_sheet = read_relational_sheet(
            $service,
            $spreadsheetId,
            $request_information_sheet_name
        );

        $attachments_sheet = read_relational_sheet(
            $service,
            $spreadsheetId,
            $attachments_sheet_name
        );

    } catch (\Throwable $e) {

        $json_data->error = $e->getMessage();

        echo json_encode($json_data);
        return;
    }


    /*
     * ---------------------------------------------------------
     * VALIDATE REQUIRED HEADERS
     * ---------------------------------------------------------
     */

    $required_request_headers = [
        'RequestID',
        'StudentID',
        'EmailAddressSnapshot',
        'LastNameSnapshot',
        'FirstNameSnapshot',
        'MiddleNameSnapshot',
        'SuffixSnapshot',
        'StudentNumberSnapshot',
        'OTRCopyType',
        'CreatedAt',
        'Status',
        'StatusUpdatedAt'
    ];

    $required_item_headers = [
        'RequestItemID',
        'RequestID',
        'Category',
        'RequestTypeCode',
        'RequestType',
        'Quantity',
        'CreatedAt'
    ];

    $required_information_headers = [
        'RequestInformationID',
        'RequestID',
        'FieldCode',
        'FieldLabel',
        'ValueType',
        'Value',
        'CreatedAt',
        'UpdatedAt'
    ];

    $required_attachment_headers = [
        'AttachmentID',
        'RequestID',
        'RequestItemID',
        'AttachmentType',
        'AttachmentLabel',
        'FileName',
        'MimeType',
        'FileSizeBytes',
        'DriveFileID',
        'UploadedAt',
        'Status'
    ];


    foreach ($required_request_headers as $header) {

        if (!in_array($header, $requests_sheet['headers'], true)) {

            $json_data->error =
                'Missing Requests header: ' . $header;

            echo json_encode($json_data);
            return;
        }
    }


    foreach ($required_item_headers as $header) {

        if (!in_array($header, $request_items_sheet['headers'], true)) {

            $json_data->error =
                'Missing RequestItems header: ' . $header;

            echo json_encode($json_data);
            return;
        }
    }


    foreach ($required_information_headers as $header) {

        if (!in_array($header, $request_information_sheet['headers'], true)) {

            $json_data->error =
                'Missing RequestInformation header: ' . $header;

            echo json_encode($json_data);
            return;
        }
    }


    foreach ($required_attachment_headers as $header) {

        if (!in_array($header, $attachments_sheet['headers'], true)) {

            $json_data->error =
                'Missing Attachments header: ' . $header;

            echo json_encode($json_data);
            return;
        }
    }


    /*
     * ---------------------------------------------------------
     * BUILD LOOKUP ARRAYS
     * ---------------------------------------------------------
     */

    $items_by_request = [];

    foreach ($request_items_sheet['rows'] as $item) {

        $request_id = row_value(
            $item,
            'RequestID'
        );

        if ($request_id === '') {
            continue;
        }

        if (!isset($items_by_request[$request_id])) {
            $items_by_request[$request_id] = [];
        }

        $items_by_request[$request_id][] = $item;
    }


    $information_by_request = [];

    foreach ($request_information_sheet['rows'] as $information) {

        $request_id = row_value(
            $information,
            'RequestID'
        );

        if ($request_id === '') {
            continue;
        }

        if (!isset($information_by_request[$request_id])) {
            $information_by_request[$request_id] = [];
        }

        $information_by_request[$request_id][] = $information;
    }


    $attachments_by_request = [];

    foreach ($attachments_sheet['rows'] as $attachment) {

        $request_id = row_value(
            $attachment,
            'RequestID'
        );

        if ($request_id === '') {
            continue;
        }

        if (!isset($attachments_by_request[$request_id])) {
            $attachments_by_request[$request_id] = [];
        }

        $attachments_by_request[$request_id][] = $attachment;
    }


    /*
     * ---------------------------------------------------------
     * CONVERT REQUEST INFORMATION INTO:
     *
     * $information[RequestID][FieldCode] = Value
     * ---------------------------------------------------------
     */

    $information_values = [];

    foreach ($request_information_sheet['rows'] as $information) {

        $request_id = row_value(
            $information,
            'RequestID'
        );

        $field_code = row_value(
            $information,
            'FieldCode'
        );

        if ($request_id === '' || $field_code === '') {
            continue;
        }

        if (!isset($information_values[$request_id])) {
            $information_values[$request_id] = [];
        }

        $information_values[$request_id][$field_code] =
            row_value(
                $information,
                'Value'
            );
    }


    /*
     * ---------------------------------------------------------
     * GET ACTIVE TRANSCRIPT USERS
     * ---------------------------------------------------------
     */

    $transcript_users =
        $this->Google_sheets_model->transcript_users();


    /*
     * ---------------------------------------------------------
     * DETERMINE WHICH REQUESTS TO PROCESS
     *
     * Requests sheet:
     *
     * Row 1 = header
     * Row 2 = first request
     *
     * $last_rownum is the last processed Requests row.
     * ---------------------------------------------------------
     */

    $requests_to_process = [];

    foreach ($requests_sheet['rows'] as $index => $request) {

        /*
         * Because $rows starts after the header,
         * actual Google Sheet row is index + 2.
         */
        $sheet_row_number = $index + 2;

        if ($sheet_row_number <= $last_rownum) {
            continue;
        }

        $request['__sheet_row_number'] =
            $sheet_row_number;

        $requests_to_process[] = $request;
    }


    if (empty($requests_to_process)) {

        $json_data->with_data = false;
        $json_data->message = 'No new requests found.';

        echo json_encode($json_data);
        return;
    }


    $json_data->with_data = true;


    /*
     * ---------------------------------------------------------
     * CHECK EXISTING REQUEST IDs
     *
     * The UNIQUE KEY:
     *
     * uq_tr_details_request_id
     *
     * is the final DB protection.
     *
     * We also check before insertion so a previously synced
     * RequestID does not create another local transaction.
     * ---------------------------------------------------------
     */

    $request_ids = [];

    foreach ($requests_to_process as $request) {

        $request_id =
            row_value($request, 'RequestID');

        if ($request_id !== '') {
            $request_ids[] = $request_id;
        }
    }

    $existing_request_ids = [];

    if (!empty($request_ids)) {

        $existing_rows = $this->db
            ->select('request_id')
            ->where_in('request_id', $request_ids)
            ->get('tr_details')
            ->result_array();

        foreach ($existing_rows as $existing) {

            if (!empty($existing['request_id'])) {

                $existing_request_ids[
                    $existing['request_id']
                ] = true;
            }
        }
    }


    /*
     * ---------------------------------------------------------
     * ARRAYS TO INSERT
     * ---------------------------------------------------------
     */

    $data = [];

    $data_schools = [];

    $quantity = [];

    $tr_purpose = [];

    $tr_school_email = [];

    $tr_uploads = [];

    $tr_others_title = [];


    /*
     * Keep track of requests which are actually new.
     */
    $processed_request_count = 0;

    $skipped_duplicate_count = 0;

    $unmapped_item_codes = [];


    /*
     * ---------------------------------------------------------
     * REQUEST TYPE MAP
     * ---------------------------------------------------------
     *
     * These are request types that map directly to a single
     * local rt_id.
     *
     * For OTR:
     *   first_copy / recopy are NOT RequestItems values.
     *   They come from Requests.OTRCopyType.
     *
     * Special-case purposes are handled separately below because
     * they expand through transcript_request_type.parent_id.
     */

    $request_type_map = [
	    'non_issuance_of_gpa'              => 7,
	    'english_as_medium_of_instruction' => 5,
	    'certificate_of_attendance'        => 26,
	    'grade_equivalency_certificate'   => 6,
        // OTR CTC
        'ctc' =>
            3,


        // Certifications
        'certificate_of_graduation' =>
            4,

        'gpa' =>
            7,

        'course_description' =>
            33,

        'special_order' =>
            8,

        'certified_text_of_diploma' =>
            9,

        'certificate_of_enrollment' =>
            11,

        'ctc_of_diploma' =>
            12,

        'english_translation_of_diploma' =>
            13
    ];


    /*
     * ---------------------------------------------------------
     * SPECIAL-CASE REQUEST TYPES
     * ---------------------------------------------------------
     *
     * The RequestTypeCode identifies the special-case purpose.
     * The value below is the parent rt_id in
     * transcript_request_type.
     *
     * PHP does NOT insert the parent itself.
     * It retrieves the parent's children and inserts those
     * child rt_ids into tr_quantity.
     *
     * This is important because these special cases already
     * define their own OTR/document requirements.
     */

    $special_case_parent_map = [

        'wes' =>
            15,

        'icas' =>
            22,

        'ncees' =>
            23,

        'cgfns' =>
            24,

        'cav_dfa' =>
            14,

        'cav_promotion' =>
            27,

        'other_school_outside_ph' =>
            35
    ];

    /*
     * ---------------------------------------------------------
     * PROCESS EACH REQUEST
     * ---------------------------------------------------------
     */

/* Load once before processing requests. */
if (!isset($this->requestvaluelibrary)) {
    $this->load->library('RequestValueLibrary');
}

    foreach ($requests_to_process as $request) {

        $request_id =
            row_value($request, 'RequestID');


        /*
         * Skip completely invalid request rows.
         */
        if ($request_id === '') {
            continue;
        }


        /*
         * Skip if already imported.
         */
        if (isset($existing_request_ids[$request_id])) {

            $skipped_duplicate_count++;

            continue;
        }


        /*
         * -----------------------------------------------------
         * REQUEST BASIC INFORMATION
         * -----------------------------------------------------
         */

        $created_at =
            row_value($request, 'CreatedAt');

        if ($created_at !== '') {

            $timestamp =
                date(
                    'Y-m-d H:i:s',
                    strtotime($created_at)
                );

        } else {

            $timestamp =
                date('Y-m-d H:i:s');
        }


        $email =
            row_value(
                $request,
                'EmailAddressSnapshot'
            );

        $fname =
            row_value(
                $request,
                'FirstNameSnapshot'
            );

        $mname =
            row_value(
                $request,
                'MiddleNameSnapshot'
            );

        $lname =
            row_value(
                $request,
                'LastNameSnapshot'
            );

        $suffix =
            row_value(
                $request,
                'SuffixSnapshot'
            );

        $student_no =
            row_value(
                $request,
                'StudentNumberSnapshot'
            );


        /*
         * -----------------------------------------------------
         * REQUEST INFORMATION
         * -----------------------------------------------------
         */

        $information =
            isset($information_values[$request_id])
                ? $information_values[$request_id]
                : [];


        /*
         * Helper for information fields.
         */
        $info_value = function ($field_code) use ($information) {

            return isset($information[$field_code])
                ? trim((string) $information[$field_code])
                : '';
        };


        $sex =
            $info_value('gender');


        $birthday =
            parse_mysql_date(
                $info_value('date_of_birth')
            );


        $pob =
            $info_value('place_of_birth');


        $fathers_name =
            $info_value('fathers_name');


        $mothers_name =
            $info_value('mothers_maiden_name');
        /*
         * -----------------------------------------------------
         * CONTROLLED VALUES / DISPLAY LABELS
         * -----------------------------------------------------
         */

        $value_library =
            $this->requestvaluelibrary;


        /*
         * First semester enrolled.
         *
         * Example:
         * {"term":"first_semester","schoolYear":"2021-2022"}
         */
        $first_semester =
            parse_semester_json(
                $info_value('first_semester_enrolled')
            );

        $sem_admitted =
            $value_library->label(
                'semester',
                $first_semester['term']
            );

        $ay_admitted =
            $first_semester['term'] === 'midyear'
                ? $first_semester['year']
                : $first_semester['schoolYear'];


        /*
         * Last semester enrolled.
         *
         * Example:
         * {"term":"midyear","year":"2025"}
         */
        $last_semester =
            parse_semester_json(
                $info_value('last_semester_enrolled')
            );

        $sem_last_enrolled =
            $value_library->label(
                'semester',
                $last_semester['term']
            );

        $ay_last_enrolled =
            $last_semester['term'] === 'midyear'
                ? $last_semester['year']
                : $last_semester['schoolYear'];


        $admitted_as =
            $value_library->label(
                'admitted_as',
                $info_value('admitted_as')
            );


        $degree =
            $info_value('degree_program');


        $major_specialization =
            $info_value('major_specialization');


        $student_status =
        	$value_library->label(
                'status',
                $info_value('student_status')
            );


        $school_attended_after_uplb =
            $info_value(
                'school_attended_after_uplb'
            );


        $date_of_graduation =
            parse_mysql_date(
                $info_value('date_of_graduation')
            );


        /*
         * -----------------------------------------------------
         * LATIN HONORS
         *
         * If "Other" has a value, use that value.
         *
         * Example:
         *
         * latin_honors       = Other
         * latin_honors_other = Cum Laude
         *
         * local DB:
         *
         * latin_honors = Cum Laude
         * -----------------------------------------------------
         */

		$latin_honors = $info_value('latin_honors');
        $latin_honors_other = $info_value('latin_honors_other');

        if (
            strtolower(trim((string) $latin_honors)) === 'other'
            &&
            $latin_honors_other !== ''
        ) {
            $latin_honors = $latin_honors_other;
        }


        /*
         * cross_registration
         *
         * Google Sheet value may be:
         *
         * Yes / No
         */
        $is_cross_reg =
            yes_no_flag(
                $info_value('cross_registration')
            );



        /*
         * DPA:
         *
         * true / false
         * Yes / No
         * Agree
         */
        $is_dpa =
            yes_no_flag(
                $info_value('dpa_agreement')
            );


        /*
         * -----------------------------------------------------
         * ASSIGN LOCAL TRANSCRIPT USER
         * -----------------------------------------------------
         */

        if (count($transcript_users) === 1) {

            $assigned_to =
                $transcript_users[0];

        } elseif (count($transcript_users) > 1) {

            $index =
                array_search(
                    $last_assigned_to,
                    $transcript_users,
                    true
                );

            if ($index === false) {

                $assigned_to =
                    $transcript_users[0];

            } else {

                $nextIndex =
                    ($index + 1)
                    % count($transcript_users);

                $assigned_to =
                    $transcript_users[$nextIndex];
            }

        } else {

            /*
             * No active transcript users.
             *
             * Keep NULL/empty rather than inventing an assignee.
             */
            $assigned_to = null;
        }


        $last_assigned_to =
            $assigned_to;


        /*
         * -----------------------------------------------------
         * GENERATE LOCAL TRANS CODE
         * -----------------------------------------------------
         */

        $sheet_row_number =
            $request['__sheet_row_number'];

        $trans_code =
            generate_trans_code()
            . $sheet_row_number;


        /*
         * -----------------------------------------------------
         * BUILD tr_details
         * -----------------------------------------------------
         *
         * IMPORTANT:
         *
         * RequestID is NOT the trans_code.
         *
         * RequestID = stable Google integration ID
         * trans_code = local claiming/reference code
         */

        $data[] = [

            'rownum' =>
                $sheet_row_number,

            'timestamp' =>
                $timestamp,

            'email' =>
                $email,

            'fname' =>
                $fname,

            'mname' =>
                $mname,

            'lname' =>
                $lname,

            'suffix' =>
                $suffix,

            'sex' =>
                $sex,

            'student_no' =>
                $student_no,

            'birthday' =>
                $birthday,

            'pob' =>
                $pob,

            'fathers_name' =>
                $fathers_name,

            'mothers_name' =>
                $mothers_name,

            'sem_admitted' =>
                $sem_admitted,

            'ay_admitted' =>
                $ay_admitted,

            'admitted_as' =>
                $admitted_as,

            'sem_last_enrolled' =>
                $sem_last_enrolled,

            'ay_last_enrolled' =>
                $ay_last_enrolled,

            'degree' =>
                $degree,

            'major_specialization' =>
                $major_specialization,

            'school_attended_after_uplb' =>
                $school_attended_after_uplb,

            'date_of_graduation' =>
                $date_of_graduation,

            'latin_honors' =>
                $latin_honors,

            'student_status' =>
                $student_status,

            'is_cross_reg' =>
                $is_cross_reg,

            'assigned_to' =>
                $assigned_to,

            'trans_code' =>
                $trans_code,

            'is_dpa' =>
                $is_dpa,

            /*
             * New stable Google RequestID.
             */
            'request_id' =>
                $request_id,

            /*
             * Keep the old local defaults for now.
             *
             * We are NOT inventing a mapping from the new
             * string statuses to the old integer statuses yet.
             */
            'status' =>
                0,

            'active_status' =>
                0
        ];

/*
 * -----------------------------------------------------
 * SCHOOL HISTORY
 * -----------------------------------------------------
 *
 * RequestInformation:
 *
 * school_history = JSON array
 *
 * Actual Google Sheets structure:
 *
 * [
 *   {
 *     "level": "Associate",
 *     "name": "Trace",
 *     "location": "LB",
 *     "course": "IT1",
 *     "dateGraduated": "2026-09-30"
 *   },
 *   {
 *     "level": "MS",
 *     "name": "Trace 2",
 *     "location": "LB 2",
 *     "course": "IT 2"
 *   }
 * ]
 *
 * Local type:
 *
 * Associate = 0
 * College   = 1
 * MS        = 2
 * PhD       = 3
 */

$school_history =
    parse_school_history(
        $info_value('school_history')
    );


foreach ($school_history as $school) {

    if (!is_array($school)) {
        continue;
    }


    /*
     * Google Sheets uses "level".
     */
    $school_type =
        isset($school['level'])
            ? trim((string) $school['level'])
            : '';


    $local_type =
        $value_library->schoolLevelType($school_type);

    if ($local_type === null) {
        continue;
    }


    /*
     * Google Sheets uses "dateGraduated".
     *
     * If no graduation date exists,
     * store an empty value.
     */
    $date_graduated =
        isset($school['dateGraduated'])
            ? trim((string) $school['dateGraduated'])
            : '';


    $data_schools[] = [

        'name' =>
            isset($school['name'])
                ? trim((string) $school['name'])
                : '',

        'location' =>
            isset($school['location'])
                ? trim((string) $school['location'])
                : '',

        'course' =>
            isset($school['course'])
                ? trim((string) $school['course'])
                : '',

        'year_month_graduated' =>
            $date_graduated,

        'type' =>
            $local_type,

        'trans_code' =>
            $trans_code
    ];
}

$cross_registration =
    parse_cross_registration(
        $info_value('cross_registration_entries')
    );


foreach ($cross_registration as $cross_reg) {

    if (!is_array($cross_reg)) {
        continue;
    }


    /*
     * Google Sheets uses "level".
     */
    $campus_unit =
        isset($cross_reg['campusUnit'])
            ? trim((string) $cross_reg['campusUnit'])
            : '';

    $semester =
        isset($cross_reg['semester'])
            ? trim((string) $cross_reg['semester'])
            : '';

    $year_enrolled =
        isset($cross_reg['yearEnrolled'])
            ? trim((string) $cross_reg['yearEnrolled'])
            : '';


    $data_cross_reg[] = [

        'trans_code' => $trans_code,
        'campus_unit' => $campus_unit,
        'semester' => $semester,
        'year_enrolled' => $year_enrolled,
        'campus_unit' => $campus_unit,
    ];
}
        /*
         * -----------------------------------------------------
         * REQUEST ITEMS
         * -----------------------------------------------------
         *
         * New RequestItems schema:
         *
         * RequestItemID
         * RequestID
         * Category
         * RequestTypeCode
         * RequestType
         * Quantity
         * CreatedAt
         *
         * OTR rules:
         *
         * 1. Requests.OTRCopyType determines the base OTR
         *    rt_id for REGULAR purposes:
         *
         *      first_copy -> rt_id 1
         *      recopy     -> rt_id 2
         *
         * 2. A regular purpose creates an OTR tr_quantity row
         *    using that base rt_id and stores its RequestType
         *    in tr_purpose.
         *
         * 3. A special-case purpose does NOT add rt_id 1 or 2.
         *    Instead, its special-case parent is used to retrieve
         *    all child request types from transcript_request_type.
         *
         * 4. CTC is a standalone OTR request:
         *
         *      ctc -> rt_id 3
         *
         *    and does not create a tr_purpose row.
         *
         * Certification items map directly to their local rt_id
         * and do not create tr_purpose rows.
         */

        $request_items =
            isset($items_by_request[$request_id])
                ? $items_by_request[$request_id]
                : [];


        /*
         * OTRCopyType belongs to Requests, not RequestItems.
         */
        $otr_copy_type =
            strtolower(
                row_value(
                    $request,
                    'OTRCopyType'
                )
            );


        $regular_otr_rt_id = null;

        if ($otr_copy_type === 'first_copy') {
            $regular_otr_rt_id = 1;
        } elseif ($otr_copy_type === 'recopy') {
            $regular_otr_rt_id = 2;
        }


        foreach ($request_items as $item) {

            $category =
                strtolower(
                    row_value(
                        $item,
                        'Category'
                    )
                );


            $request_type_code =
                row_value(
                    $item,
                    'RequestTypeCode'
                );


            $request_type =
                row_value(
                    $item,
                    'RequestType'
                );


            $quantity_value =
                row_value(
                    $item,
                    'Quantity'
                );


            /*
             * Skip items without quantity.
             */
            if ($quantity_value === '') {
                continue;
            }


            /*
             * -------------------------------------------------
             * OTR
             * -------------------------------------------------
             */
            if ($category === 'otr') {

                /*
                 * CTC is a standalone OTR request.
                 *
                 * It has no purpose and maps directly to rt_id 3.
                 */
                if ($request_type_code === 'ctc') {

                    $quantity[] = [

                        'rt_id' =>
                            3,

                        'quantity' =>
                            $quantity_value,

                        'trans_code' =>
                            $trans_code,

                        'is_for_visa' =>
                            0
                    ];

                    continue;
                }


                /*
                 * -------------------------------------------------
                 * SPECIAL CASE
                 * -------------------------------------------------
                 *
                 * Do NOT add rt_id 1 or 2 for a special case.
                 *
                 * Instead, retrieve all child request types under
                 * the special-case parent.
                 */
                if (
                    isset(
                        $special_case_parent_map[
                            $request_type_code
                        ]
                    )
                ) {

                    $special_parent_id =
                        $special_case_parent_map[
                            $request_type_code
                        ];


                    $special_case_set =
                        $this->General_model->list_dynamic(
                            'transcript_request_type',
                            [
                                'parent_id' =>
                                    $special_parent_id
                            ]
                        );


                    if (empty($special_case_set)) {

                        $unmapped_item_codes[] = [

                            'request_id' =>
                                $request_id,

                            'item_code' =>
                                $request_type_code,

                            'item_label' =>
                                $request_type
                        ];

                        /*
                         * Do not create a partial special-case
                         * request when its child request types
                         * are not configured in the local DB.
                         */
                        continue;
                    }


                    foreach ($special_case_set as $val) {

                        $quantity[] = [

                            'rt_id' =>
                                $val->id,

                            'quantity' =>
                                $quantity_value,

                            'trans_code' =>
                                $trans_code,

                            'is_for_visa' =>
                                0
                        ];
                    }


                    /*
                     * The special-case purpose is still a real
                     * purpose selected by the student.
                     */
                    if ($request_type !== '') {

                        $tr_purpose[] = [

                            'purpose' =>
                                $request_type,

                            'trans_code' =>
                                $trans_code
                        ];
                    }

                    continue;
                }


                /*
                 * -------------------------------------------------
                 * REGULAR OTR PURPOSE
                 * -------------------------------------------------
                 *
                 * A regular purpose follows OTRCopyType:
                 *
                 *   first_copy -> rt_id 1
                 *   recopy     -> rt_id 2
                 */
                if ($regular_otr_rt_id === null) {

                    $unmapped_item_codes[] = [

                        'request_id' =>
                            $request_id,

                        'item_code' =>
                            $request_type_code,

                        'item_label' =>
                            $request_type
                    ];

                    continue;
                }


                $quantity[] = [

                    'rt_id' =>
                        $regular_otr_rt_id,

                    'quantity' =>
                        $quantity_value,

                    'trans_code' =>
                        $trans_code,

                    'is_for_visa' =>
                        0
                ];


                /*
                 * Store the selected OTR purpose.
                 */
                if ($request_type !== '') {

                    $tr_purpose[] = [

                        'purpose' =>
                            $request_type,

                        'trans_code' =>
                            $trans_code
                    ];
                }

                continue;
            }


            /*
             * -------------------------------------------------
             * CERTIFICATION
             * -------------------------------------------------
             *
             * Certification RequestTypeCode maps directly to
             * its local rt_id.
             */
            if ($category === 'certification') {

                if (
                    !isset(
                        $request_type_map[
                            $request_type_code
                        ]
                    )
                ) {

                    $unmapped_item_codes[] = [

                        'request_id' =>
                            $request_id,

                        'item_code' =>
                            $request_type_code,

                        'item_label' =>
                            $request_type
                    ];

                    continue;
                }


                $quantity[] = [

                    'rt_id' =>
                        $request_type_map[
                            $request_type_code
                        ],

                    'quantity' =>
                        $quantity_value,

                    'trans_code' =>
                        $trans_code,

                    'is_for_visa' =>
                        0
                ];

                continue;
            }


            /*
             * -------------------------------------------------
             * UNKNOWN CATEGORY
             * -------------------------------------------------
             */

            $unmapped_item_codes[] = [

                'request_id' =>
                    $request_id,

                'item_code' =>
                    $request_type_code,

                'item_label' =>
                    $request_type
            ];
        }


        /*
         * -----------------------------------------------------
         * FOREIGN SCHOOL EMAIL
         * -----------------------------------------------------
         *
         * If the relational sheet contains this field,
         * preserve it in tr_school_email.
         *
         * The current finalized information-field list does
         * not yet show foreign_school_email, so this simply
         * remains empty until that field exists.
         */

        $foreign_school_email =
            $info_value(
                'foreign_school_email'
            );


        if ($foreign_school_email !== '') {

            $tr_school_email[] = [

                'email' =>
                    $foreign_school_email,

                'trans_code' =>
                    $trans_code
            ];
        }


        /*
         * -----------------------------------------------------
         * ATTACHMENTS
         * -----------------------------------------------------
         *
         * The local table has:
         *
         * tr_uploads.link
         * tr_uploads.type
         *
         * The new relational sheet has:
         *
         * DriveFileID
         * AttachmentType
         * AttachmentLabel
         * FileName
         *
         * For now DriveFileID is stored in link.
         */

        $request_attachments =
            isset($attachments_by_request[$request_id])
                ? $attachments_by_request[$request_id]
                : [];


        foreach ($request_attachments as $attachment) {

            $drive_file_id =
                row_value(
                    $attachment,
                    'DriveFileID'
                );


            if ($drive_file_id === '') {
                continue;
            }


            $attachment_type =
            	$value_library->label(
	                'uploads',
	                row_value(
	                    $attachment,
	                    'AttachmentType'
	                )
	            );



            $attachment_label =
            	$value_library->label(
	                'uploads',
	                row_value(
	                    $attachment,
	                    'AttachmentLabel'
	                )
	            );



            /*
             * Prefer AttachmentType.
             * If empty, use AttachmentLabel.
             */
            $upload_type =
                $attachment_type !== ''
                    ? $attachment_type
                    : $attachment_label;


            $tr_uploads[] = [

                'link' =>
                    $drive_file_id,

                'trans_code' =>
                    $trans_code,

                'type' =>
                    $upload_type
            ];
        }


        /*
         * -----------------------------------------------------
         * INCREMENT COUNTER
         * -----------------------------------------------------
         */

        $processed_request_count++;
    }


    /*
     * ---------------------------------------------------------
     * NOTHING NEW AFTER DUPLICATE CHECK
     * ---------------------------------------------------------
     */

    if (empty($data)) {

        /*
         * Even if everything was a duplicate, move the
         * checkpoint forward because those Requests already
         * exist locally.
         */

        $last_request =
            end($requests_to_process);

        $last_rownum_inserted =
            $last_request['__sheet_row_number'];


        $this->db->trans_start();

        $this->General_model->update_where(
            [
                'last_rownum' =>
                    $last_rownum_inserted,

                'last_assigned_to' =>
                    $last_assigned_to,

                'last_update' =>
                    date('Y-m-d H:i:s')
            ],
            [
                'status' =>
                    0,

                'type' =>
                    1
            ],
            'tr_details_sheets_data'
        );

        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE) {

            $json_data->error =
                'Failed to update sync checkpoint.';

        } else {

            $json_data->error = 0;

            $json_data->message =
                'No new requests to import.';

            $json_data->skipped_duplicates =
                $skipped_duplicate_count;
        }


        echo json_encode($json_data);
        return;
    }



    /*
     * ---------------------------------------------------------
     * LAST REQUEST ROW PROCESSED
     * ---------------------------------------------------------
     */

    $last_request =
        end($requests_to_process);

    $last_rownum_inserted =
        $last_request['__sheet_row_number'];


    /*
     * ---------------------------------------------------------
     * DATABASE TRANSACTION
     * ---------------------------------------------------------
     */

    try {

        $this->db->trans_start();


        /*
         * tr_details
         */
        if (!empty($data)) {

            $this->General_model->insert_batch(
                'tr_details',
                $data
            );
        }


        /*
         * tr_quantity
         */
        if (!empty($quantity)) {

            $this->General_model->insert_batch(
                'tr_quantity',
                $quantity
            );
        }


        /*
         * tr_schools_attended
         */
        if (!empty($data_schools)) {

            $this->General_model->insert_batch(
                'tr_schools_attended',
                $data_schools
            );
        }

        /*
         * tr_cross_registration
         */
        if (!empty($data_cross_reg)) {

            $this->General_model->insert_batch(
                'tr_cross_registration',
                $data_cross_reg
            );
        }


        /*
         * tr_uploads
         */
        if (!empty($tr_uploads)) {

            $this->General_model->insert_batch(
                'tr_uploads',
                $tr_uploads
            );
        }


        /*
         * tr_school_email
         */
        if (!empty($tr_school_email)) {

            $this->General_model->insert_batch(
                'tr_school_email',
                $tr_school_email
            );
        }


        /*
         * tr_purpose
         */
        if (!empty($tr_purpose)) {

            $this->General_model->insert_batch(
                'tr_purpose',
                $tr_purpose
            );
        }


        /*
         * tr_others_title
         *
         * Currently nothing from the finalized relational
         * structure is being mapped here.
         *
         * Keep this block for future "Other" request types.
         */
        if (!empty($tr_others_title)) {

            $this->General_model->insert_batch(
                'tr_others_title',
                $tr_others_title
            );
        }


        /*
         * -----------------------------------------------------
         * UPDATE GOOGLE SHEETS CHECKPOINT
         * -----------------------------------------------------
         *
         * This is intentionally inside the same transaction.
         *
         * If any DB insert fails:
         *
         * - DB changes rollback
         * - last_rownum remains unchanged
         *
         * Therefore the next sync can safely retry.
         */

        $this->General_model->update_where(
            [
                'last_rownum' =>
                    $last_rownum_inserted,

                'last_assigned_to' =>
                    $last_assigned_to,

                'last_update' =>
                    date('Y-m-d H:i:s')
            ],
            [
                'status' =>
                    0,

                'type' =>
                    1
            ],
            'tr_details_sheets_data'
        );


        $this->db->trans_complete();


        /*
         * -----------------------------------------------------
         * TRANSACTION RESULT
         * -----------------------------------------------------
         */

        if ($this->db->trans_status() === FALSE) {

            $json_data->error = 1;

            $json_data->message =
                'Database synchronization failed. Transaction rolled back.';

        } else {

            $json_data->error = 0;

            $json_data->message =
                'Requests synchronized successfully.';

            $json_data->processed =
                $processed_request_count;

            $json_data->skipped_duplicates =
                $skipped_duplicate_count;

            $json_data->last_rownum =
                $last_rownum_inserted;


            /*
             * This is useful during testing.
             *
             * Example:
             *
             * course_description
             *
             * will appear here until its local rt_id is
             * confirmed.
             */

            $json_data->unmapped_item_codes =
                array_values(
                    array_unique(
                        array_map(
                            function ($item) {

                                return $item['item_code'];

                            },
                            $unmapped_item_codes
                        )
                    )
                );
        }


    } catch (\Throwable $e) {

        /*
         * CI transaction handling normally handles rollback,
         * but explicitly rollback for safety.
         */

        $this->db->trans_rollback();

        $json_data->error =
            $e->getMessage();

        $json_data->trace =
            $e->getTraceAsString();
    }


    /*
     * ---------------------------------------------------------
     * RETURN RESULT
     * ---------------------------------------------------------
     */

    echo json_encode($json_data);
}

	public function update_payments() {
		$this->load->model('General_model', '', TRUE);
		$this->load->model('Google_sheets_model', '', TRUE);
		$client = new Google_Client();

		$client->setApplicationName('Our IS');

		$client->setAuthConfig(
		    (FCPATH) . 'assets/credentials/service-account.json'
		);

		$client->setScopes([
		    Google_Service_Sheets::SPREADSHEETS,
		    Google_Service_Drive::DRIVE
		]);

		$service = new Google_Service_Sheets($client);
		$json_data = new stdClass();

    	$sheets_data = $this->General_model->list_dynamic("tr_details_sheets_data",["status"=>0,"type"=>2]);
		$last_rownum = $sheets_data[0]->last_rownum;
		$spreadsheetId = $sheets_data[0]->spreadsheetId;
		$tab_name = $sheets_data[0]->tab_name;
		$end_col = $sheets_data[0]->end_col;
		$start_row = $last_rownum+1;
		$range = "$tab_name!A$start_row:$end_col";//entire sheet		
		try {
		    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
		    $values = $response->getValues();

		    if (empty($values)) {
		    	$json_data->with_data = false; 
		    } else {
		    	$json_data->with_data = true; 
                $filteredValues = [];
	        	$data = [];
	        	$not_found=0;
	            foreach ($values as $key => $row) {//iterate to filtered values
	            	$last_rownum_inserted = $start_row+$key;
	            	$timestamp = date("Y-m-d H:i:s",strtotime($row[0]));
	            	$email = trim($row[1]);
	            	$trans_code = preg_replace('/\s+/', '', $row[2]);
	            	$refnum = trim($row[3]);
	            	$file_link = $row[4];
            		$update = $this->General_model->update_where(['status'=>3],['trans_code'=>$trans_code, "status"=>2],'tr_details');
	                $data[] = ["rownum"=>$last_rownum_inserted,
	                			"timestamp"=>$timestamp,
	                			"email"=>$email,
	                			"trans_code"=>$trans_code,
	                			"refnum"=>$refnum,
	                			"file_link"=>$file_link,
	                			"is_found"=>$update
	                		];
            		if(!$update){
            			$not_found++;
            		}
		    	}

	            $json_data->not_found = $not_found;
				$this->db->trans_start(); // Start transaction
	            $this->General_model->insert_batch("tr_payments",$data);//insert batch
		        //update last inserted sheets data
		        $this->General_model->update_where(["last_rownum"=>$last_rownum_inserted,"last_update"=>date("Y-m-d H:i:s")],["status"=>0,"type"=>2],"tr_details_sheets_data");
				if ($this->db->trans_status() === FALSE) {
		            // If there is an error, rollback and return false
		            $this->db->trans_rollback();
		            $json_data->error = 1;
		        } else {
		            // Commit the transaction if everything is okay
		            $this->db->trans_commit();
		            $json_data->error = 0;
		        }			    
			}
		}  catch (\Throwable $e) {
		    $json_data->error = $e->getMessage();
		    $json_data->trace = $e->getTraceAsString();
		}
        echo json_encode($json_data);
	}


	public function id_requests()
	{
		$this->load->model('General_model', '', TRUE);
		$this->load->model('Google_sheets_model', '', TRUE);

		$json_data = new stdClass();
		$json_data->with_data = false;
		$json_data->error = 0;
		$json_data->not_found = 0;
		$json_data->inserted = 0;
		$json_data->updated = 0;
		$json_data->skipped = 0;

		$transaction_started = false;

		try {

			// ==========================================
			// GOOGLE SHEETS CONFIGURATION
			// ==========================================

			$client = new Google_Client();
			$client->setApplicationName('Our IS');

			$client->setAuthConfig(
				FCPATH . 'assets/credentials/service-account.json'
			);

			$client->setScopes([
				Google_Service_Sheets::SPREADSHEETS,
				Google_Service_Drive::DRIVE
			]);

			$service = new Google_Service_Sheets($client);

			$sheets_data = $this->General_model->list_dynamic(
				"tr_details_sheets_data",
				[
					"status" => 0,
					"type"   => 3
				]
			);

			if (empty($sheets_data)) {
				throw new RuntimeException(
					'Google Sheets tracking configuration was not found.'
				);
			}

			$sheet_config = $sheets_data[0];

			$last_rownum = (int) $sheet_config->last_rownum;
			$spreadsheetId = $sheet_config->spreadsheetId;
			$tab_name = $sheet_config->tab_name;
			$end_col = $sheet_config->end_col;

			$start_row = $last_rownum + 1;

			$range = $tab_name . '!A' . $start_row . ':' . $end_col;

			// ==========================================
			// FETCH GOOGLE SHEETS DATA
			// ==========================================

			$response = $service->spreadsheets_values->get(
				$spreadsheetId,
				$range
			);

			$values = $response->getValues();

			if (empty($values)) {
				$json_data->message = 'No new rows found.';
				echo json_encode($json_data);
				return;
			}

			$json_data->with_data = true;

			// Last row fetched, regardless of whether individual
			// records will be inserted, updated, or skipped.
			$last_rownum_processed =
				$start_row + count($values) - 1;

			$data = [];

			// Prevent duplicate five-part tokens within the same
			// Google Sheets batch before insert_batch() executes.
			$pending_tokens = [];

			// ==========================================
			// START DATABASE TRANSACTION
			// ==========================================

			$this->db->trans_begin();
			$transaction_started = true;

			// ==========================================
			// PROCESS EACH GOOGLE SHEETS ROW
			// ==========================================

			foreach ($values as $key => $row) {

				$sheet_row_number = $start_row + $key;

				// --------------------------------------
				// VALIDATION STATUS
				// Column P = index 15
				// --------------------------------------

				$validation_status = strtoupper(
					trim((string) ($row[15] ?? ''))
				);

				if ($validation_status !== 'GOOD') {
					$json_data->skipped++;
					continue;
				}

				// --------------------------------------
				// REQUIRED REQUEST TYPE
				// Column J = index 9
				// --------------------------------------

				$request_type_text = trim(
					(string) ($row[9] ?? '')
				);

				// ======================================
				// DETERMINE REQUEST TYPE
				// ======================================

				switch ($request_type_text) {

					case "NEW: New first year undergraduate":
						$request_type = 1;
						$status = 1;
						$with_trans_code = true;
						break;

					case "NEW: Transferee":
						$request_type = 2;
						$status = 1;
						$with_trans_code = true;
						break;

					case "Second degree":
						$request_type = 3;
						$status = 0;
						$with_trans_code = false;
						break;

					case "Non-degree":
						$request_type = 4;
						$status = 0;
						$with_trans_code = false;
						break;

					case "NEW: New Graduate Student":
						$request_type = 5;
						$status = 1;
						$with_trans_code = true;
						break;

					case "Exchange student":
						$request_type = 6;
						$status = 0;
						$with_trans_code = false;
						break;

					case "REPLACEMENT: Lost ID":
						$request_type = 7;
						$status = 0;
						$with_trans_code = false;
						break;

					case "REPLACEMENT: Damaged ID":
						$request_type = 8;
						$status = 0;
						$with_trans_code = false;
						break;

					case "REPLACEMENT: Change information":
						$request_type = 9;
						$status = 0;
						$with_trans_code = false;
						break;

					case "REPLACEMENT: shifted to another degree program":
						$request_type = 10;
						$status = 0;
						$with_trans_code = false;
						break;

					case "Continuing undergraduate: First Request":
						$request_type = 11;
						$status = 0;
						$with_trans_code = false;
						break;

					case "Continuing Graduate Student: First Request":
						$request_type = 12;
						$status = 0;
						$with_trans_code = false;
						break;

					default:
						$json_data->not_found++;
						$json_data->skipped++;
						log_message(
							'error',
							"Unknown request type at Google Sheets row "
							. $sheet_row_number
							. ": " . $request_type_text
						);
						continue 2;
				}

				// ======================================
				// PREPARE DATA
				// ======================================

				$submitted_timestamp = strtotime(
					trim((string) ($row[0] ?? ''))
				);

				$birthday_timestamp = strtotime(
					trim((string) ($row[7] ?? ''))
				);

				if (
					$submitted_timestamp === false ||
					$birthday_timestamp === false
				) {
					$json_data->not_found++;
					$json_data->skipped++;

					log_message(
						'error',
						"Invalid submission date or birthday at "
						. "Google Sheets row " . $sheet_row_number
					);

					continue;
				}

				$date_submitted = date(
					'Y-m-d H:i:s',
					$submitted_timestamp
				);

				$birthday = date(
					'Y-m-d',
					$birthday_timestamp
				);

				$token = trim(
					(string) ($row[10] ?? '')
				);

				$student_number = trim(
					(string) ($row[2] ?? '')
				);

				if ($student_number === '') {
					$json_data->not_found++;
					$json_data->skipped++;
					continue;
				}

				$common_data = [
					"email"            => $row[1] ?? '',
					"student_number"   => $student_number,
					"fname"            => $row[3] ?? '',
					"mname"            => $row[4] ?? '',
					"lname"            => $row[5] ?? '',
					"college"          => $row[6] ?? '',
					"birthday"         => $birthday,
					"address"          => $row[8] ?? '',
					"request_type"     => $request_type,
					"proof_of_payment" => $row[11] ?? '',
					"form5"            => $row[12] ?? '',
					"id_pic"            => $row[13] ?? '',
					"signature"         => $row[14] ?? '',
					"status"            => $status,
					"first_year_form5" => $row[18] ?? '',
					"current_form5"    => $row[19] ?? '',
				];
					$json_data->common_data = $common_data;

				// ======================================
				// NEW STUDENT: TRANSACTION CODE REQUIRED
				// ======================================

				if ($with_trans_code === true) {

					$parts = explode('-', $token);

					// ----------------------------------
					// FIVE-PART TOKEN: INSERT IF UNIQUE
					// ----------------------------------

					if (count($parts) === 5) {

						$common_data["transaction_id"] = $token;
						$common_data["date_submitted"] =
							$date_submitted;

						// Identify duplicate by:
						// student_number + transaction_id.
						$existing = $this->db
							->where(
								'student_number',
								$student_number
							)
							->where('transaction_id', $token)
							->where('is_deleted', 0)
							->limit(1)
							->get('id_requests')
							->row();

						// Also check rows already queued for
						// insert in this same batch.
						$pending_key = hash(
							'sha256',
							$student_number . "\0" . $token
						);

						if (
							$existing ||
							isset($pending_tokens[$pending_key])
						) {
							$json_data->skipped++;
							continue;
						}

						$pending_tokens[$pending_key] = true;

						$data[] = $common_data;

					// ----------------------------------
					// SIX-PART TOKEN: UPDATE STATUS 4
					// ----------------------------------

					} elseif (count($parts) === 6) {

						// Remove the final rejection number.
						$original_token = implode(
							'-',
							array_slice($parts, 0, 5)
						);

						// Only update an active status-4 record.
						// If no matching status-4 record exists,
						// skip without inserting anything.
						$existing = $this->db
							->where(
								'student_number',
								$student_number
							)
							->where(
								'transaction_id',
								$original_token
							)
							->where('status', 4)
							->where('is_deleted', 0)
							->order_by('id', 'DESC')
							->limit(1)
							->get('id_requests')
							->row();

						if (!$existing) {
							$json_data->skipped++;
							continue;
						}

						$common_data["transaction_id"] =
							$original_token;

						$common_data["date_submitted"] =
							$date_submitted;

						$updated = $this->db
							->where('id', $existing->id)
							->where('status', 4)
							->where('is_deleted', 0)
							->update(
								'id_requests',
								$common_data
							);

						if ($updated === false) {
							throw new RuntimeException(
								'Failed to update transaction token '
								. 'for Google Sheets row '
								. $sheet_row_number
							);
						}

						$json_data->updated++;

					} else {

						// Invalid token structure.
						$json_data->not_found++;
						$json_data->skipped++;

						log_message(
							'error',
							"Invalid transaction token at Google "
							. "Sheets row " . $sheet_row_number
						);

						continue;
					}

				// ======================================
				// OLD STUDENT: NO TRANSACTION CODE
				// ======================================

				} else {

					// Old-student requests must have a blank token.
					if ($token !== '') {
						$json_data->not_found++;
						$json_data->skipped++;
						continue;
					}

					// Find an active status-4 request for this
					// student. Other statuses do not prevent
					// a new request from being inserted.
					$existing_request = $this->db
						->where(
							'student_number',
							$student_number
						)
						->where('status', 4)
						->where('is_deleted', 0)
						->order_by('id', 'DESC')
						->limit(1)
						->get('id_requests')
						->row();

					$common_data["transaction_id"] = "";
					$common_data["date_submitted"] =
						$date_submitted;

					if ($existing_request) {

						// Update the existing status-4 request.
						$updated = $this->db
							->where('id', $existing_request->id)
							->where('status', 4)
							->where('is_deleted', 0)
							->update(
								'id_requests',
								$common_data
							);

						if ($updated === false) {
							throw new RuntimeException(
								'Failed to update old-student request '
								. 'at Google Sheets row '
								. $sheet_row_number
							);
						}

						$json_data->updated++;

					} else {

						// No active status-4 request:
						// insert a new request, even if other
						// non-4 records exist for this student.
						$data[] = $common_data;
					}
				}
			}

			// ==========================================
			// INSERT NEW REQUESTS
			// ==========================================

			if (!empty($data)) {

				$inserted = $this->db->insert_batch(
					'id_requests',
					$data
				);

				if ($inserted === false) {
					throw new RuntimeException(
						'Failed to insert new ID requests.'
					);
				}

				$json_data->inserted = count($data);
			}

			// ==========================================
			// UPDATE GOOGLE SHEETS TRACKING
			// ==========================================

			// Advance to the last fetched row, including rows
			// skipped by validation or duplicate checks.
			$tracking_updated = $this->db
				->where('status', 0)
				->where('type', 3)
				->update(
					'tr_details_sheets_data',
					[
						"last_rownum" => $last_rownum_processed,
						"last_update" => date('Y-m-d H:i:s')
					]
				);

			if ($tracking_updated === false) {
				throw new RuntimeException(
					'Failed to update Google Sheets tracking.'
				);
			}

			// ==========================================
			// COMMIT TRANSACTION
			// ==========================================

			if ($this->db->trans_status() === false) {
				throw new RuntimeException(
					'Database transaction failed.'
				);
			}

			$this->db->trans_commit();
			$transaction_started = false;

			$json_data->error = 0;
			$json_data->last_rownum = $last_rownum_processed;
			$json_data->message = 'Google Sheets import completed.';

		} catch (\Throwable $e) {

			if ($transaction_started) {
				$this->db->trans_rollback();
				$transaction_started = false;
			}

			$json_data->error = 1;
			$json_data->message = $e->getMessage();

			log_message(
				'error',
				'id_requests import failed: ' . $e->getMessage()
			);
		}

		echo json_encode($json_data);
	}

	public function id_requests_recheck($mode = 'audit')
	{
	    $this->load->model('General_model', '', TRUE);
	    $this->load->model('Google_sheets_model', '', TRUE);

	    // Audit is the safe default. Only repair changes the database.
	    $mode = strtolower($mode);

	    if (!in_array($mode, ['audit', 'repair'], true)) {
	        show_error('Invalid mode. Use audit or repair.', 400);
	        return;
	    }

	    $repair = ($mode === 'repair');

	    $result = [
	        'success' => false,
	        'mode' => $mode,
	        'message' => '',
	        'summary' => [
	            'rows_scanned' => 0,
	            'valid_rows' => 0,
	            'would_insert' => 0,
	            'inserted' => 0,
	            'would_update' => 0,
	            'updated' => 0,
	            'skipped' => 0,
	            'invalid' => 0,
	            'errors' => 0
	        ],
	        'rows' => []
	    ];

	    $transaction_started = false;

	    try {
	        /*
	         * 1. LOAD GOOGLE SHEETS CONFIGURATION
	         */
	        $sheet_data = $this->General_model->list_dynamic(
	            'tr_details_sheets_data',
	            [
	                'status' => 0,
	                'type' => 3
	            ]
	        );

	        if (empty($sheet_data)) {
	            throw new Exception(
	                'No Google Sheets configuration was found.'
	            );
	        }

	        $config = $sheet_data[0];

	        $spreadsheetId = $config->spreadsheetId;
	        $tab_name = $config->tab_name;
	        $end_col = $config->end_col;

	        /*
	         * 2. CONNECT TO GOOGLE SHEETS
	         */
	        $client = new Google_Client();
	        $client->setApplicationName('Our IS');

	        $client->setAuthConfig(
	            FCPATH . 'assets/credentials/service-account.json'
	        );

	        $client->setScopes([
	            Google_Service_Sheets::SPREADSHEETS,
	            Google_Service_Drive::DRIVE
	        ]);

	        $service = new Google_Service_Sheets($client);

	        /*
	         * 3. READ THE ENTIRE SHEET
	         *
	         * Row 1 contains headers. Data starts at row 2.
	         * The normal incremental checkpoint is not used.
	         */
	        $escaped_tab = str_replace("'", "''", $tab_name);
	        $range = "'" . $escaped_tab . "'!A2:" . $end_col;

	        $response = $service->spreadsheets_values->get(
	            $spreadsheetId,
	            $range
	        );

	        $values = $response->getValues();

	        if (empty($values)) {
	            $result['success'] = true;
	            $result['message'] = 'No data rows were found in the sheet.';

	            header('Content-Type: application/json; charset=utf-8');
	            echo json_encode($result);
	            return;
	        }

	        /*
	         * 4. REQUEST TYPE MAPPING
	         */
	        $request_types = [
	            'NEW: New first year undergraduate' => [
	                'request_type' => 1,
	                'status' => 1,
	                'with_trans_code' => true
	            ],
	            'NEW: Transferee' => [
	                'request_type' => 2,
	                'status' => 1,
	                'with_trans_code' => true
	            ],
	            'Second degree' => [
	                'request_type' => 3,
	                'status' => 0,
	                'with_trans_code' => false
	            ],
	            'Non-degree' => [
	                'request_type' => 4,
	                'status' => 0,
	                'with_trans_code' => false
	            ],
	            'NEW: New Graduate Student' => [
	                'request_type' => 5,
	                'status' => 1,
	                'with_trans_code' => true
	            ],
	            'Exchange student' => [
	                'request_type' => 6,
	                'status' => 0,
	                'with_trans_code' => false
	            ],
	            'REPLACEMENT: Lost ID' => [
	                'request_type' => 7,
	                'status' => 0,
	                'with_trans_code' => false
	            ],
	            'REPLACEMENT: Damaged ID' => [
	                'request_type' => 8,
	                'status' => 0,
	                'with_trans_code' => false
	            ],
	            'REPLACEMENT: Change information' => [
	                'request_type' => 9,
	                'status' => 0,
	                'with_trans_code' => false
	            ],
	            'REPLACEMENT: shifted to another degree program' => [
	                'request_type' => 10,
	                'status' => 0,
	                'with_trans_code' => false
	            ],
	            'Continuing undergraduate: First Request' => [
	                'request_type' => 11,
	                'status' => 0,
	                'with_trans_code' => false
	            ],
	            'Continuing Graduate Student: First Request' => [
	                'request_type' => 12,
	                'status' => 0,
	                'with_trans_code' => false
	            ]
	        ];

	        /*
	         * Tracks token-based inserts encountered during this scan.
	         *
	         * The key includes student number, request type, and token.
	         * Different request types must not be treated as duplicates.
	         */
	        $seen_tokens = [];

	        /*
	         * Prevents the same rejected database record from being
	         * scheduled for multiple updates during the scan.
	         */
	        $planned_updates = [];

	        /*
	         * 5. BEGIN TRANSACTION ONLY IN REPAIR MODE
	         */
	        if ($repair) {
	            $this->db->trans_begin();
	            $transaction_started = true;
	        }

	        /*
	         * 6. RECHECK EVERY GOOGLE SHEETS ROW
	         */
	        foreach ($values as $index => $row) {

	            $sheet_row_number = $index + 2;

	            $result['summary']['rows_scanned']++;

	            $report = [
	                'sheet_row' => $sheet_row_number,
	                'student_number' => isset($row[2])
	                    ? trim((string) $row[2])
	                    : '',
	                'request_type' => isset($row[9])
	                    ? trim((string) $row[9])
	                    : '',
	                'transaction_token' => isset($row[10])
	                    ? trim((string) $row[10])
	                    : '',
	                'action' => '',
	                'reason' => '',
	                'database_id' => null
	            ];

	            /*
	             * A. VALIDATE REQUIRED COLUMNS
	             */
	            if (
	                !isset($row[15]) ||
	                strtoupper(trim((string) $row[15])) !== 'GOOD'
	            ) {
	                $report['action'] = 'SKIP';
	                $report['reason'] = 'Validation column is not GOOD.';

	                $result['summary']['skipped']++;
	                $result['rows'][] = $report;

	                continue;
	            }

	            if (
	                !isset($row[0], $row[2], $row[9], $row[10]) ||
	                trim((string) $row[2]) === ''
	            ) {
	                $report['action'] = 'INVALID';
	                $report['reason'] =
	                    'Missing submission date, student number, request type, or token column.';

	                $result['summary']['invalid']++;
	                $result['rows'][] = $report;

	                continue;
	            }

	            /*
	             * B. DETERMINE REQUEST TYPE
	             */
	            $type_label = trim((string) $row[9]);

	            if (!isset($request_types[$type_label])) {
	                $report['action'] = 'INVALID';
	                $report['reason'] = 'Unknown request type.';

	                $result['summary']['invalid']++;
	                $result['rows'][] = $report;

	                continue;
	            }

	            $type_info = $request_types[$type_label];

	            $request_type = (int) $type_info['request_type'];
	            $status = (int) $type_info['status'];
	            $with_trans_code = $type_info['with_trans_code'];

	            $student_number = trim((string) $row[2]);
	            $token = trim((string) $row[10]);

	            $result['summary']['valid_rows']++;

	            /*
	             * C. CONVERT DATES
	             */
	            $submitted_timestamp = strtotime(
	                trim((string) $row[0])
	            );

	            $birthday_timestamp = isset($row[7])
	                ? strtotime(trim((string) $row[7]))
	                : false;

	            if (
	                $submitted_timestamp === false ||
	                $birthday_timestamp === false
	            ) {
	                $report['action'] = 'INVALID';
	                $report['reason'] =
	                    'Invalid submission date or birthday.';

	                $result['summary']['invalid']++;
	                $result['rows'][] = $report;

	                continue;
	            }

	            $date_submitted = date(
	                'Y-m-d H:i:s',
	                $submitted_timestamp
	            );

	            $birthday = date(
	                'Y-m-d',
	                $birthday_timestamp
	            );

	            /*
	             * D. PREPARE DATABASE DATA
	             */
	            $common_data = [
	                'email' => isset($row[1])
	                    ? trim((string) $row[1])
	                    : '',
	                'student_number' => $student_number,
	                'fname' => isset($row[3])
	                    ? trim((string) $row[3])
	                    : '',
	                'mname' => isset($row[4])
	                    ? trim((string) $row[4])
	                    : '',
	                'lname' => isset($row[5])
	                    ? trim((string) $row[5])
	                    : '',
	                'college' => isset($row[6])
	                    ? trim((string) $row[6])
	                    : '',
	                'birthday' => $birthday,
	                'address' => isset($row[8])
	                    ? trim((string) $row[8])
	                    : '',
	                'request_type' => $request_type,
	                'proof_of_payment' => isset($row[11])
	                    ? trim((string) $row[11])
	                    : '',
	                'form5' => isset($row[12])
	                    ? trim((string) $row[12])
	                    : '',
	                'id_pic' => isset($row[13])
	                    ? trim((string) $row[13])
	                    : '',
	                'signature' => isset($row[14])
	                    ? trim((string) $row[14])
	                    : '',
	                'status' => $status
	            ];

	            $action = '';
	            $reason = '';
	            $matched_id = null;

	            /*
	             * E. REQUESTS THAT REQUIRE A TRANSACTION TOKEN
	             */
	            if ($with_trans_code) {

	                $parts = explode('-', $token);

	                /*
	                 * CASE 1: FIVE-PART TOKEN
	                 *
	                 * Insert only when the same student, request type,
	                 * and transaction token do not already exist.
	                 */
	                if (count($parts) === 5) {

	                    $duplicate_key = $student_number
	                        . '|' . $request_type
	                        . '|' . $token;

	                    $existing = $this->db
	                        ->where('student_number', $student_number)
	                        ->where('request_type', $request_type)
	                        ->where('transaction_id', $token)
	                        ->where('is_deleted', 0)
	                        ->limit(1)
	                        ->get('id_requests')
	                        ->row_array();

	                    if (
	                        !empty($existing) ||
	                        isset($seen_tokens[$duplicate_key])
	                    ) {
	                        $action = 'SKIP';
	                        $reason =
	                            'Duplicate student number, request type, and transaction token.';

	                        if (!empty($existing)) {
	                            $matched_id = (int) $existing['id'];
	                        }

	                    } else {
	                        $action = 'INSERT';
	                        $reason =
	                            'No active record matches the student, request type, and token.';

	                        $seen_tokens[$duplicate_key] = true;
	                    }

	                /*
	                 * CASE 2: SIX-PART TOKEN
	                 *
	                 * Recover the original five-part token.
	                 * Only match records belonging to the same request type.
	                 */
	                } elseif (count($parts) === 6) {

	                    $original_token = implode(
	                        '-',
	                        array_slice($parts, 0, 5)
	                    );

	                    $matches = $this->db
	                        ->where('student_number', $student_number)
	                        ->where('request_type', $request_type)
	                        ->where('transaction_id', $original_token)
	                        ->where('is_deleted', 0)
	                        ->order_by('id', 'DESC')
	                        ->get('id_requests')
	                        ->result_array();

	                    if (empty($matches)) {

	                        $action = 'SKIP';
	                        $reason =
	                            'No active record matches the original token and request type.';

	                    } else {

	                        $non_rejected_match = null;
	                        $rejected_match = null;

	                        foreach ($matches as $match) {

	                            if ((int) $match['status'] !== 4) {
	                                $non_rejected_match = $match;
	                                break;
	                            }

	                            if ($rejected_match === null) {
	                                $rejected_match = $match;
	                            }
	                        }

	                        if ($non_rejected_match !== null) {

	                            $action = 'SKIP';
	                            $reason =
	                                'A matching active request has status other than 4.';

	                            $matched_id = (int) $non_rejected_match['id'];

	                        } elseif ($rejected_match !== null) {

	                            $matched_id = (int) $rejected_match['id'];

	                            if (isset($planned_updates[$matched_id])) {

	                                $action = 'SKIP';
	                                $reason =
	                                    'This rejected record is already scheduled for update.';

	                            } else {

	                                $action = 'UPDATE';
	                                $reason =
	                                    'Matching rejected request of the same type found.';

	                                $planned_updates[$matched_id] = true;
	                            }
	                        }
	                    }

	                } else {

	                    $action = 'SKIP';
	                    $reason =
	                        'Invalid token: expected five or six hyphen-separated parts.';
	                }

	            /*
	             * F. REQUESTS THAT DO NOT REQUIRE A TRANSACTION TOKEN
	             */
	            } else {

	                if ($token !== '') {

	                    $action = 'SKIP';
	                    $reason =
	                        'This request type requires a blank transaction token.';

	                } else {

	                    /*
	                     * Find a rejected request of the SAME TYPE.
	                     * A rejected request of another type must not match.
	                     */
	                    $rejected = $this->db
	                        ->where('student_number', $student_number)
	                        ->where('request_type', $request_type)
	                        ->where('status', 4)
	                        ->where('is_deleted', 0)
	                        ->order_by('id', 'DESC')
	                        ->get('id_requests')
	                        ->row_array();

	                    if (!empty($rejected)) {

	                        $matched_id = (int) $rejected['id'];

	                        if (isset($planned_updates[$matched_id])) {

	                            $action = 'SKIP';
	                            $reason =
	                                'This rejected record is already scheduled for update.';

	                        } else {

	                            $action = 'UPDATE';
	                            $reason =
	                                'Matching rejected request of the same type found.';

	                            $planned_updates[$matched_id] = true;
	                        }

	                    } else {

	                        /*
	                         * Do not skip simply because the student number
	                         * exists. Check the SAME request type instead.
	                         */
	                        $existing_same_request = $this->db
	                            ->where('student_number', $student_number)
	                            ->where('request_type', $request_type)
	                            ->where('is_deleted', 0)
	                            ->limit(1)
	                            ->get('id_requests')
	                            ->row_array();

	                        if (!empty($existing_same_request)) {

	                            $action = 'SKIP';
	                            $reason =
	                                'An active request of the same type already exists.';

	                            $matched_id = (int) $existing_same_request['id'];

	                        } else {

	                            $action = 'INSERT';
	                            $reason =
	                                'No active request of the same type exists.';
	                        }
	                    }
	                }
	            }

	            /*
	             * G. AUDIT OR EXECUTE
	             */
	            if (!$repair) {

	                if ($action === 'INSERT') {

	                    $report['action'] = 'WOULD_INSERT';
	                    $report['reason'] = $reason;

	                    $result['summary']['would_insert']++;
	                    $result['rows'][] = $report;

	                } elseif ($action === 'UPDATE') {

	                    $report['action'] = 'WOULD_UPDATE';
	                    $report['reason'] = $reason;
	                    $report['database_id'] = $matched_id;

	                    $result['summary']['would_update']++;
	                    $result['rows'][] = $report;

	                } else {

	                    $result['summary']['skipped']++;

	                    $report['action'] = 'SKIP';
	                    $report['reason'] = $reason;
	                    $report['database_id'] = $matched_id;

	                    $result['rows'][] = $report;
	                }

	                continue;
	            }

	            /*
	             * REPAIR MODE: INSERT
	             */
	            if ($action === 'INSERT') {

	                $insert_data = $common_data;

	                $insert_data['transaction_id'] = $token;
	                $insert_data['date_submitted'] = $date_submitted;

	                if (!$this->db->insert('id_requests', $insert_data)) {
	                    throw new Exception(
	                        'Insert failed for Google Sheets row '
	                        . $sheet_row_number
	                    );
	                }

	                $result['summary']['inserted']++;

	            /*
	             * REPAIR MODE: UPDATE REJECTED RECORD
	             */
	            } elseif ($action === 'UPDATE') {

	                $this->db
	                    ->where('id', $matched_id)
	                    ->where('student_number', $student_number)
	                    ->where('request_type', $request_type)
	                    ->where('is_deleted', 0)
	                    ->where('status', 4)
	                    ->update('id_requests', $common_data);

	                if ($this->db->trans_status() === false) {
	                    throw new Exception(
	                        'Update failed for Google Sheets row '
	                        . $sheet_row_number
	                    );
	                }

	                $result['summary']['updated']++;

	            } else {

	                $result['summary']['skipped']++;
	            }

	            $report['action'] = $action;
	            $report['reason'] = $reason;
	            $report['database_id'] = $matched_id;

	            $result['rows'][] = $report;
	        }

	        /*
	         * 7. COMMIT OR ROLLBACK
	         *
	         * The normal Google Sheets checkpoint is not modified.
	         */
	        if ($repair) {

	            if ($this->db->trans_status() === false) {

	                $this->db->trans_rollback();
	                $transaction_started = false;

	                throw new Exception(
	                    'Repair failed. Database transaction was rolled back.'
	                );
	            }

	            $this->db->trans_commit();
	            $transaction_started = false;
	        }

	        $result['success'] = true;

	        $result['message'] = $repair
	            ? 'Full-sheet repair completed.'
	            : 'Full-sheet audit completed. No database records were changed.';

	    } catch (Throwable $e) {

	        if ($transaction_started) {
	            $this->db->trans_rollback();
	            $transaction_started = false;
	        }

	        $result['success'] = false;
	        $result['message'] = $e->getMessage();
	        $result['summary']['errors']++;
	    }

	    /*
	     * 8. RETURN RESULTS
	     *
	     * Return the full report so skipped rows, request types,
	     * and duplicate decisions can be inspected.
	     */
	    header('Content-Type: application/json; charset=utf-8');

	    echo json_encode($result);
	}

	public function id_masterlist_add_new_record() {
		$this->load->model('General_model', '', TRUE);
		$this->load->model('Google_sheets_model', '', TRUE);
		$client = new Google_Client();

		$client->setApplicationName('Our IS');

		$client->setAuthConfig(
		    (FCPATH) . 'assets/credentials/service-account.json'
		);

		$client->setScopes([
		    Google_Service_Sheets::SPREADSHEETS,
		    Google_Service_Drive::DRIVE
		]);

		$service = new Google_Service_Sheets($client);
    	$sheets_data = $this->General_model->list_dynamic("tr_details_sheets_data",["status"=>0,"type"=>3]);
		$spreadsheetId = $sheets_data[0]->spreadsheetId;
		$tab_name = "MasterList";
		$end_col = "N";
		$range = "$tab_name!A:$end_col";//entire sheet
		$json_data = new stdClass();		
		try {
	        $student_number = $this->input->post('student_number');
	        $fname = $this->input->post('fname');
	        $lname = $this->input->post('lname');
	        $mname = $this->input->post('mname');
	        $college = $this->input->post('college');
	        $birthday = $this->input->post('birthday');
	        $address = $this->input->post('address');
	        $transaction_id = $this->input->post('transaction_id');
	        $email = $this->input->post('email');
			$values = [
			    [
			        $college,
			        $student_number,
			        $email,
			        $fname,
			        $lname,
			        $mname,
			        date('m/d/Y'),
			        "",
			        "SENT",
			        date('m/d/Y H:i:s'),
			        date('m/d/Y H:i:s'),
			        "1",
			        $transaction_id,
			        "UNUSED"
			    ]
			];

			$body = new Google_Service_Sheets_ValueRange([
			    'values' => $values
			]);

			$params = [
			    'valueInputOption' => 'USER_ENTERED',
			    'insertDataOption' => 'INSERT_ROWS'
			];

			$result = $service->spreadsheets_values->append(
			    $spreadsheetId,
			    $range,
			    $body,
			    $params
			);
		}  catch (\Throwable $e) {
		    $json_data->error = $e->getMessage();
		    $json_data->trace = $e->getTraceAsString();
		}
        echo json_encode($json_data);
	}
}
