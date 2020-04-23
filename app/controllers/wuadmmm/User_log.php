<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_log extends Admin_Controller{
	public function __construct(){
		parent::__construct();
	}
	
	
	public function index(){
	

		//获取搜索字段
		$username=trim($this->input->get('username'));
		$ip=trim($this->input->get('ip'));
		

		//组合模糊查询
		$likeArr=array();
		$likeArr['username']=$username ? $username : '';
		$likeArr['ip']=$ip ? $ip : '';



		//排序字段,前端保证只提交一个值
		$id_order=trim($this->input->get('id_order')) ? $this->input->get('id_order') : '';		
		$addtime_order=trim($this->input->get('addtime_order')) ? $this->input->get('addtime_order') : '';

		$order='id DESC';//默认排序
		if(!$id_order && !$addtime_order){
			$id_order='DESC';
		}

		if($id_order){
			$order='id '.$id_order;
		}
		if($addtime_order){
			$order='addtime '.$addtime_order;
		}

		/*分页*/

		$pageSize=$this->conf['cfg_num'];
		$page=$this->input->get('per_page') ? $this->input->get('per_page') : 1;
	
		//总记录数
		$countRows=$this->db
		->like($likeArr)
		->get('log')
		->num_rows();

		$this->load->library('pagination');		
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string']= TRUE;
		$config['num_links'] = 3;
		$config['base_url']=site_url('user_log/index'."
			?username={$username}&ip={$ip}&id_order={$id_order}&addtime_order={$addtime_order}");
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


	    $data['user_log']=$this->db
		->like($likeArr)
		->limit( $pageSize, ($page-1)*$pageSize )
		->order_by($order)
		->get('log')
		->result_array();


	   	$data['search']=$likeArr;
	   	$data['id_order']=$id_order;
	   	$data['addtime_order']=$addtime_order;



		
		$this->load->view('admin/user_log.htm',$data);
		
	}


	public function del($id){

		if($this->db->where('id',$id)->delete('log') ){
			$this->msg('删除成功',admin_url('user_log/index'));
		}

		$this->msg('删除失败，请重试');
	}






}

