<?php

defined('BASEPATH') OR exit('No direct script access allowed');


if ( ! function_exists('admin_url'))//后台控制器路径
{
	
	function admin_url($uri = '', $protocol = NULL)
	{	
		$admin_dir='wuadmmm';//后台控制器目录
		return get_instance()->config->site_url($admin_dir.'/'.$uri, $protocol);
	}
}


//将 xml数据转换为数组格式。
if(! function_exists('xml_to_array'))
{	
	function xml_to_array($xml){
	    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
	    if(preg_match_all($reg, $xml, $matches)){
	        $count = count($matches[0]);
	        for($i = 0; $i < $count; $i++){
	        $subxml= $matches[2][$i];
	        $key = $matches[1][$i];
	            if(preg_match( $reg, $subxml )){
	                $arr[$key] = xml_to_array( $subxml );
	            }else{
	                $arr[$key] = $subxml;
	            }
	        }
	    }
	    return $arr;
	}
}


/**
 * 中文字符串的截取
 *
 * @access: public
 * @author: linyong
 * @param: string，$str，原字符串
 * @param: int，$len ，截取的长度
 * @return: string
 */
if(! function_exists('utf_substr'))
{
	function utf_substr($str,$len){
	    for($i=0;$i<$len;$i++){
	        $temp_str=substr($str,0,1);
	        if(ord($temp_str) > 127){
	            $i++;
	            if($i<$len){
	                $new_str[]=substr($str,0,3);
	                $str=substr($str,3);
	            }
	        }else{
	            $new_str[]=substr($str,0,1);
	            $str=substr($str,1);
	        }
	    }
	    return join($new_str);
	}
}	