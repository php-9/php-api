<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Api_Controller {

	protected $noNeedLogin=['login'];
	public function __construct(){
		parent::__construct();					
	}
	public function index(){	
		
		// $this->db->insert('admin',array(
		// 	'username'=>'a'.mt_rand(1000,9999),
		// 	'addTime'=>time(),
		// 	'password'=>'asdfasdas'
		// 	));
		// 	
		$res=[];
		$res['pageNum']=$this->payload('pageNum') ? $this->payload('pageNum') : 1;
		//每页有多少行数据
		$res['pageSize']=$this->payload('pageSize') ? $this->payload('pageSize'): 10;
		

		$res['list']=$this->db	    
						->limit( $res['pageSize'], ($res['pageNum']-1)*$res['pageSize'] )		
						->get('admin')
						->result_array();

		$res['pageTotal']=$this->db	    
								
							->get('admin')
							->num_rows();


		
		$this->success($res);
		
		
	}

	public function reg(){
		
	}

	//登录
	public function login111(){
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

	//登录 
	public function login(){

		$this->load->library('user_agent');//浏览器信息
		$this->load->library('jwt');
		
		
		$username=$this->payload('username');
		$password=$this->payload('password');

		$password=md5($password);

		if($u=$this->db->where('username',$username)->where('password',$password)->get('admin')->row_array()){


			//生成token	
			$token=	$this->jwt->getToken($u['id']);	
			//记录登录日志
			$logArr['ip']=$this->input->ip_address();
			$logArr['u_id']=$u['id'];
			$logArr['username']=$u['username'];
			$logArr['description']='成功登录';
			$logArr['agent']=$this->agent->browser().','.$this->agent->version();//浏览器信息
			$logArr['addtime']=time();
			$logArr['type']=1;//登录

			$this->db->insert('admin_log',$logArr);


			//记录登录 token
			//只允许单点登录,同一账号不能同时登录
			$sessArr['token']=$token;
			$sessArr['u_id']=$u['id'];
			$sessArr['username']=$u['username'];
			$sessArr['addtime']=time();
			$this->db->where('u_id',$u['id'])->delete('admin_session');//删除之前登录
			$this->db->insert('admin_session',$sessArr);//重写新登录

			$res['token']=	$token;		
			//返回token等数据
			$this->success($res);
		}

		$this->fail('帐号密码错误！');

	}

	//帐号状态
	public function  status(){
		//更新状态
		if($_SERVER['REQUEST_METHOD']=='PUT'){
			$uid=$this->payload('id');
			$data=[];
			$data['status']=$this->payload('status');
			if($this->db->where('id',$uid)->update('admin',$data)){
				$this->success([],200,'数据更新成功');
			}
			
		}

		$this->fail('操作失败！');
	}


		

	
}
