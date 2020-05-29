<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//api接口
class Api_Controller extends CI_Controller{	

	protected $noNeedLogin = [];//无需登录的方法

	protected $userInfo=[];//登录用户

	public function __construct(){

		parent::__construct();
		//启用文件缓存
		$this->load->driver('cache',array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'my_'));

		if( !in_array( strtolower($this->router->fetch_method()) , $this->noNeedLogin ) ){//登录
			$this->doLogin();
		}
	}


	protected function response($code=504,$msg='fail',$data=[]){
		header('Content-Type:application/json; charset=utf-8');
		$res=array(
			'code'=>$code,
			'msg'=>$msg,
			'data'=>$data
			);
		exit(json_encode($res,JSON_UNESCAPED_UNICODE));
	}


	protected function success($data=[],$code=200,$msg='success'){
		$this->response($code,$msg,$data);
	}


	protected function fail($msg='fail',$code=504,$data=[]){
		$this->response($code,$msg,$data);
	}


	//验证登录
	protected function doLogin() {
		$this->load->library('jwt');

		if( empty($_SERVER['HTTP_TOKEN']) ){
			$this->fail('token无法获取');
		}

		$uid=$this->jwt->verifyToken($_SERVER['HTTP_TOKEN']);

		//jwt校验
		if(!$uid){
			$this->fail('请登录',501);
		}

		//数据库token校验
		if( !$u = $this->db->where('u_id',$uid)->where('token',$_SERVER['HTTP_TOKEN'])->get('admin_session')->row_array()){
			$this->fail('此帐号已在别处登录',502);
		}

		
		$this->userInfo['id']=$uid;
		$this->userInfo['username']=$u['username'];				
		
	}


	//获取playload数据
	//axios默认发送的payload json,要用php://input获取
	//参数，$index标识，$default默认输出
	public function payload($index='',$default=''){
		// 获取payload json数据，转换成数组形式
		$postData = file_get_contents('php://input');
		$requests = !empty($postData) ? json_decode($postData, true) : array();

		if($_SERVER['REQUEST_METHOD']=='GET' || !$requests ){
			return  array();
		}

		if($index==''){
			return $requests;
		}

		if(isset($requests[$index])){
			return $requests[$index];
		}

		return $default;
		
	}



	/*无限分类列表*/
	public function _list($arr,$pid=0,$level=0){

		static $res=array();
		foreach ($arr as $v) {
			if($v['pid']==$pid){
				$v['level']=$level;
				$res[]=$v;
				$this->_list($arr,$v['id'],$level+1);
			}
		}
		return $res;
	}

	/*无限分类树*/
	public function _tree($arr,$pid=0,$level=0){

		$res=[];
		foreach ($arr as $k => $v) {
			if ($v['pid'] == $pid){
                 $v['level']=$level;
                 
                 $v['child']=$this->_tree($arr,$v['id'],$level+1);
                 $res[]=$v;              
            }
		}
		return $res;


	}


	

	



  

    

}



