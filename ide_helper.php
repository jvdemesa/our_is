
<?php

/**
 * IDE helper for CodeIgniter 3.
 * This file is for editor autocomplete and analysis.
 */

class CI_Controller
{
    /** @var CI_Loader */
    public $load;

    /** @var CI_DB_query_builder */
    public $db;

    /** @var CI_Session */
    public $session;

    /** @var CI_Input */
    public $input;

    /** @var CI_Output */
    public $output;

    /** @var CI_Form_validation */
    public $form_validation;

    /** @var CI_Config */
    public $config;

    /** @var CI_Email */
    public $email;
}

class CI_Model
{
    /** @var CI_Loader */
    public $load;

    /** @var CI_DB_query_builder */
    public $db;

    /** @var CI_Session */
    public $session;

    /** @var CI_Input */
    public $input;

    /** @var CI_Config */
    public $config;
}
