<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data1 extends Admin_Controller {

	public function __construct(){
		parent::__construct();		
		
	}

	public function index(){

		

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
		$config['base_url']=admin_url('data1/index'."
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



		
		$this->load->view('admin/data1.htm',$data);
		
	}


	// public function add(){
	// 	$this->load->view('admin/data1_add.htm');
	// }

	// public function add_pass(){

	// 	$custom=$this->input->post('custom',true);
	// 	$drug=$this->input->post('drug',true);
	// 	$spec=$this->input->post('spec',true);
	// 	$factory=$this->input->post('factory',true);
	// 	$num=$this->input->post('num',true);

	// 	$custom=trim($custom);
	// 	$drug=trim($drug);
	// 	$spec=trim($spec);
	// 	$factory=trim($factory);
	// 	$num=trim($num);

	// 	if(!$custom){
	// 		$this->msg('请输入客户名称');
	// 	}


	// 	$login_user=$_SESSION['user'];

	// 	$data['custom']=$custom;
	// 	$data['drug']=$drug;
	// 	$data['spec']=$spec;
	// 	$data['factory']=$custom;
	// 	$data['num']=$num;
	// 	$data['addtime']=time();
	// 	$data['edittime']=time();
	// 	$data['u_id']=$login_user['id'];

		

	// 	if( $this->db->insert('data1',$data) ){
	// 		//成功
	// 		$login_user=$_SESSION['user'];			
	// 		//日志
	// 		$logArr['ip']=$this->input->ip_address();
	// 		$logArr['username']=$login_user['username'];
	// 		$logArr['con']='数据表1 (id:'.$this->db->insert_id().')添加';
	// 		$logArr['agent']=$this->agent->browser().','.$this->agent->version();//浏览器信息
	// 		$logArr['addtime']=time();
	// 		$logArr['type']=3;//添加数据
	// 		$this->db->insert('log',$logArr);
			
	// 		$this->msg('数据添加成功',site_url('data1/index'));
	// 	}

	// 	$this->msg('数据添加失败');
	// }

	public function edit($id){
		$data['data1']=$this->db->where('id',$id)->get('data1')->row_array();
		$this->load->view('admin/data1_edit.htm',$data);
	}

	public function edit_pass($id){

		$custom=$this->input->post('custom',true);
		$drug=$this->input->post('drug',true);
		$spec=$this->input->post('spec',true);
		$factory=$this->input->post('factory',true);
		$num=$this->input->post('num',true);

		$custom=trim($custom);
		$drug=trim($drug);
		$spec=trim($spec);
		$factory=trim($factory);
		$num=trim($num);

		if(!$custom){
			$this->msg('请输入客户名称');
		}

		$login_user=$_SESSION['admin'];

		$data['custom']=$custom;
		$data['drug']=$drug;
		$data['spec']=$spec;
		$data['factory']=$custom;
		$data['num']=$num;		
		$data['edittime']=time();
		$data['u_id']=$login_user['id'];


		

		if( $this->db->where('id',$id)->update('data1',$data) ){

			//成功					
			//日志
			$logArr['ip']=$this->input->ip_address();
			$logArr['username']=$login_user['username'];
			$logArr['con']='数据表1 (id:'.$id.')编辑';
			$logArr['agent']=$this->agent->browser().','.$this->agent->version();//浏览器信息
			$logArr['addtime']=time();
			$logArr['type']=3;//编辑数据
			$this->db->insert('admin_log',$logArr);

			$this->msg('数据编辑成功',admin_url('data1/edit/'.$id));
		}

		$this->msg('数据编辑失败');
		
	}


	public function del($id){

		$data['isdel']=1;

		if( $this->db->where('id',$id)->update('data1',$data) ){

			//成功
			$login_user=$_SESSION['admin'];			
			//日志
			$logArr['ip']=$this->input->ip_address();
			$logArr['username']=$login_user['username'];
			$logArr['con']='数据表1 (id:'.$id.')删除';
			$logArr['agent']=$this->agent->browser().','.$this->agent->version();//浏览器信息
			$logArr['addtime']=time();
			$logArr['type']=3;//删除数据
			$this->db->insert('admin_log',$logArr);

			$this->msg('已删除',admin_url('data1/index'));
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
			$this->db->where('id', $v)->delete('data1');			
		}

		$this->msg('批量删除操作完成',$_SERVER['HTTP_REFERER'] );
	}


	//导入数据
	public function import(){
		$this->load->view('admin/data1_import.htm');
	}


	public function import_pass(){

		if(!$_FILES['file']['name']){
			$this->msg('系统错误');
		}

		$tmp_file = $_FILES['file']['tmp_name'];
		$file_types = explode('.', $_FILES['file']['name']);
		$file_type = $file_types[count($file_types)-1];
		
		//判断是否为excel文件
		if (strtolower($file_type) != 'xls') {
			echo "不是xls文件，请重新上传！";
		}

		//设置上传路径
		$savePath = "./uploads/";
		//文件命名
		$str = date('ymdHis').rand(1000,9999);
		$file_name = $str.".".$file_type;
		if (!copy($tmp_file,$savePath.$file_name)) {
			$this->msg('上传失败');
		}

		$startTime = microtime(); //返回当前时间的Unix 时间戳
		//加载PHPExcel的类
		$this->load->library('PHPExcel');
		$this->load->library('PHPExcel/IOFactory');
		//创建PHPExcel实例
		

		/*读取excel文件，并进行相应处理*/

		$fileName = $savePath.$file_name;

		if (!file_exists($fileName)) {

		    $this->msg("文件".$fileName."不存在");

		}
		$objPHPExcel = new PHPExcel();
		//$objProps = $objPHPExcel->getProperties();

		$objReader = IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load($fileName);

		//获取工作表的数目
		$sheetCount = $objPHPExcel->getSheetCount();
		if( $sheetCount!=1 ){
			$this->msg('数据文件格式错误(sheet)');
		}

		//要导入的工作表下标，sheet1=0、sheet2=1，以此类推
		//$sheetNum	= 0;
		//读取工作表文件
		//$_currentSheet = $objPHPExcel->getSheet(0) ;
		//获取表格行数
		$rowCount = $objPHPExcel->getActiveSheet()->getHighestRow();
		//获取表格列数
		$columnCount = $objPHPExcel->getActiveSheet()->getHighestColumn();
		//列数不对
		if( $columnCount!='E' ){
			$this->msg('数据文件格式错误(column)');
		}
		$data=array();
		//从第几行开始到第几行结束，一般从第二行开始，第一行是字段名
		for($i = 2; $i <= ($rowCount+1); $i++){
			//取各单元格内容
			$a = $objPHPExcel->getSheet( 0 )->getCell('A'.$i)->getCalculatedValue();
			$b = $objPHPExcel->getSheet( 0 )->getCell('B'.$i)->getCalculatedValue();
			$c = $objPHPExcel->getSheet( 0 )->getCell('C'.$i)->getCalculatedValue();
			$d = $objPHPExcel->getSheet( 0 )->getCell('D'.$i)->getCalculatedValue();
			$e = $objPHPExcel->getSheet( 0 )->getCell('E'.$i)->getCalculatedValue();
			if($a){//software_sort
				$data[] = array('custom'=>$a, "drug"=>$b, 'spec'=>$c, 'factory'=>$d ,'num'=>$e);
			}
	 			
		}

		$user=$_SESSION['admin'];

		//事务
		$this->db->trans_start();
		foreach ($data as $v) {
			$arr=array();
			$arr=$v;
			$arr['addtime']=time();
			$arr['u_id']=$user['id'];
			$this->db->insert('data1',$arr);
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
		    $this->msg('数据导入失败');
		}

		$this->msg('数据导入成功',admin_url('data1/index'));

		//得到二维数组结果集
		//var_dump($data);
	
		//echo "<div>总共消耗的时间为：".round(((microtime() - $startTime)),3)."秒</div>";



	}

	

}
