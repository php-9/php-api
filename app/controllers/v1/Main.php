<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends Api_Controller {

	
	public function __construct(){
		parent::__construct();					
	}
	public function index(){	
		
		
		$this->is_login();


		echo $this->create_token();
		//$this->json();

		
	}


		

	
}
