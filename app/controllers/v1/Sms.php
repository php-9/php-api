<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms extends Api_Controller {

	protected $noNeedLogin = ['index'];

	public function __construct(){
		parent::__construct();					
	}
	public function index()
	{	
		$this->load->library('httpservice');

		$username=trim($this->input->get('username'));//trim(null)为''

		if(!$username){
			$this->buildFailed('参数错误');
		}
		if($this->db->where('username',$username)->get('member')->row_array()){
			$this->buildFailed('手机号已存在');
		}

		//验证码
		$rand=rand(1000,9999);


		//短信接口地址
		$appid='C37117338';
		$apikey='12345678';
		$url = "http://106.ihuyi.com/webservice/sms.php?method=Submit";
		$post_data = "account={$appid}&password={$apikey}&mobile=".$username."&content=".rawurlencode("您的验证码是：".$rand."。请不要把验证码泄露给其他人。");
		

		$gets =  xml_to_array($this->httpservice->post($url,$post_data) );


		if(isset($gets['SubmitResult']['code']) && $gets['SubmitResult']['code']==2){//发送成功
		    //发送成功，入库
			$data['username']=$username;
			$data['addtime']=time();
			$data['rand']=$rand;
			if(!$this->db->insert('rand',$data) ){			
				$this->buildFailed('系统错误2');
			}

			$this->buildJson(0,'验证码已发送',[]);

		}
		$this->buildFailed($gets['SubmitResult']['msg']);	

	}
}
