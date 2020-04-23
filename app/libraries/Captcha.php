<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//汉字转拼音
class Captcha{

    private $secret = "ffdsfsd@4_45";

    public function getImg(){

        //创建一个大小为 150*38 的验证码  
        $image = imagecreatetruecolor(150, 38);  
        $bgcolor = imagecolorallocate($image, 255, 255, 255);  
        imagefill($image, 0, 0, $bgcolor);  
          
        $captch_code = '';  
        for ($i = 0; $i < 4; $i++) {  
            $fontsize = 5;  
            $fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));  
            $data = 'abcdefghijkmnpqrstuvwxy3456789';  
            $fontcontent = substr($data, rand(0, strlen($data) - 1), 1);  
            $captch_code .= $fontcontent;  
            $x = ($i * 150 / 4) + rand(5, 10);  
            $y = rand(5, 10);  
            imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);  
        }  
        //就生成的验证码保存到session  
        $_SESSION['authcode'] = $captch_code;  
         
        //在图片上增加点干扰元素  
        for ($i = 0; $i < 200; $i++) {  
            $pointcolor = imagecolorallocate($image, rand(180, 200), rand(180, 200), rand(180, 200));  
            imagesetpixel($image, rand(1, 149), rand(1, 37), $pointcolor);  
        }  
          
        //在图片上增加线干扰元素  
        for ($i = 0; $i < 3; $i++) {  
            $linecolor = imagecolorallocate($image, rand(180, 220), rand(180, 220), rand(180, 220));  
            imageline($image, rand(1, 149), rand(1, 37), rand(1, 149), rand(1, 37), $linecolor);  
        }

        //设置头  
        header('content-type:image/png');  
        imagepng($image);  
        imagedestroy($image);

    }
}