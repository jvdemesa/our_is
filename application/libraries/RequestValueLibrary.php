<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Centralized conversion of canonical Google/Form values to
 * human-readable values used by the legacy local database.
 *
 * Only mappings confirmed from the current relational data/form
 * are included here. Unknown values are returned unchanged.
 */
class RequestValueLibrary
{
    protected $maps = [

        'admitted_as' => [
            'new_first_year' => 'New First Year',
            'new_transferee' => 'New Transferee',
        ],

        'semester' => [
            'first_semester'  => '1st Semester',
            'second_semester' => '2nd Semester',
            'midyear'         => 'Midyear',
        ],

        'school_level' => [
            'associate' => 'Associate',
            'college'   => 'College',
            'ms'        => 'MS',
            'phd'       => 'PhD',
        ],

        'status' => [
            'transferred_out' => 'Transferred Out',
            'graduated' => 'Graduated',
            'currently_enrolled' => 'Currently Enrolled',
        ],

        'uploads' => [
            'scanned_original_diploma' => 'Original Diploma',
            'scanned_original_otr' => 'OTR',
            'affidavit_of_loss_or_damage' => 'Affidavid',
            'passport_photo_2x2' => '2x2 Photo',
            'wes_form' => 'WES Form',
            'signed_accomplished_form' => 'Signed Form',
            'official_request_letter' => 'Request Letter',
            'official_request_letter_registrar' => 'Letter to UR',
            'scanned_original_otr_cav' => 'CAV OTR',
        ],
    ];

    /**
     * Convert a canonical code to its display/local value.
     * Unknown values are deliberately preserved.
     */
    public function label($group, $value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (isset($this->maps[$group][$value])) {
            return $this->maps[$group][$value];
        }

        return $value;
    }

    /**
     * Case-insensitive conversion useful for values such as
     * school levels received from JSON.
     */
    public function labelInsensitive($group, $value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        foreach ($this->maps[$group] ?? [] as $code => $label) {
            if (strcasecmp($code, $value) === 0) {
                return $label;
            }
        }

        return $value;
    }

    /**
     * Convert a canonical value to a local integer type.
     * Returns NULL when no mapping exists.
     */
    public function schoolLevelType($value)
    {
        $value = strtolower(trim((string) $value));

        $map = [
            'associate' => 0,
            'college'   => 1,
            'ms'        => 2,
            'phd'       => 3,
        ];

        if (isset($map[$value])) {
            return $map[$value];
        }

        if (in_array($value, ['0', '1', '2', '3'], true)) {
            return (int) $value;
        }

        return null;
    }
}
