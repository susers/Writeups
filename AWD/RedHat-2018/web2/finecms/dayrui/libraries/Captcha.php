<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

 /* v3.1.0  */
	
/**
 * 验证码
 */
 
class Captcha {

    public $width = 100;	//验证码的宽度
    public $height = 30;	//验证码的高
    public $font_color;	//设置字体色
    public $charset = 'abcdefghkmnprstuvwyzABCDEFGHKMNPRSTUVWYZ23456789';	//设置随机生成因子
    public $background = '#EDF7FF';	//设置背景色
    public $code_len = 4; //生成验证码字符数
    public $font_size = 14; //字体大小
    private $img; //图片内存
    private $code; //验证码
    private $font;	//设置字体的地址
    private $x_start; //文字X轴开始的地方

    function __construct() {
        $this->font = BASEPATH.'fonts/texb.ttf';
    }

    /**
     * 生成随机验证码。
     */
    protected function creat_code() {
	
        $code = '';
        $charset_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->code_len; $i++) {
            $code .= $this->charset[rand(1, $charset_len)];
        }
		
        $this->code = strtolower($code);
    }

    /**
     * 获取验证码
     */
    public function get_code() {
	
		if (!$this->code) $this->creat_code();
		
        return $this->code;
    }

    /**
     * 生成图片
     */
    public function doimage($mode = 0) {
	
        $this->img = imagecreatetruecolor($this->width, $this->height);
		
        if (!$this->font_color) {
            $this->font_color = imagecolorallocate($this->img, rand(0, 156), rand(0, 156), rand(0, 156));
        } else {
            $this->font_color = imagecolorallocate($this->img, hexdec(substr($this->font_color, 1, 2)), hexdec(substr($this->font_color, 3, 2)), hexdec(substr($this->font_color, 5, 2)));
        }
		
        //设置背景色
        $background = imagecolorallocate($this->img, hexdec(substr($this->background, 1, 2)), hexdec(substr($this->background, 3, 2)), hexdec(substr($this->background, 5, 2)));
        //画一个柜形，设置背景颜色。
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $background);
		
        $this->creat_font();
        $this->creat_line();
        $this->output($mode);
    }

    /**
     * 生成文字
     */
    private function creat_font() {
	
        $x = $this->width / $this->code_len;
		
        for ($i = 0; $i < $this->code_len; $i++) {
            imagettftext($this->img, $this->font_size, rand(-30, 30), $x * $i + rand(0, 5), $this->height / 1.4, $this->font_color, $this->font, $this->code[$i]);
            if ($i == 0) $this->x_start = $x * $i + 5;
        }
    }

    /**
     * 画线
     */
    private function creat_line() {
	
        imagesetthickness($this->img, 3);
        $xpos = ($this->font_size * 2) + rand(-5, 5);
        $width = $this->width / 2.66 + rand(3, 10);
        $height = $this->font_size * 3.14;
		
        if (rand(0, 100) % 2 == 0) {
            $start = rand(0, 66);
            $ypos = $this->height / 3 - rand(10, 30);
            $xpos += rand(5, 15);
        } else {
            $start = rand(180, 246);
            $ypos = $this->height / 3 + rand(10, 30);
        }
		
        $end = $start + rand(100, 210);
        $font_color = imagecolorallocate($this->img, 222, 222, 222);
		
        imagearc($this->img, $xpos, $ypos, $width, $height, $start, $end, $font_color);
    }

    /**
     * 输出图片
     */
    private function output($mode = 0) {
	
		ob_clean(); //关键代码，防止出现'图像因其本身有错无法显示'的问题。
		
        if ($mode) {
            header('content-type:image/jpeg');
            imagejpeg($this->img, '', 70);
        } else {
            header("content-type:image/png\r\n");
            imagepng($this->img);
        }
		
        imagedestroy($this->img);
    }

}