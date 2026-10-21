<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Convert term text (e.g., "First Semester, 2016-2017") 
 * to numeric term code (e.g., 1161)
 */
if (!function_exists('convert_term_code')) {
    function convert_term_code($term_text)
    {
        if (empty($term_text)) return null;

        $term_text = trim($term_text);

        // 🔹 Identify semester/term
        $sem_code = 0;
        $is_midyear = false;

        if (stripos($term_text, 'First Semester') !== false) {
            $sem_code = 1;
        } elseif (stripos($term_text, 'Second Semester') !== false) {
            $sem_code = 2;
        } elseif (stripos($term_text, 'Midyear') !== false || stripos($term_text, 'Mid Year') !== false) {
            $sem_code = 3;
            $is_midyear = true;
        }

        // ⚡ Faster: extract 4-digit numbers only
        preg_match_all('/\d{4}/', $term_text, $matches);

        $full_year = 0;
        if (!empty($matches[0])) {
            $years = array_map('intval', $matches[0]);

            if ($is_midyear) {
                // If Midyear and two years exist, use 1st (start year)
                // If only one year exists, use previous year
                $full_year = (count($years) > 1) ? $years[0] : ($years[0] - 1);
            } else {
                // For First/Second Semester → use first (start year)
                $full_year = $years[0];
            }
        }

        // 🔹 Determine century
        $century = 0;
        if ($full_year >= 3000) {
            $century = 2;
        } elseif ($full_year >= 2000) {
            $century = 1;
        } elseif ($full_year >= 1900) {
            $century = 0;
        }

        // 🔹 Last two digits of year
        $year = (int)substr($full_year, -2);

        // 🔹 Combine into 4-digit string: CYYT
        return sprintf("%d%02d%d", $century, $year, $sem_code);
    }


}

/**
 * Convert numeric term code (e.g., 1162) 
 * back to readable text (e.g., "Second Semester, 2016–2017")
 */
if (!function_exists('convert_term_text')) {
    function convert_term_text($term_code)
    {
        if (empty($term_code) || !is_numeric($term_code)) return null;

        $term_code = str_pad($term_code, 4, '0', STR_PAD_LEFT);

        $century = substr($term_code, 0, 1);
        $year    = substr($term_code, 1, 2);
        $sem     = substr($term_code, -1);

        // 🔹 Convert to full year
        $full_year = ($century == '1') ? (2000 + (int)$year) : (1900 + (int)$year);

        // 🔹 Determine term text
        switch ($sem) {
            case '1':
                $term_text = "First Semester";
                $next_year = $full_year + 1;
                $term_text .= ", {$full_year}-{$next_year}";
                break;
            case '2':
                $term_text = "Second Semester";
                $next_year = $full_year + 1;
                $term_text .= ", {$full_year}-{$next_year}";
                break;
            case '3':
                $mid_year =$full_year+1;
                $term_text = "Midyear, {$mid_year}";
                break;
            default:
                $term_text = "Unknown Term";
                break;
        }

        return $term_text;
    }
}
