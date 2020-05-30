<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller {

	protected $noNeedLogin=[];
	public function __construct(){
		parent::__construct();					
	}
	public function index(){	
		
		
		//建立日期文件夹保存
		$file_folder='./uploads/'.date('Ymd');
		if (!file_exists($file_folder)){
            mkdir ($file_folder);            
        }
		$config['upload_path'] = $file_folder;
		$config['allowed_types'] = 'gif|jpg|png';
		$config['overwrite']=FALSE;
		$config['encrypt_name']=TRUE;
		$config['max_size'] = '1024';	
		$this->load->library('upload', $config);		
		if($this->upload->do_upload('file') )//成功
		{
			$file_data=$this->upload->data();
			$fileArr=array();
			$fileArr=base_url().$file_folder.'/'.$file_data['file_name'];
			
			echo json_encode(array(
				'code'=>1,
				'msg'=>"上传成功",
				'data'=>$fileArr
			));
			
		}else{//失败

			echo json_encode(array(
				'code'=>0,
				'data'=>array(),
				'msg'=>'上传失败'
			));
		}
		
	}

}
