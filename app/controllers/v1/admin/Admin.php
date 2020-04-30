<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Api_Controller {

	protected $noNeedLogin=['index','login'];

	public function __construct(){
		parent::__construct();					
	}

	public function index(){

	}

	public function login(){
		
		
		$username=$this->payload('username');
		$password=$this->payload('password');

		$password=md5($password);

		if($admin=$this->db->where('username',$username)->where('password',$password)->get('admin')->row_array()){
			//返回token等数据
			$this->success($admin);
		}

		$this->fail('帐号密码错误！');

	}




		

	
}
