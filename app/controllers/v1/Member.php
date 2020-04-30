<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Api_Controller {

	protected $noNeedLogin = ['reg','login','forget'];

	public function __construct(){
		parent::__construct();					
	}
	public function index()
	{	
		if($this->userInfo){
			$resData['member']=$this->userInfo;
			$this->buildSuccess($resData);
		}
		$this->buildFailed('系统错误');
		
	}

	public function login(){
		$mobile=trim($this->input->post('mobile') );
		$pwd=trim($this->input->post('pwd') );
		if(!$mobile){
			$this->buildFailed('用户名错误');
		}
		if(!$pwd){
			$this->buildFailed('密码错误');
		}

		$member=$this->db->where(array('username'=>$mobile,'password'=>md5($pwd)) )->get('member')->row_array();

		if(!$member){
			$this->buildFailed('手机或密码错误！');
		}
		$res['token']=$this->jwt->getToken($member['id']);
		$this->buildSuccess($res);
	}

	public function reg(){


		$username=trim($this->input->post('username') );
		$password=trim($this->input->post('password') );
		$pid=trim($this->input->post('pid') );
		$rand=trim($this->input->post('rand') );


		if(!$username || !is_numeric($username) || strlen($username)!=11 ){			
			$this->buildFailed('用户名错误');
		}
		
		if(!$password){
			$this->buildFailed('密码错误');
		}

		if(!$pid){
			$this->buildFailed('推荐码错误');
		}

		$rand_res=$this->db->select('rand')->where('username',$username)->order_by('id','desc')->get('rand')->row_array();
		if(!isset($rand_res['rand']) || $rand!=$rand_res['rand'] ){
			$this->buildFailed('验证码错误');
		}

		if(isset($rand_res['addtime']) && ($rand_res['addtime']+600)<time() ){
			$this->buildFailed('验证码超时');
		}


		if(!$parent=$this->db->where('id',$pid)->get('member')->row_array()){
			$this->buildFailed('推荐码不存在');
		}

		if($member=$this->db->where('username',$username)->get('member')->row_array() ){//用户存在
			$this->buildFailed('用户已存在');
		}

		$config=$this->db->where('id',1)->get('setting')->row_array();

		$data['username']=$username;
		$data['password']=md5($password);
		$data['pid']=$pid;
		$data['regtime']=time();
		$data['end_time']=time() + $config['timelong'];//观看时长
				
		
		if(!$res=$this->db->insert('member',$data) ){
			$this->buildFailed('注册失败');
		}

		$this->buildSuccess();
		
	}


	public function forget(){//重设密码


		$username=trim($this->input->post('username') );
		$password=trim($this->input->post('password') );		
		$rand=trim($this->input->post('rand') );


		if(!$username || !is_numeric($username) || strlen($username)!=11 ){			
			$this->buildFailed('用户名错误');
		}
		
		if(!$password){
			$this->buildFailed('密码错误');
		}		

		$rand_res=$this->db->select('rand')->where('username',$username)->order_by('id','desc')->get('rand')->row_array();//验证码

		if(!isset($rand_res['rand']) || $rand!=$rand_res['rand'] ){
			$this->buildFailed('验证码错误');
		}

		if(isset($rand_res['addtime']) && ($rand_res['addtime']+600)<time() ){
			$this->buildFailed('验证码超时');
		}


		if(!$member=$this->db->where('username',$username)->get('member')->row_array() ){//用户存在
			$this->buildFailed('用户不存在');
		}
		
		$data['password']=md5($password);
		
		if(!$res=$this->db->where('username',$username)->update('member',$data) ){
			$this->buildFailed('密码重置失败');
		}

		$this->buildSuccess();
		
	}


	public function team($page=1){//团队列表
		$pagesize=20;
		$user=$this->userInfo;
		if(!$page || !$user){
			$this->buildFailed('系统错误');
		}		
		$son=$this->db
				->where('pid',$user['id'])
				->order_by('id','desc')
				->limit($pagesize,($page-1)*$pagesize)
				->get('member')
				->result_array();
		$data['count']=$this->db
				->where('pid',$user['id'])							
				->get('member')
				->num_rows();				
		$data['lists']=$son;
	    $this->buildSuccess($data);			
	}


	public function pay_record($page=1){//续费记录
		$pagesize=20;
		$user=$this->userInfo;
		if(!$page || !$user){
			$this->buildFailed('系统错误');
		}

		$where=['uid'=>$user['id'],'type'=>2];		
		$son=$this->db
				->where($where)
				->order_by('id','desc')
				->limit($pagesize,($page-1)*$pagesize)
				->get('order')
				->result_array();

		$where=['uid'=>$user['id'],'type'=>2,'state'=>1];//成功续费		
		$res=$this->db
				->select_sum('money','sum_money')
				->where($where)							
				->get('order')
				->row_array();				
		$data['count']=	$res['sum_money'] ? $res['sum_money']:0;

		$data['lists']=$son;
	    $this->buildSuccess($data);			
	}


	public function cash_record($page=1){//提现记录
		$pagesize=20;
		$user=$this->userInfo;
		if(!$page || !$user){
			$this->buildFailed('系统错误');
		}

		$where=['uid'=>$user['id'],'type'=>3];		
		$son=$this->db
				->where($where)
				->order_by('id','desc')
				->limit($pagesize,($page-1)*$pagesize)
				->get('order')
				->result_array();

		$where=['uid'=>$user['id'],'type'=>3,'state'=>1];//成功提现		
		$res=$this->db
				->select_sum('money','sum_money')
				->where($where)							
				->get('order')
				->row_array();				
		$data['count']=	$res['sum_money'] ? $res['sum_money']:0;				
		$data['lists']=$son;
	    $this->buildSuccess($data);			
	}


	public function cash($money){
		$user=$this->userInfo;
		if(!$user){
			$this->buildFailed('系统错误');
		}

		if($money<=0 || !is_numeric($money) ){
			$this->buildFailed('金额错误');
		}

		if($user['coin']<$money ){
			$this->buildFailed('金币不足');
		}
		$now=time();

		//构建订单
		$data['sn']=date('ymd-His',$now).'-'.rand(1000,9999);
		$data['uid']=$user['id'];
		$data['type']=3;//类型是提现
		$data['money']=$money;
		$data['coin']=bcsub($user['coin'],$money);//减法，后还有多少金币
		$data['addtime']=$now;
		$data['mark']=$user['cash_info'];//银行和帐号

		$data_u['coin']=bcsub($user['coin'],$money);//减法，后还有多少金币

		$this->db->db_debug = FALSE; //禁用错误		
		$this->db->trans_start();//事务开始
		
		$this->db->insert('order',$data);
		$this->db->where('id',$user['id'])->update('member',$data_u);
		  

		$this->db->trans_complete();//事务完成

		if ($this->db->trans_status() === FALSE){//事务失败		  
		    $this->buildFailed('系统错误');
		}

		$this->buildJson(0,'提现申请成功',[]);

	}


	public function play(){//播放判断登录过期
		//更新token,播放权限
		$returnData['token']='';
		$returnData['isplay']=0;
		if( $this->isLogin() ){//已登录
			$returnData['token']=$this->jwt->getToken($this->userInfo['id']);
			$returnData['isplay']=$this->checkUserTime() ? 1 : 0 ;			
		}
		//更新token,播放权限
		$this->buildJson(0,'success',$returnData);
	}


	public function get_set_cash(){//获取提现的设置

		$user=$this->userInfo;		

		if($user['cash_info']){
			$cash_info= explode(',', $user['cash_info']);
		}else{
			$cash_info=['支付宝',''];
		}
		

		$resData['bank']=$cash_info[0];
		$resData['account']=$cash_info[1];

		$this->buildJson(0,'success',$resData);

		

	}


	public function set_cash(){//设置提现收款帐号
		$bank=$this->input->post('selected');
		$account=$this->input->post('account');

		if(!$bank){
			$this->buildFailed('银行错误');
		}

		if(!$account){
			$this->buildFailed('收款帐号错误');
		}

		$data['cash_info']=$bank.','.$account;

		if($res=$this->db->where('id',$this->userInfo['id'])->update('member',$data) ){
			$resData['bank']=$bank;
			$resData['account']=$account;
			$this->buildSuccess($resData);
		}



		$this->buildFailed('系统错误');

	}
	

	
}
