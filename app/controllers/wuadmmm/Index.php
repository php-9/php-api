<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends Admin_Controller{

	public function __construct(){

		parent::__construct();
		
	}

	public function index(){
		//后台首页
		
		$data['model']=$this->db->get('model')->result_array();
		$this->load->view('admin/index.htm',$data);
		
	}
	


}
