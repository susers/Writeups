<?php
/**
 * 图像处理
 */
class Dthumb {

    var $w_pct = 100;
    var $w_quality = 80;
    var $w_minwidth = 300;
    var $w_minheight = 300;

    private function info($img) {

        $imageinfo = getimagesize($img);
        if($imageinfo === false) {
            return false;
        }

        $imagetype = strtolower(substr(image_type_to_extension($imageinfo[2]),1));
        $imagesize = filesize($img);
        $info = array(
            'width'=>$imageinfo[0],
            'height'=>$imageinfo[1],
            'type'=>$imagetype,
            'size'=>$imagesize,
            'mime'=>$imageinfo['mime']
        );

        return $info;
    }

    private function getpercent($srcwidth,$srcheight,$dstw,$dsth) {

        if (empty($srcwidth) || empty($srcheight) || ($srcwidth <= $dstW && $srcheight <= $dstH)) {
            $w = $srcwidth ;
        }
		
        $h = $srcheight;

        if ((empty($dstw) || $dstw == 0)  && $dsth > 0 && $srcheight > $dsth) {
            $h = $dsth;
            $w = round($dsth / $srcheight * $srcwidth);
        } elseif ((empty($dsth) || $dsth == 0) && $dstw > 0 && $srcwidth > $dstw) {
            $w = $dstw;
            $h = round($dstw / $srcwidth * $srcheight);
        } elseif ($dstw > 0 && $dsth > 0) {
            if (($srcwidth / $dstw) < ($srcheight / $dsth)) {
                $w = round($dsth / $srcheight * $srcwidth);
                $h = $dsth;
            } elseif (($srcwidth / $dstw) > ($srcheight / $dsth)) {
                $w = $dstw;
                $h = round($dstw / $srcwidth * $srcheight );
            } else {
                $h = $dstw;
                $w = $dsth;
            }
        }

        $array['w']  = $w;
        $array['h']  = $h;

        return $array;
    }

    public function thumb($image, $filename = '', $maxwidth = 200, $maxheight = 200, $suffix='', $autocut = 0) {

        if (!$this->check($image)) {
            return false;
        }

        $info = $this->info($image);
        if ($info === false) {
            return false;
        }

        $srcwidth  = $info['width'];
        $srcheight = $info['height'];
        $pathinfo = pathinfo($image);

        $type = $pathinfo['extension'];
        if (!$type) {
            $type = $info['type'];
        }
        $type = strtolower($type);
        unset($info);

        $creat_arr = $this->getpercent($srcwidth,$srcheight,$maxwidth,$maxheight);
        $createwidth = $width = $creat_arr['w'];
        $createheight = $height = $creat_arr['h'];

        $psrc_x = $psrc_y = 0;
        if ($autocut && $maxwidth > 0 && $maxheight > 0) {
            if ($maxwidth/$maxheight<$srcwidth/$srcheight && $maxheight>=$height) {
                $width = $maxheight/$height*$width;
                $height = $maxheight;
            } elseif ($maxwidth/$maxheight>$srcwidth/$srcheight && $maxwidth>=$width) {
                $height = $maxwidth/$width*$height;
                $width = $maxwidth;
            }
            $createwidth = $maxwidth;
            $createheight = $maxheight;
        }

        $createfun = 'imagecreatefrom'.($type=='jpg' ? 'jpeg' : $type);
        $srcimg = $createfun($image);
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $thumbimg = imagecreatetruecolor($createwidth, $createheight);
        } else {
            $thumbimg = imagecreate($width, $height);
        }

        if (function_exists('imagecopyresampled')) {
            imagecopyresampled($thumbimg, $srcimg, 0, 0, $psrc_x, $psrc_y, $width, $height, $srcwidth, $srcheight);
        } else {
            imagecopyresized($thumbimg, $srcimg, 0, 0, $psrc_x, $psrc_y, $width, $height,  $srcwidth, $srcheight);
        }

        if ($type=='gif' || $type=='png') {
            $background_color = imagecolorallocate($thumbimg,  0, 255, 0);  //  指派一个绿色
            imagecolortransparent($thumbimg, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
        }

        if ($type=='jpg' || $type=='jpeg') {
            imageinterlace($thumbimg, $this->interlace);
        }

        $imagefun = 'image'.($type=='jpg' ? 'jpeg' : $type);
        if (empty($filename)) {
            $filename  = substr($image, 0, strrpos($image, '.')).$suffix.'.'.$type;
        }

        $imagefun($thumbimg, $filename);
        imagedestroy($thumbimg);
        imagedestroy($srcimg);

        return $filename;
    }

    public function display($filename) {

        // 图片属性
        $types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
        $vals = getimagesize($filename);
        $mime = (isset($types[$vals[2]])) ? 'image/'.$types[$vals[2]] : 'image/jpg';
        $type = $vals[2];

        // 输出图片
        ob_clean();
        header('Content-Disposition: filename='.$filename.';');
        header('Content-Type: '.$mime);
        header('Content-Transfer-Encoding: binary');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');

        switch ($type) {
            case 1	:
                $im = imagecreatefromgif($filename);
                imagegif($im);
                break;
            case 2	:
                $im = imagecreatefromjpeg($filename);
                imagejpeg($im, NULL, 90);
                break;
            case 3	:
                $im = imagecreatefrompng($filename);
                // 解决png透明问题
                $resize_im = imagecreatetruecolor($vals[0], $vals[1]);
                imagesavealpha($im, true);
                imagealphablending($resize_im, false);
                imagesavealpha($resize_im, true);
                imagecopyresampled($resize_im, $im, 0, 0, 0, 0, $size, $size, $imginfo[0], $imginfo[1]);
                imagepng($im);
                break;
        }

        @imagedestroy($im);
    }

    public function html($filename) {

        // 图片属性
        $types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
        $vals = getimagesize($filename);
        $mime = (isset($types[$vals[2]])) ? 'image/'.$types[$vals[2]] : 'image/jpg';
        $type = $vals[2];



        switch ($type) {
            case 1	:
                $im = imagecreatefromgif($filename);
                imagegif($im, $filename);
                break;
            case 2	:
                $im = imagecreatefromjpeg($filename);
                imagejpeg($im, $filename, 90);
                break;
            case 3	:
                $im = imagecreatefrompng($filename);
                // 解决png透明问题
                $resize_im = imagecreatetruecolor($vals[0], $vals[1]);
                imagesavealpha($im, true);
                imagealphablending($resize_im, false);
                imagesavealpha($resize_im, true);
                imagepng($im, $filename);
                break;
        }

    }

    private function check($image) {
        return extension_loaded('gd') && preg_match("/\.(jpg|jpeg|gif|png)/i", $image, $m) && is_file($image) && function_exists('imagecreatefrom'.($m[1] == 'jpg' ? 'jpeg' : $m[1]));
    }

}