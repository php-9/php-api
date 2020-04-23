<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Controller{
	public function __construct()
	{
		parent::__construct();
	}
	
	
	public function index()
	{		

			$data['admin']=$this->db->get('admin')->result_array();
			$this->load->view('admin/admin.htm',$data);

	
	}

	public function add(){
		$this->load->view('admin/admin_add.htm');
	}

	public function add_pass(){

		$username=$this->input->post('username');
		$password=$this->input->post('password');


		if(!$username){
			$this->msg('请输入帐号');
		}

		if(!$password){
			$this->msg('请输入密码');
		}

		//判断帐号已存在
		if(  $this->db->where('username',$username)->get('admin')->row('username')  ){
			$this->msg('帐号已存在，请更换帐号');
		}

		

		$data['username']=$username;
		$data['password']=md5($password);		


		if( $this->db->insert('admin',$data) ){
			$this->msg('添加成功',admin_url('admin/index'));
		}

		$this->msg('添加失败,请重试');


	}

	public function edit($id){
		$member=$this->db->where('id',$id)->get('admin')->row_array();		
		$data['admin']=$member;
		$this->load->view('admin/admin_edit.htm',$data);

	}

	public function edit_pass($id){
		$member=$this->db->where('id',$id)->get('admin')->row_array();		
		$password=trim($this->input->post('password') );
		
		$data=[];	
		if($password){//密码字段有填写
			$data['password']=md5($password);//加密
			if(!$this->db->where('id',$id)->update('admin',$data)){
				$this->msg('修改失败!');
			}
			$this->msg('修改成功!',admin_url('admin'));
		}else{
			$this->msg('未修改',admin_url('admin'));
		}


	}

	public function del($id){
		if($this->db->where('id',$id)->delete('admin') ){
			$this->msg('删除成功!',admin_url('admin'));
		}

		$this->msg('删除失败!');
	}





}

