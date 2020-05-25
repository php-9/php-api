<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends Api_Controller {

	protected $noNeedLogin=[];
	public function __construct(){
		parent::__construct();					
	}
	public function index(){	
		
		$res=[];
		$res['pageNum']=$this->payload('pageNum') ? $this->payload('pageNum') : 1;
		//每页有多少行数据
		$res['pageSize']=$this->payload('pageSize') ? $this->payload('pageSize'): 10;
		

		$res['list']=$this->db	    
						->limit( $res['pageSize'], ($res['pageNum']-1)*$res['pageSize'] )		
						->get('goods')
						->result_array();

		$res['pageTotal']=$this->db	    
								
							->get('goods')
							->num_rows();


		
		$this->success($res);
		
		
	}

	//获取用户
	public function row(){
		$id=$this->input->get('id');

		if(!$id) $this->fail('参数错误');
		$user=$this->db->where('id',$id)->get('user')->row_array();
		$this->success($user);

	}

	//添加产品
	public function add(){
		//goods数据
		$goodsData=[];
		$goodsData['name']=$this->payload('name');
		$goodsData['brand']=$this->payload('brand');
		$goodsData['year']=$this->payload('year');
		$goodsData['thumbs']=$this->payload('thumbs');		
		$goodsData['create_time']=time();

		//品牌，年份，名称是否相同
		$goods=$this->db->where('brand',$goodsData['brand'])
						->where('year',$goodsData['year'])
						->where('name',$goodsData['name'])
						->get('goods')
						->row_array();
		//存在产品
		if($goods){
			//返回产品id
			$this->success(['goods_id'=>$goods['id']],200);
		}else{
			//添加产品
			if( $this->db->insert('goods',$goodsData) ){
				//返回产品id
				$this->success(['goods_id'=>$this->db->insert_id()],200);
			}

		}

		

		$this->fail('添加操作失败');
		

	}


	//编辑用户
	public function edit(){
		$id=$this->payload('id');
		$realname=$this->payload('realname');
		$status=$this->payload('status');

		$password=$this->payload('password');
		$password=trim($password);

		$userData=[];
		if($password) $userData['password']=$password;
		$userData['realname']=$realname;
		$userData['status']=$status;
		if(!$id) $this->fail('参数错误');
		if( $this->db->where('id',$id)->update('user',$userData) ){
			$this->success();
		}

		$this->fail('修改失败');

	}


	//删除用户
	public function del(){
		$id=$this->input->get('id');
		if(!$id) $this->fail('参数错误');

		if( $this->db->where('id',$id)->delete('user') ){
			$this->success();
		}

		$this->fail('删除操作失败');
		
		

	}

	

	//登录 
	public function login(){

		$this->load->library('user_agent');//浏览器信息
		$this->load->library('jwt');
		
		
		$username=$this->input->get('username');
		$password=$this->input->get('password');
		$verify=$this->input->get('verify');
		$verify_uni=$this->input->get('verify_uni');

		//获取缓存
		$code=$this->cache->get($verify_uni);

		if($verify!=$code){
			$this->fail('验证码错误！');
		}

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
