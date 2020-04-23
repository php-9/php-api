<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends Admin_Controller{

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{			
		$data['config']=$this->db->order_by('flag','asc')->get('config')->result_array();
		$this->load->view('admin/config.htm',$data);
	}

	public function add(){
		$this->load->view('admin/config_add.htm');
	}

	public function add_pass(){
		$arr=$this->input->post();
		
		$res=$this->db->insert('config',$arr);

		if($res){			
			$this->msg('操作成功',admin_url('config'));
		}

		$this->msg('操作失败');
	}

	public function edit($id){
		$data['config']=$this->db->where('id',$id)->get('config')->row_array();
		$this->load->view('admin/config_edit.htm',$data);
	}


	public function edit_pass($id){
		$arr=$this->input->post();
		
		$res=$this->db->where('id',$id)->update('config',$arr);
		if($res){			
			$this->msg('操作成功',admin_url('config'));
		}

		$this->msg('操作失败');
	}

	public function set(){
		$data['config']=$this->db->get('config')->result_array();
		$this->load->view('admin/config_set.htm',$data);
	}

	public function set_pass(){

		
		//获取所有类型
		$varsArr=$this->db->get('config')->result_array();	

		$cfgType=[];//非文件
		$fileArr=[];//文件类型
		foreach ($varsArr as $v) {
			if($v['type']=='img'){
				$fileArr[]=$v['key'];
			}else{
				$cfgType[$v['key']]=$v['type'];
			}			
			
		}
		$data=[];

		//文件上传
		foreach ($fileArr as $v) {
			//图片上传
			$file_folder='uploads/'.date('Ymd');//建立日期文件夹保存
			if (!file_exists($file_folder)){
			    mkdir ($file_folder);            
			}
			$pars['upload_path'] = $file_folder;
			$pars['allowed_types'] = 'gif|jpg|png';
			$pars['overwrite']=FALSE;
			$pars['encrypt_name']=TRUE;
			$pars['max_size'] = '1024';   
			$this->load->library('upload', $pars);        
			if($this->upload->do_upload($v) )
			{
			    $file_data=$this->upload->data();
			    $data[$v]=base_url().$file_folder.'/'.$file_data['file_name'];
			    
			}
			//图片上传
		}
				


		//post提交的数据
		$postArr=$this->input->post();
		foreach ($postArr as $k=>$v) {			
			//判断字段的类型
			switch ($cfgType[$k]) {
				case 'text':
					$data[$k]=$v;
					break;
				case 'textarea':
					$data[$k]=$v;
					break;
				case 'boolean':
					$data[$k]=$v;
					break;
				case 'select':
					$data[$k]=$v;
					break;
						
				
			}//判断字段的类型
			
		}

		foreach ($data as $k=>$v) {
			$this->db->where('key',$k)->update('config',array('value'=>$v));
		}

		
		$this->msg('操作成功',admin_url('config/set'));

		
		

	}

	public function del($id){
		$res=$this->db->where('id',$id)->delete('config');

		if($res){
			$this->msg('操作成功',admin_url('config/index'));
		}

		$this->msg('操作失败');
	}
	


}
