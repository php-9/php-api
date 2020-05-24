<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Api_Controller {

	protected $noNeedLogin=['login','code','verify_code'];
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

	//获取用户
	public function row(){
		$id=$this->payload('id');
		$user=$this->db->where('id',$id)->get('admin')->row_array();
		$this->success($user);

	}

	//添加用户
	public function add(){
		$username=$this->payload('username');
		$password=$this->payload('password');

		if(!$username){
			$this->fail('请输入帐号');
		}

		if(!$password){
			$this->fail('请输入密码');
		}

		if($this->db->where('username',$username)->get('admin')->row_array()){
			$this->fail('帐号已存在');
		}

		if($this->db->insert('admin',['username'=>$username,'password'=>$password,'create_time'=>time()])){
			$this->success([],200);
		}

		$this->fail('添加失败');
		

	}


	//编辑用户
	public function edit(){
		$id=$this->payload('id');
		$user=$this->db->where('id',$id)->get('admin')->row_array();
		$this->success($user);

	}


	//删除用户
	public function del(){
		$id=$this->payload('id');
		$user=$this->db->where('id',$id)->get('admin')->row_array();
		$this->success($user);

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


	/////////
	//验证码 //
	/////////
	public function code(){

		//$id=$this->input->get('id');
		//唯一id
		$uni=md5(uniqid(md5(microtime(true)),true));

		//缓存数据
		//$this->cache->save($uni, '这是验证码', 300);
		//
		//$this->cache->get('3ef7d8ab3a3e35b60cb3b771f6caf7c6');
		$data['verify_uni']=$uni;
		$data['verify_url']=base_url().'uploads/verify_code/'.$uni.'.jpg';		

		$this->verify_code($uni);//生成验证码图片

		$this->success($data);

	}


	//验证码
	private function verify_code($uni=''){
		//创建一个大小为 150*38 的验证码  
		$image = imagecreatetruecolor(126, 32);  
		$bgcolor = imagecolorallocate($image, 255, 255, 255);  
		imagefill($image, 0, 0, $bgcolor);  
		  
		$captch_code = '';  
		for ($i = 0; $i < 4; $i++) {  
		    $fontsize = 5;  
		    $fontcolor = imagecolorallocate($image, rand(0, 160), rand(0, 160), rand(0, 160));  
		    $data = 'abcdefghjkmnpqrstuvwxy23456789';  
		    $fontcontent = substr($data, rand(0, strlen($data) - 1), 1);
		    $captch_code .= $fontcontent;  
		    $x = $i * 126 / 4 + rand(5,8);//($i * 150 / 4) + rand(5, 10);  
		    $y = rand(22, 25);  
		    //imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor); 
		    $size=rand(16,22);
		    imagefttext($image, $size , 0,  $x, $y, $fontcolor, realpath('./static/captcha/CharlemagneStd-Bold.otf'),$fontcontent); 
		}  


		//就生成的验证码保存到session  
		$this->cache->save($uni, $captch_code, 300);		  
		 
		//在图片上增加点干扰元素  
		for ($i = 0; $i < 200; $i++) {  
		    $pointcolor = imagecolorallocate($image, rand(180, 200), rand(180, 200), rand(180, 200));  
		    imagesetpixel($image, rand(1, 149), rand(1, 37), $pointcolor);  
		}  
		  
		//在图片上增加线干扰元素  
		for ($i = 0; $i < 3; $i++) {  
		    $linecolor = imagecolorallocate($image, rand(180, 220), rand(180, 220), rand(180, 220));  
		    imageline($image, rand(1, 149), rand(1, 37), rand(1, 149), rand(1, 37), $linecolor);  
		}

		
		//设置头  
		header('content-type:image/png');  
		imagepng($image,'./uploads/verify_code/'.$uni.'.jpg');  
		imagedestroy($image);
	}


		

	
}
