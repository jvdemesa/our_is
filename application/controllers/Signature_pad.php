<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signature_pad extends MY_Controller {

	public function __construct() {
		parent::__construct();
		//$this->isLogin();
	}

	public function index(){
		$this->load->view('signature_pad/index');
	}

	public function initial_sinature(){
		$this->load->view('signature_pad/index');
	}
}