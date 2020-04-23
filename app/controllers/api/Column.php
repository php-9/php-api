<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Column extends Api_Controller {

	protected $noNeedLogin = ['index_article'];

	public function __construct(){
		parent::__construct();					
	}
	public function index()
	{	
		
		
	}

	public function index_article(){
		$id=trim($this->input->get('id') );

		if(!$id){
			$this->buildFailed('文章id不存在');
		}

		$column=$this->db->where('id',$id)->get('column')->row_array();
		if(!$column){
			$this->buildFailed('文章错误!');
		}

		$resData['name']=$column['name'];
		$resData['body']=$column['body'];

		$this->buildSuccess($resData);

	}



	
	
}
