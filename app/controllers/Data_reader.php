<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_reader extends Home_Controller {

	public function __construct(){
		parent::__construct();
		
		//判断权限	
		$user=$_SESSION['user'];		
		if( $user['auth']!=3 ){
			$this->msg('此帐号无法访问本路径',site_url('login'));
		}
	}

	public function index(){

		$login_user=$_SESSION['user'];

		//获取搜索字段
		$custom=trim($this->input->get('custom'));
		$drug=trim($this->input->get('drug'));
		$spec=trim($this->input->get('spec'));
		$factory=trim($this->input->get('factory'));
		$num=trim($this->input->get('num'));		

		//组合模糊查询
		$likeArr=array();
		$likeArr['custom']=$custom ? $custom : '';
		$likeArr['drug']=$drug ? $drug : '';
		$likeArr['spec']=$spec ? $spec : '';
		$likeArr['factory']=$factory ? $factory : '';
		$likeArr['num']=$num ? $num : '';


		//排序字段,前端保证只提交一个值
		$id_order=trim($this->input->get('id_order')) ? $this->input->get('id_order') : '';
		$num_order=trim($this->input->get('num_order')) ? $this->input->get('num_order') : '';
		$addtime_order=trim($this->input->get('addtime_order')) ? $this->input->get('addtime_order') : '';

		$order='id DESC';//默认排序
		if(!$id_order && !$num_order && !$addtime_order){
			$id_order='DESC';
		}

		if($id_order){
			$order='id '.$id_order;
		}
		if($num_order){
			$order='num '.$num_order;
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
		->get('data1')
		->num_rows();

		$this->load->library('pagination');		
		$config['use_page_numbers'] = TRUE;
		$config['page_query_string']= TRUE;
		$config['num_links'] = 3;
		$config['base_url']=site_url('data_reader/index'."
			?custom={$custom}&drug={$drug}&spec={$spec}&factory={$factory}&num={$num}&id_order={$id_order}&num_order={$num_order}&addtime_order={$addtime_order}");
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


	    $data['data1']=$this->db	    
	    ->where('isdel',0)
		->like($likeArr)
		->limit( $pageSize, ($page-1)*$pageSize )
		->order_by($order)
		->get('data1')
		->result_array();


	   	$data['search']=$likeArr;
	   	$data['id_order']=$id_order;	   
	   	$data['num_order']=$num_order;
	   	$data['addtime_order']=$addtime_order;



		
		$this->load->view('home/data_reader1.htm',$data);
		
	}



		public function index2(){

					$login_user=$_SESSION['user'];


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
					$config['base_url']=site_url('data_reader/index2'."
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



					
					$this->load->view('home/data_reader2.htm',$data);
			
		}


	

	

}
