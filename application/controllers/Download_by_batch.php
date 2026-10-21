<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download_by_batch extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['file', 'download']);
    }

    // Example: /transcript_backup/download_batch/1
    // will download part 1 (rows 1–50000)
    public function download_batch($batch_no = 1) {
        $batch_no = (int)$batch_no;
        if ($batch_no < 1) $batch_no = 1;

        $limit = 50000; // adjust batch size here
        $offset = ($batch_no - 1) * $limit;

        // count total records
        $total_rows = $this->db->count_all('transcript_general_data');
        if ($offset >= $total_rows) {
            show_error("Batch number out of range. Total rows: $total_rows");
            return;
        }

        // fetch batch data
        $query = $this->db
            ->limit($limit, $offset)
            ->get('transcript_general_data');

        $rows = $query->result_array();

        if (empty($rows)) {
            show_error("No data found for batch $batch_no");
            return;
        }

        // convert to CSV
        $csv_data = $this->array_to_csv($rows);

        $filename = "general_data_batch_{$batch_no}.csv";
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
        $total_rows = $this->db->count_all('transcript_general_data');
        $total_batches = ceil($total_rows / $limit);

        echo "Total rows: $total_rows<br>";
        echo "Batch size: $limit<br>";
        echo "Total batches: $total_batches<br><br>";

        for ($i = 1; $i <= $total_batches; $i++) {
            echo "<a href='" . site_url("transcript_backup/download_batch/$i") . "'>Download batch $i</a><br>";
        }
    }
}
