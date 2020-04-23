<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//api接口
class Api_Controller extends CI_Controller{	

	protected $noNeedLogin = [];//无需登录的方法

	protected $loginUser=[];//登录用户

	public function __construct(){

		parent::__construct();
		//启用文件缓存
		$this->load->driver('cache',array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'my_'));
		
		if( !in_array( strtolower($this->router->fetch_method()) , $this->noNeedLogin ) ){//登录
			if(!$this->isLogin()){
				$this->fail('未登录',505);
			}
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
	protected function isLogin() {
		$this->load->library('jwt');

		if( empty($_SERVER['HTTP_TOKEN']) ){
			$this->fail('token找不到');
		}

		$uid=$this->jwt->verifyToken($_SERVER['HTTP_TOKEN']);

		if($uid){//jwt校验成功
			
			if( $this->db->where('uid',$uid)->where('token',$_SERVER['HTTP_TOKEN'])->get('user_token')->row_array() ){//校验登录列表token

				$this->loginUser['id']=$uid;
				return true;
			}
			
			
		}
		
		return false;
	}


	

	



  

    

}



