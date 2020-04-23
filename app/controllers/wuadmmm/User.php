<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	
	public function index(){
			
			//获取搜索字段
			$username=$this->input->get('username');		
			$realname=$this->input->get('realname');
			$department=$this->input->get('department');
			$position=$this->input->get('position');
			$auth=$this->input->get('auth');

			$likeArr=array();

			$likeArr['username']=$username ? $username : '';
			$likeArr['realname']=$realname ? $realname : '';
			$likeArr['department']=$department ? $department : '';
			$likeArr['position']=$position ? $position : '';
			$likeArr['auth']=$auth ? $auth : '';

			//id排序字段
			$id_order=$this->input->get('id_order') ? $this->input->get('id_order') : 'DESC';





			
			/*分页*/

			$pageSize=20;
			$page=$this->input->get('per_page') ? $this->input->get('per_page') : 1;
		
			//总记录数
			$countRows=$this->db
			->where('isdel',0)
			->like($likeArr)
			->get('user')
			->num_rows();

			$this->load->library('pagination');		
			$config['use_page_numbers'] = TRUE;
			$config['page_query_string']= TRUE;
			$config['num_links'] = 3;
			$config['base_url']=admin_url('user/index'."
				?username={$username}&realname={$realname}&department={$department}&position={$position}&auth={$auth}&id_order={$id_order}");
			$config['total_rows']=$countRows;//记录总数
			$config['per_page']=$pageSize;						
			$config['full_tag_open'] ="<div class='page'>";
	        $config['full_tag_close'] ="</div>";		
	        $config['full_tag_open'] = '<ul class=pagination>';
	        $config['full_tag_close'] = '<li><a>共 '.$countRows.' 条记录</a></li>'.'</ul>';
	        $config['cur_tag_open'] = '<li class="active"><a>';
	        $config['cur_tag_close'] = '</a></li>';
	        $config['num_tag_open'] = '<li>';
	        $config['num_tag_close'] = '</li>';
	        $config['prev_tag_open'] = '<li>';
	        $config['prev_tag_close'] = '</li>';
	        $config['next_tag_open'] = '<li>';
	        $config['next_tag_close'] = '</li>';
	        $config['first_link'] = '首页';
	        $config['first_tag_open'] = '<li>';
	        $config['first_tag_close'] = '</li>';
	        $config['last_link'] = '末页';
	        $config['last_tag_open'] = '<li>';
	        $config['last_tag_close'] = '</li>';
			$this->pagination->initialize($config);
		    $data['pages']=$this->pagination->create_links();
		    //分页


		    $data['user']=$this->db
		    ->where('isdel',0)
			->like($likeArr)
			->limit( $pageSize, ($page-1)*$pageSize )
			->order_by('id '.$id_order)
			->get('user')
			->result_array();


		   	$data['search']=$likeArr;
		   	$data['id_order']=$id_order;
			$this->load->view('admin/user.htm',$data);	
	}

	public function add(){
			$this->load->view('admin/user_add.htm');	
	}


	public function add_pass(){
		$username=$this->input->post('username');
		$password=$this->input->post('password');
		$realname=$this->input->post('realname');
		$department=$this->input->post('department');
		$position=$this->input->post('position');
		$auth=$this->input->post('auth');


		if(!$username){
			$this->msg('请输入帐号');
		}

		if(!$password){
			$this->msg('请输入密码');
		}

		//判断帐号已存在
		if(  $this->db->where('username',$username)->get('user')->row('username')  ){
			$this->msg('帐号已存在，请更换帐号');
		}

		

		$data['username']=$username;
		$data['password']=md5($password);		
		$data['realname']=$realname;
		$data['department']=$department;
		$data['position']=$position;
		$data['auth']=$auth;
		$data['addtime']=time();

		if( $this->db->insert('user',$data) ){
			$this->msg('添加成功',admin_url('user/index'));
		}

		$this->msg('添加失败,请重试');

	}


	public function edit($id){

		$data['user']=$this->db->where('id',$id)->get('user')->row_array();
		$this->load->view('admin/user_edit.htm',$data);


	}


	public function edit_pass($id){

		
		$password=$this->input->post('password');
		$realname=$this->input->post('realname');
		$department=$this->input->post('department');
		$position=$this->input->post('position');
		$auth=$this->input->post('auth');


		$password=trim($password);
		$realname=trim($realname);
		$department=trim($department);
		$position=trim($position);
		$auth=trim($auth);

		//存在修改密码
		if($password){
			$data['password']=md5($password);
		}
		
				
		$data['realname']=$realname;
		$data['department']=$department;
		$data['position']=$position;
		$data['auth']=$auth;




		if( $this->db->where('id',$id)->update('user',$data) ){
			$this->msg('编辑成功',admin_url('user'));
		}

		$this->msg('编辑失败,请重试');

	}



	public function del($id){

		if( $this->db->where('id',$id)->delete('user') ){
			$this->msg('已删除',admin_url('user'));
		}

		$this->msg('删除失败,请重试');

	}








}

