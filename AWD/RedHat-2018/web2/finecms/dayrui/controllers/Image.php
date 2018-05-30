<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



class Image extends M_Controller {

    /**
     * 图片处理类
     */
    public function __construct() {
        parent::__construct();
        $this->output->enable_profiler(FALSE);
    }

    /**
     * 图片处理2
     */
    public function thumb2() {

        list($id, $width, $height, $autocut) = explode('-', $this->input->get('p'));

        $id or $id = $this->input->get('id');
        $width or $width = $this->input->get('width');
        $height or $height = $this->input->get('height');
        $autocut or $autocut = $this->input->get('autocut');

        $this->load->library('dthumb');

        // 输出图片的地址
        $display = WEBPATH.'cache/thumb/'.md5("index.php?c=image&m=thumb&p=$id-$width-$height-$autocut").'.jpg';

        // 存在缩略图时就输出图片
        if (is_file($display)) {
            $this->dthumb->display($display);
            return;
        }

        // 是附件id时
        if (is_numeric($id)) {
            $info = get_attachment($id);
            // 远程图片下载到本地缓存目录
            if (isset($info['remote']) && $info['remote']) {
                $file = WEBPATH.'cache/attach/'.time().'_'.basename($info['attachment']);
                file_put_contents($file, dr_catcher_data($info['attachment']));
            } else {
                $file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
            }
            unset($info);
        }

        // 图片不存在时调用默认图片
        if (!is_file($file)) {
            $file = WEBPATH.'statics/admin/images/nopic.gif';
        }

        // 生成缩略图
        $this->dthumb->thumb($file, $display, $width, $height, '', $autocut);

        // 输出缩略图
        $this->dthumb->display($display);
    }

    /**
     * 图片处理
     */
    public function thumb() {

        // 参数解析
        list($id, $width, $height, $water, $size) = explode('-', $this->input->get('p'));

        // 接收参数
        $id or $id = $this->input->get('id');
        $width or $width = $this->input->get('width');
        $height or $height = $this->input->get('height');
        $water or $water = $this->input->get('water');
        $size or $size = (int)$this->input->get('size');

        // 缓存文件
        $thumb_file = WEBPATH.'cache/thumb/'.md5("index.php?c=image&m=thumb&p=$id-$width-$height-$water-$size").'.jpg';
/*
        // 存在缩略图时就输出图片
        if (is_file($thumb_file)) {
            $this->thumb_display($thumb_file);
            return;
        }*/

        $info = get_attachment($id); // 图片信息

        if ($info && in_array($info['fileext'], array('jpg', 'gif', 'png', 'jpeg'))) {
            // 远程图片下载到本地缓存目录
            if (isset($info['remote']) && $info['remote']) {
                $file = WEBPATH.'cache/attach/'.SYS_TIME.'_'.basename($info['attachment']);
                if ($size) {
                    $info['attachment'] =  str_replace(
                        basename($info['attachment']),
                        basename($info['attachment'], '.'.$info['fileext']).'_'.$size.'.'.$info['fileext'],
                        $info['attachment']
                    );
                }
                file_put_contents($file, dr_catcher_data($info['attachment']));
            } else {
                if ($size) {
                    $file_size =  str_replace(
                        basename($info['attachment']),
                        basename($info['attachment'], '.'.$info['fileext']).'_'.$size.'.'.$info['fileext'],
                        $info['attachment']
                    );
                    $file = is_file(SYS_UPLOAD_PATH.'/'.$file_size) ? SYS_UPLOAD_PATH.'/'.$file_size : SYS_UPLOAD_PATH.'/'.$info['attachment'];
                } else {
                    $file = SYS_UPLOAD_PATH.'/'.$info['attachment'];
                }
            }
            $file = !is_file($file) ? WEBPATH.'statics/admin/images/nopic.gif' : $file;
        } else {
            $file = WEBPATH.'statics/admin/images/nopic.gif';
        }

        // 处理宽高
        list($_width, $_height) = @getimagesize($file);
        $width = $width ? $width : $_width;
        $height = $height ? $height : $_height;

        // 站点配置信息
        $site = $this->get_cache('siteinfo', $info['siteid']);

        // 生成新图参数
        $config['width'] = $width;
        $config['height'] = $height;
        $config['create_thumb'] = TRUE;
        $config['source_image'] = $file;
        $config['new_image'] = $thumb_file;
        $config['thumb_marker'] = '';
        $config['image_library'] = 'gd2';
        $config['dynamic_output'] = FALSE; // 输出到浏览器
        $config['maintain_ratio'] = (bool)$site['SITE_IMAGE_RATIO']; // 使图像保持原始的纵横比例

        // 水印判断
        if (isset($info['remote']) && $info['remote'] && !$site['SITE_IMAGE_REMOTE']
            ? FALSE : ((bool)$site['SITE_IMAGE_WATERMARK'] && $water ? TRUE : FALSE)) {
            // 水印参数
            $config['wm_type'] = $site['SITE_IMAGE_TYPE'] ? 'overlay' : 'text';
            $config['wm_vrt_offset'] = $site['SITE_IMAGE_VRTOFFSET'];
            $config['wm_hor_offset'] = $site['SITE_IMAGE_HOROFFSET'];
            $config['wm_vrt_alignment'] = $site['SITE_IMAGE_VRTALIGN'];
            $config['wm_hor_alignment'] = $site['SITE_IMAGE_HORALIGN'];
            // 文字模式
            $config['wm_text'] = $site['SITE_IMAGE_TEXT'];
            $config['wm_font_size'] = $site['SITE_IMAGE_SIZE'];
            $config['wm_font_path'] = WEBPATH.'statics/watermark/'.($site['SITE_IMAGE_FONT'] ? $site['SITE_IMAGE_FONT'] : 'default.ttf');
            $config['wm_font_color'] = $site['SITE_IMAGE_COLOR'] ? str_replace('#', '', $site['SITE_IMAGE_COLOR']) : '#000000';
            // 图片模式
            $config['wm_opacity'] = $site['SITE_IMAGE_OPACITY'] ? $site['SITE_IMAGE_OPACITY'] : 80;
            $config['wm_overlay_path'] = WEBPATH.'statics/watermark/'.($site['SITE_IMAGE_OVERLAY'] ? $site['SITE_IMAGE_OVERLAY'] : 'default.png');
            // 生成图片的临时文件
            $this->load->library('image_lib', $config);
            $this->image_lib->resize($thumb_file);
            // 打开临时文件再水印
            $this->image_lib->full_src_path = $config['new_image'];
            $this->image_lib->watermark();
        } else {
            // 默认模式
            $this->load->library('image_lib', $config);
            $this->image_lib->resize($thumb_file);
        }

        $this->thumb_display(!is_file($thumb_file) ? WEBPATH.'statics/admin/images/nopic.gif' : $thumb_file);

        // 删除远程附件临时文件
        if (isset($info['remote']) && $info['remote']) {
            @unlink($file);
        }
    }


    // 图片输出
    private function thumb_display($filename) {

        // 图片属性
        $types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
        $vals = getimagesize($filename);
        $mime = (isset($types[$vals[2]])) ? 'image/'.$types[$vals[2]] : 'image/jpg';
        $type = $vals[2];

        // 输出图片
        ob_start();
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
                imagecopyresampled($resize_im, $im, 0, 0, 0, 0, 0, 0, 0, 0);
                imagepng($im);
                break;
        }

        @imagedestroy($im);
    }
}