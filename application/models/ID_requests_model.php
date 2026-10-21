<?php  
	class ID_requests_model extends CI_Model{		
		var $column_order = array('a.rownum'); //set column field database for datatable orderable
		var $column_search = array(
			"a.student_number",
		    "a.fname",
		    "a.lname",
		    "a.mname",
		    "concat(lname,' ', fname)",
		    "concat(lname,', ', fname)",
		    );

		var $order = array('a.rownum' => 'DESC'); // default order   

		public function __construct(){
			parent::__construct();
			$this->load->database();
		}
		 
		public function _get_datatables_query()
		{
		    $this->db->select('a.*, b.release_date');
		    $this->db->from('id_requests a');
		    $this->db->join(
		        "(SELECT id_request_id, MAX(release_date) AS release_date
		          FROM id_email_logs
		          WHERE is_sent = '1'
		            AND release_date IS NOT NULL
		          GROUP BY id_request_id) b",
		        'b.id_request_id = a.id',
		        'left',
		        FALSE
		    );

		    $this->db->where('a.is_deleted', 0);

		    if ($this->input->post('status') != '') {
		        $this->db->where('a.status', $this->input->post('status'));
		    }
		    if ($this->input->post('request_type') != '') {
		        $this->db->where('a.request_type', $this->input->post('request_type'));
		    }

		    $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
		    if ($search !== '') {
		        $i = 0;
		        foreach ($this->column_search as $item) {
		            if ($i === 0) {
		                $this->db->group_start();
		                $this->db->like($item, $search);
		            } else {
		                $this->db->or_like($item, $search);
		            }
		            if (count($this->column_search) - 1 == $i) {
		                $this->db->group_end();
		            }
		            $i++;
		        }
		    }

		    $this->db->order_by('a.id', 'ASC');
		}

		public function get_datatables()
		{
		    $this->_get_datatables_query();
		    if (isset($_POST['length']) && $_POST['length'] != -1) {
		        $this->db->limit($_POST['length'], $_POST['start']);
		    }
		    return $this->db->get()->result();
		}

		public function count_filtered()
		{
		    $this->_get_datatables_query();
		    return $this->db->get()->num_rows();
		}

		public function count_all()
		{
		    $this->db->from('id_requests a');
		    $this->db->where('a.is_deleted', 0);
		    return $this->db->count_all_results();
		} 

		public function count_release_sched($release_date){
			$this->db->select('COUNT(*) AS total_release');
			$this->db->from('id_email_logs a');
			$this->db->where('a.is_sent', 1);
			$this->db->where('a.release_date', $release_date);
			$this->db->where('a.action', "release_schedule");

			$query = $this->db->get();
			// echo $this->db->last_query();
			// exit();
			return $query->row()->total_release;
		}   

		/*
		 * =================================================================
		 * ID Request Non-Compliance persistence
		 * =================================================================
		 * Non-Compliance submissions are stored in two tables:
		 *   id_request_non_compliance_messages - one row per submission (case)
		 *   id_request_non_compliance_uploads  - one row per file issue
		 * The message row id is used as non_compliance_case_id so the file
		 * issues stay grouped with the submission that produced them.
		 */

		// File identifiers accepted from the Non-Compliance modal
		// (see views/id_requests/index_modal.php). Stored in uploads.file_type.
		const NON_COMPLIANCE_FILE_TYPES = array(
			'id_picture',
			'signature',
			'form5',
			'first_year_form5',
			'proof_of_payment'
		);

		/**
		 * Returns the accepted Non-Compliance file identifiers.
		 */
		public function get_non_compliance_file_types(){
			return self::NON_COMPLIANCE_FILE_TYPES;
		}

		/**
		 * Checks that a file identifier is one of the accepted values.
		 */
		public function is_valid_non_compliance_file_type($file_type){
			return in_array($file_type, self::NON_COMPLIANCE_FILE_TYPES, TRUE);
		}

		/**
		 * Validates a positive integer id (accepts int or numeric string).
		 */
		private function is_positive_integer($value){
			if(is_int($value)){
				return $value > 0;
			}
			if(is_string($value) && $value !== '' && ctype_digit($value)){
				return ((int)$value) > 0;
			}
			return FALSE;
		}

		/**
		 * Confirms that an ID request id exists in id_requests.
		 */
		public function is_valid_id_request($id_request_id){
			if(!$this->is_positive_integer($id_request_id)){
				return FALSE;
			}
			$this->db->select('id');
			$this->db->from('id_requests');
			$this->db->where('id', (int)$id_request_id);
			$query = $this->db->get();
			return ($query->num_rows() > 0);
		}

		/**
		 * Normalises the incoming file issue payload into
		 * array(file_type => upload_reason).
		 *
		 * Accepts either an associative array keyed by file identifier
		 *   array('id_picture' => 'blurry photo', 'form5' => 'wrong semester')
		 * or a list of rows
		 *   array(array('file_type' => 'id_picture', 'upload_reason' => '...'))
		 * Duplicate file identifiers collapse into a single entry, which
		 * prevents duplicate file issue rows within one submission.
		 */
		public function normalize_non_compliance_file_reasons($files){
			$normalized = array();
			if(empty($files) || !is_array($files)){
				return $normalized;
			}

			foreach($files as $key => $value){
				$file_type = NULL;
				$reason = NULL;

				if(is_array($value)){
					if(isset($value['file_type'])){
						$file_type = $value['file_type'];
					}elseif(isset($value['file'])){
						$file_type = $value['file'];
					}
					if(isset($value['upload_reason'])){
						$reason = $value['upload_reason'];
					}elseif(isset($value['reason'])){
						$reason = $value['reason'];
					}
				}elseif(!is_int($key)){
					$file_type = $key;
					$reason = $value;
				}

				if($file_type === NULL){
					continue;
				}

				$file_type = trim((string)$file_type);
				if($file_type === ''){
					continue;
				}

				// Last value wins; also guarantees one row per file type.
				$normalized[$file_type] = ($reason === NULL) ? '' : trim((string)$reason);
			}

			return $normalized;
		}

		/**
		 * Validates a Non-Compliance submission.
		 *
		 * Rules:
		 *   - a general message and/or at least one file issue is required
		 *   - each file issue must use an accepted file identifier
		 *   - each selected file must have a reason
		 *
		 * Returns array(message, file_reasons, errors).
		 */
		public function validate_non_compliance_submission($message, $file_reasons){
			$errors = array();
			$message = ($message === NULL) ? '' : trim((string)$message);
			$file_reasons = $this->normalize_non_compliance_file_reasons($file_reasons);

			foreach($file_reasons as $file_type => $reason){
				if(!$this->is_valid_non_compliance_file_type($file_type)){
					$errors[] = "Invalid file identifier: ".$file_type.".";
					continue;
				}
				if($reason === ''){
					$errors[] = "A reason is required for the selected file: ".$file_type.".";
				}
			}

			if($message === '' && empty($file_reasons)){
				$errors[] = "Provide a Non-Compliance message or at least one file issue.";
			}

			return array(
				'message'      => $message,
				'file_reasons' => $file_reasons,
				'errors'       => $errors
			);
		}

		/**
		 * Persists a Non-Compliance submission: one message row (the case)
		 * plus one row per file issue. The whole submission is written in a
		 * single transaction so it either succeeds or rolls back completely.
		 * Previous submissions are never overwritten; a new case is always
		 * created.
		 *
		 * @return array error (0|1), message, id, non_compliance_case_id, upload_count
		 */
		public function save_non_compliance($id_request_id, $message, $file_reasons, $created_by = NULL){
			if(!$this->is_valid_id_request($id_request_id)){
				return array('error'=>1, 'message'=>'Invalid or unknown request ID.');
			}

			$validated = $this->validate_non_compliance_submission($message, $file_reasons);
			if(!empty($validated['errors'])){
				return array('error'=>1, 'message'=>implode(' ', $validated['errors']));
			}

			$id_request_id = (int)$id_request_id;
			$message_text = ($validated['message'] === '') ? NULL : $validated['message'];
			$file_reasons = $validated['file_reasons'];
			$created_by = $this->is_positive_integer($created_by) ? (int)$created_by : NULL;
			$created_at = date('Y-m-d H:i:s');

			$this->db->trans_start();

			$this->db->insert('id_request_non_compliance_messages', array(
				'id_request_id'          => $id_request_id,
				'non_compliance_message' => $message_text,
				'created_by'             => $created_by,
				'created_at'             => $created_at
			));
			$case_id = $this->db->insert_id();

			$upload_count = 0;
			if($case_id && !empty($file_reasons)){
				$batch = array();
				foreach($file_reasons as $file_type => $reason){
					$batch[] = array(
						'id_request_id'          => $id_request_id,
						'non_compliance_case_id' => $case_id,
						'file_type'              => $file_type,
						'upload_reason'          => $reason,
						'created_by'             => $created_by,
						'created_at'             => $created_at
					);
				}
				$this->db->insert_batch('id_request_non_compliance_uploads', $batch);
				$upload_count = $this->db->affected_rows();
			}

			$this->db->trans_complete();

			if($this->db->trans_status() === FALSE || !$case_id){
				return array('error'=>1, 'message'=>'Transaction Failed. Something went wrong. Please try again.');
			}

			return array(
				'error'                  => 0,
				'message'                => 'Non-Compliance has been saved.',
				'id'                     => (int)$case_id,
				'non_compliance_case_id' => (int)$case_id,
				'upload_count'           => (int)$upload_count
			);
		}

		/**
		 * Retrieves the latest Non-Compliance submission for a request and
		 * only the upload reasons that belong to that submission.
		 *
		 * Submissions are ordered by created_at DESC with id DESC as the
		 * tie-breaker. Requests without Non-Compliance history return the
		 * same response shape with found=FALSE and empty values.
		 */
		public function get_latest_non_compliance($id_request_id){
			$response = array(
				'error'      => 0,
				'message'    => '',
				'found'      => FALSE,
				'submission' => NULL,
				'uploads'    => array()
			);

			if(!$this->is_valid_id_request($id_request_id)){
				$response['error'] = 1;
				$response['message'] = 'Invalid or unknown request ID.';
				return $response;
			}

			$this->db->select('*');
			$this->db->from('id_request_non_compliance_messages');
			$this->db->where('id_request_id', (int)$id_request_id);
			$this->db->order_by('created_at', 'DESC');
			$this->db->order_by('id', 'DESC');
			$this->db->limit(1);
			$submission = $this->db->get()->row();

			if(empty($submission)){
				return $response;
			}

			$response['found'] = TRUE;
			$response['submission'] = $submission;

			$this->db->select('*');
			$this->db->from('id_request_non_compliance_uploads');
			$this->db->where('non_compliance_case_id', $submission->id);
			$this->db->where('id_request_id', (int)$id_request_id);
			$this->db->order_by('id', 'ASC');
			$response['uploads'] = $this->db->get()->result();

			return $response;
		}

		/**
		 * Convenience accessor for the latest Non-Compliance message row
		 * (or FALSE when the request has no Non-Compliance history).
		 */
		public function get_latest_non_compliance_message($id_request_id){
			$response = $this->get_latest_non_compliance($id_request_id);
			if($response['error'] === 1 || !$response['found']){
				return FALSE;
			}
			return $response['submission'];
		}

	}
?>
