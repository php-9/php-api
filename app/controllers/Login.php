<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Pulic_Controller{

	public function __construct(){
		parent::__construct();
	}
	public function index(){
		

		$this->load->view('home/login.htm');
		
	}

	public function pass(){
		
		$username=$this->input->post('username',true);
		$password=$this->input->post('password',true);
		$code=$this->input->post('code',true);


		$username=trim($username);
		$password=trim($password);
		$code=trim($code);

		$code=strtolower($code);//转小写
		$password=md5($password);//md5


		if(!$username){
			$this->msg('请输入帐号');
		}


		if(!$password){
			$this->msg('请输入密码');
		}

		if(!$code){
			$this->msg('请输入验证码');
		}

		//校验验证码
		
		if( empty($_SESSION['home_code']) ||  $_SESSION['home_code']!=$code   ){
			$this->msg('验证码错误');	
		}

		if( $user=$this->db->where('username',$username)->where('password',$password)->get('user')->row_array()  ){

			//同时只允许一个登录
			$sessArr['session_id']=session_id();
			$sessArr['u_id']=$user['id'];
			$sessArr['addtime']=time();
			$this->db->where('u_id',$user['id'])->delete('session');//删除之前登录
			$this->db->insert('session',$sessArr);//重写新登录
			

			//成功登录
			
			$data['id']=$user['id'];
			$data['auth']=$user['auth'];
			$data['username']=$user['username'];

			$_SESSION['user'] = $data;

			//登录日志
			$logArr['ip']=$this->input->ip_address();
			$logArr['username']=$username;
			$logArr['con']='成功登录';
			$logArr['agent']=$this->agent->browser().','.$this->agent->version();//浏览器信息
			$logArr['addtime']=time();
			$logArr['type']=1;//登录

			$this->db->insert('log',$logArr);
			

			//跳转
			switch ($user['auth']) {
				case 1://数据表1
					header("Location: ".site_url('data1'));
					break;
				case 2://数据表2
					header("Location: ".site_url('data2'));
					break;
				case 3://晒看者
					header("Location: ".site_url('data_reader'));
					break;	
				default:
					//没权限
					unset($_SESSION['user']);
					$this->msg('此帐号没有权限访问');	
					break;
			}
			
			

		}else{

			$this->msg('帐号或密码错误');	

		}





		
		
	}


	//验证码
	public function captcha(){
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
		$_SESSION['home_code'] = $captch_code;  
		 
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
		imagepng($image);  
		imagedestroy($image);
	}

	public function logout(){
		$u=$_SESSION['user'];
		$this->db->where('u_id',$u['id'])->delete('session');
		unset($_SESSION['user']);
		header("Location: ".site_url('login'));
	}



}
