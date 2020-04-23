<?php
header("Content-type: text/html; charset=utf-8");


$uploaddir = './up/';
function rname($name){//重命名
	$file_arr=explode('.', $name);
	$file_str='.'.$file_arr[ count($file_arr)-1 ];
	$file_bname=time().rand(000,999);
	return $file_bname.$file_str;
}
if( isset($_FILES['file']) ){
	$fs=$_FILES['file'];//上传一个或多个文件
	$json=array();
	$json['data']=array();//准备文件数据
	$json['code']=1;
	$json['msg']='';//提示信息
	//判断有多少个文件
	if($fs){//存在数组
		foreach ($fs['name'] as $k=>$v) {
			if($fs['type'][$k]=='image/jpeg' or $fs['type'][$k]=='image/png'){//如果为图片文件
				$item=array();
				$item['name']=rname($fs['name'][$k]);
				$item['name_old']=$fs['name'][$k];
				$item['type']=$fs['type'][$k];
				$item['tmp_name']=$fs['tmp_name'][$k];
				$item['error']=$fs['error'][$k];
				$item['size']=$fs['size'][$k];
				$json['data'][]=$item;
			}
			
		}
		
	}


	$error=0;
	if($json['data']){//有数据
		foreach ($json['data'] as $v) {//循环文件上去到服务器动作
			if (!move_uploaded_file($v['tmp_name'], $uploaddir . $v['name'])) {
				$error=1;
			}
		}
	}else{
		$json['code']=0;//错误标志
		$json['msg']='没有数据';
	}


	if(!$error){//复制不出错	
		echo json_encode($json);
	}else{
		$json['msg']='复制数据到服务器不成功';
		$json['code']=0;//错误标志
		echo json_encode($json);
	}
}else{
	$json['code']=0;//错误标志
	$json['msg']='文件上传失败！';
}


?>