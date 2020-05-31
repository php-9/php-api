<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// 图片接收
// base64
class ImgUpload extends Api_Controller {

	protected $noNeedLogin=[];
	public function __construct(){
		parent::__construct();					
	}
	public function index(){	


		//接收base64数据
        $img= $this->payload('img');        
		$imgName=date('ymd-His-').uniqid().'-'.rand(10000,99999).'.png';

		$image = explode(',',$img);



		if(empty($image[1])){
			$this->fail('上传操作失败1');
		}

        $image = $image[1];


        $img_len = strlen($image);
		$file_size = $img_len - ($img_len/8)*2;
		$file_size = $file_size/1024;//kb

		//图片不能大于1M	
		if($file_size > 1024){
		  $this->fail('图片不能大于1M');	
		}
		

		//建立日期文件夹保存
		$file_folder='./uploads/goods/'.date('Y-m-d');
		if (!file_exists($file_folder)){
            mkdir ($file_folder,0777,true);            
        }

        $imageSrc=$file_folder.'/'.$imgName;

        $r = file_put_contents($imageSrc, base64_decode($image));

        $resData['url']=site_url().$imageSrc;
        $resData['name']='';

        if($r){
        	$this->success($resData);
        }
        $this->fail('上传操作失败2');

		
	}

}
