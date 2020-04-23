<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data2 extends Admin_Controller {

	public function __construct(){
		parent::__construct();
		
		
	}

	public function index(){
	


		//获取搜索字段
		$custom=trim($this->input->get('custom',true));	
		$is_send_qual=trim($this->input->get('is_send_qual',true));
		$send_qual=trim($this->input->get('send_qual',true));
		$send_qual_time=trim($this->input->get('send_qual_time',true));
		$express=trim($this->input->get('express',true));
		$is_rec_qual=trim($this->input->get('is_rec_qual',true));
		$rec_qual=trim($this->input->get('rec_qual',true));
		$remark=trim($this->input->get('remark',true));

	

		//组合模糊查询
		$likeArr=array();
		$likeArr['custom']=$custom ? $custom : '';
		$likeArr['is_send_qual']=$is_send_qual ? $is_send_qual : '';
		$likeArr['send_qual']=$send_qual ? $send_qual : '';
		$likeArr['send_qual_time']=$send_qual_time ? $send_qual_time : '';
		$likeArr['express']=$express ? $express : '';
		$likeArr['is_rec_qual']=$is_rec_qual ? $is_rec_qual : '';
		$likeArr['rec_qual']=$rec_qual ? $rec_qual : '';
		$likeArr['remark']=$remark ? $remark : '';


		//排序字段,前端保证只提交一个值
		$id_order=trim($this->input->get('id_order')) ? $this->input->get('id_order') : '';
		$send_qual_time_order=trim($this->input->get('send_qual_time_order')) ? $this->input->get('send_qual_time_order') : '';
		$addtime_order=trim($this->input->get('addtime_order')) ? $this->input->get('addtime_order') : '';

		$order='id DESC';//默认排序
		if(!$id_order && !$send_qual_time_order && !$addtime_order){
			$id_order='DESC';
		}

		if($id_order){
			$order='id '.$id_order;
		}
		if($send_qual_time_order){
			$order='send_qual_time '.$send_qual_time_order;
		}
		if($addtime_order){
			$order='addtime '.$addtime_order;
		}

		/*分页*/

		$pageSize=$this->conf['cfg_num'];
		$page=$this->input->get('per_page') ? $this->input->get('per_page') : 1;
	
		//总记录数
		$countRows=$this->db		
		->where('isdel',0)
		->like($likeArr)
		->get('data2')
		->num_rows();


		$this->load->library('pagination');		
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string']= TRUE;
		$config['num_links'] = 3;
		$config['base_url']=admin_url('data2/index'."
			?custom={$custom}&is_send_qual={$is_send_qual}&is_send_qual={$is_send_qual}&send_qual={$send_qual}&send_qual_time={$send_qual_time}&express={$express}&is_rec_qual={$is_rec_qual}&rec_qual={$rec_qual}&remark={$remark}&id_order={$id_order}&send_qual_time_order={$send_qual_time_order}&addtime_order={$addtime_order}");
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


	    $data['data2']=$this->db	   
	    ->where('isdel',0)
		->like($likeArr)
		->limit( $pageSize, ($page-1)*$pageSize )
		->order_by($order)
		->get('data2')
		->result_array();


		

	   	$data['search']=$likeArr;
	   	$data['id_order']=$id_order;	   
	   	$data['send_qual_time_order']=$send_qual_time_order;
	   	$data['addtime_order']=$addtime_order;



		
		$this->load->view('admin/data2.htm',$data);
		
	}


	// public function add(){
	// 	$this->load->view('home/data2_add.htm');
	// }

	// public function add_pass(){

	// 	$custom=trim($this->input->post('custom',true));	
	// 	$is_send_qual=trim($this->input->post('is_send_qual',true));
	// 	$send_qual=trim($this->input->post('send_qual',true));
	// 	$send_qual_time=trim($this->input->post('send_qual_time',true));
	// 	$express=trim($this->input->post('express',true));
	// 	$is_rec_qual=trim($this->input->post('is_rec_qual',true));
	// 	$rec_qual=trim($this->input->post('rec_qual',true));
	// 	$remark=trim($this->input->post('remark',true));




	// 	if(!$custom){
	// 		$this->msg('请输入客户名称');
	// 	}


	// 	$login_user=$_SESSION['user'];

	// 	$data['custom']=$custom;
	// 	$data['is_send_qual']=$is_send_qual;
	// 	$data['send_qual']=$send_qual;
	// 	$data['send_qual_time']=$send_qual_time;
	// 	$data['express']=$express;
	// 	$data['is_rec_qual']=$is_rec_qual;
	// 	$data['rec_qual']=$rec_qual;
	// 	$data['remark']=$remark;
	// 	$data['addtime']=time();
	// 	$data['edittime']=time();
	// 	$data['u_id']=$login_user['id'];

		

	// 	if( $this->db->insert('data2',$data) ){

	// 		//成功
	// 		$login_user=$_SESSION['user'];			
	// 		//日志
	// 		$logArr['ip']=$this->input->ip_address();
	// 		$logArr['username']=$login_user['username'];
	// 		$logArr['con']='数据表2 (id:'.$this->db->insert_id().')添加成功';
	// 		$logArr['agent']=$this->agent->browser().','.$this->agent->version();//浏览器信息
	// 		$logArr['addtime']=time();
	// 		$logArr['type']=3;//添加数据
	// 		$this->db->insert('log',$logArr);

	// 		$this->msg('数据添加成功',site_url('data2/index'));
	// 	}

	// 	$this->msg('数据添加失败');
	// }

	public function edit($id){
		$data['data2']=$this->db->where('id',$id)->get('data2')->row_array();
		$this->load->view('admin/data2_edit.htm',$data);
	}

	public function edit_pass($id){

		$custom=trim($this->input->post('custom',true));	
		$is_send_qual=trim($this->input->post('is_send_qual',true));
		$send_qual=trim($this->input->post('send_qual',true));
		$send_qual_time=trim($this->input->post('send_qual_time',true));
		$express=trim($this->input->post('express',true));
		$is_rec_qual=trim($this->input->post('is_rec_qual',true));
		$rec_qual=trim($this->input->post('rec_qual',true));
		$remark=trim($this->input->post('remark',true));




		if(!$custom){
			$this->msg('请输入客户名称');
		}


		$login_user=$_SESSION['admin'];

		$data['custom']=$custom;
		$data['is_send_qual']=$is_send_qual;
		$data['send_qual']=$send_qual;
		$data['send_qual_time']=$send_qual_time;
		$data['express']=$express;
		$data['is_rec_qual']=$is_rec_qual;
		$data['rec_qual']=$rec_qual;
		$data['remark']=$remark;
		//$data['addtime']=time();
		$data['edittime']=time();
		$data['u_id']=$login_user['id'];

		

		if( $this->db->where('id',$id)->update('data2',$data) ){

			//成功					
			//日志
			$logArr['ip']=$this->input->ip_address();
			$logArr['username']=$login_user['username'];
			$logArr['con']='数据表2 (id:'.$id.')编辑';
			$logArr['agent']=$this->agent->browser().','.$this->agent->version();//浏览器信息
			$logArr['addtime']=time();
			$logArr['type']=3;//编辑数据
			$this->db->insert('admin_log',$logArr);

			$this->msg('数据编辑成功',admin_url('data2/edit/'.$id));
		}

		$this->msg('数据编辑失败');
		
	}


	public function del($id){

		$data['isdel']=1;

		if( $this->db->where('id',$id)->update('data2',$data) ){

			//成功
			$login_user=$_SESSION['admin'];			
			//日志
			$logArr['ip']=$this->input->ip_address();
			$logArr['username']=$login_user['username'];
			$logArr['con']='数据表2 (id:'.$id.')删除';
			$logArr['agent']=$this->agent->browser().','.$this->agent->version();//浏览器信息
			$logArr['addtime']=time();
			$logArr['type']=3;//删除数据
			$this->db->insert('admin_log',$logArr);

			$this->msg('已删除',admin_url('data2/index'));
		}

		$this->msg('删除失败,请重试');

	}

	public function select_del(){

		$str=trim($this->input->post('select_str'));


		if(!$str){
			$this->msg('请选择要删除的数据记录',$_SERVER['HTTP_REFERER']);
		}

		$arr=explode(',', $str);//

		foreach ($arr as $v) {
			//批量删除
			$this->db->where('id', $v)->delete('data2');			
		}

		$this->msg('批量删除操作完成',$_SERVER['HTTP_REFERER'] );
	}

	

}
