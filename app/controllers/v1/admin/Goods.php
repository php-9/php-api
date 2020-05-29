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
		$goodsData['title']=$this->payload('title');
		$goodsData['brand']=$this->payload('brand');
		$goodsData['year']=$this->payload('year');
		$goodsData['images']=$this->payload('images');		
		$goodsData['add_time']=time();

		//品牌，年份，名称是否相同
		$goods=$this->db->where('brand',$goodsData['brand'])
						->where('year',$goodsData['year'])
						->where('title',$goodsData['title'])
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

	

	





	

		

	
}
