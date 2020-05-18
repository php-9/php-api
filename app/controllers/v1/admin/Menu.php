<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends Api_Controller {

	protected $noNeedLogin=[];

	public function __construct(){
		parent::__construct();					
	}

	public function index(){	


		$arr=$this->_tree( $this->db->get('admin_rule')->result_array(),0);

		//去掉没有权限节点的菜单
		$tree=[];
		foreach ($arr as $v) {
			if($v['child']){
				$tree[]=$v;
			}
		}
		
		$this->success($tree);

	}


		

	
}
