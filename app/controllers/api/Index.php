<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends Api_Controller {

	protected $noNeedLogin=['index','login'];

	public function __construct(){
		parent::__construct();					
	}

	public function index(){	
		
		
		//$this->is_login();

		//echo 'api';
		//echo $this->create_token();
		//$this->json();
		//var_dump(empty($_SERVER['HTTP_TOKEN']));

		//$this->fail();
		//$this->success();
		//session_start();
		//var_dump($this->loginUser);
		
		if ( ! $foo = $this->cache->get('foo'))
		{
		    echo 'Saving to the cache!<br />';
		    $foo = 'foobarbaz!';

		    // Save into the cache for 5 minutes
		    $this->cache->save('foo', $foo, 300);
		}

		echo $foo;
		var_dump(session_id());

	}

	//登录
	public function login(){
		$this->load->library('user_agent');//浏览器信息
		$this->load->library('jwt');
		
		$uid = 111;


		//删除当前用户之前token
		$this->db->where('uid',$uid)->delete('user_token');

		$token = $this->jwt->getToken($uid);//生成token

		$data['uid']=$uid;
		$data['token']=$token;
		$data['ip']=$this->input->ip_address();
		$data['agent']=$this->agent->browser().','.$this->agent->version();
		$data['addTime']=time();

		var_dump($data);
		$this->db->insert('user_token',$data);


		//登录日志记录
		$this->db->insert('user_login_log',$data);
	}

	//退出登录
	public function logout(){

		$this->db->where('uid',$this->loginUser['id'])->delete('user_token');

	}


		

	
}
