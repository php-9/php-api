<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends home_Controller{

	public function __construct(){
		parent::__construct();
	}
	

	public function edit_pwd(){
		$this->load->view('home/user_edit_pwd.htm');
	}


	public function edit_pwd_pass(){
		
		$old_password=trim($this->input->post('old_password',true));
		$new_password=trim($this->input->post('new_password',true));
		$new_password2=trim($this->input->post('new_password2',true));


		


		if(!$old_password){
			$this->msg('请输入原来的密码');
		}

		if(!$new_password){
			$this->msg('请输入新的密码');
		}

		if(!$new_password2){
			$this->msg('请输入确认密码');
		}

		if($new_password!=$new_password2){
			$this->msg('两次输入的新密码不相同');
		}



		$old_password=md5($old_password);
		$new_password=md5($new_password);

		$user=$_SESSION['user'];

		$u=$this->db->where('id',$user['id'])->where('password',$old_password)->get('user')->row_array();

		if(!$u){
			$this->msg('原密码不正确,请重试');
		}

		$data['password']=$new_password;

		if( $this->db->where('id',$u['id'])->update('user',$data) ){
			//成功
			
			//日志
			$logArr['ip']=$this->input->ip_address();
			$logArr['username']=$u['username'];
			$logArr['con']='修改密码';
			$logArr['agent']=$this->agent->browser().','.$this->agent->version();//浏览器信息
			$logArr['addtime']=time();
			$logArr['type']=2;//密码修改
			$this->db->insert('log',$logArr);

			//跳转
			switch ($u['auth']) {
				case 1://数据表1
					$this->msg('修改成功',site_url('data1'));
					break;
				case 2://数据表2
					$this->msg('修改成功',site_url('data2'));
					break;
				case 3://晒看者
					$this->msg('修改成功',site_url('data_reader'));
					break;	
				default:
					//没权限
					unset($_SESSION['user']);
					$this->msg('此帐号没有权限访问');	
					break;
			}

			
		}

		$this->msg('修改失败,请重试');


	}



}
