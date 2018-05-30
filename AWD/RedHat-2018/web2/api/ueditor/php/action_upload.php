<?php
/**
 * 上传附件和上传视频
 * User: Jinqn
 * Date: 14-04-09
 * Time: 上午10:17
 */
include "Uploader.class.php";

/* 上传配置 */
$base64 = "upload";
switch (htmlspecialchars($_GET['action'])) {
    case 'uploadimage':
        $config = array(
            "pathFormat" => $CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'uploadscrawl':
        $config = array(
            "pathFormat" => $CONFIG['scrawlPathFormat'],
            "maxSize" => $CONFIG['scrawlMaxSize'],
            "allowFiles" => $CONFIG['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $CONFIG['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'uploadvideo':
        $config = array(
            "pathFormat" => $CONFIG['videoPathFormat'],
            "maxSize" => $CONFIG['videoMaxSize'],
            "allowFiles" => $CONFIG['videoAllowFiles']
        );
        $fieldName = $CONFIG['videoFieldName'];
        break;
    case 'uploadfile':
    default:
        $config = array(
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
}
// 验证用户
if (!$this->uid) {
    return json_encode(array('state'=> lang('会话超时，请重新登录')));
}

/* 生成上传实例对象并完成上传 */
$up = new Uploader($fieldName, $config, $base64);

/**
 * 得到上传文件所对应的各个参数,数组结构
 * array(
 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
 *     "url" => "",            //返回的地址
 *     "title" => "",          //新文件名
 *     "original" => "",       //原始文件名
 *     "type" => ""            //文件类型
 *     "size" => "",           //文件大小
 * )
 */

/* 返回数据 */
$result = $up->getFileInfo();

// 处理程序
if (isset($result['state']) && $result['state'] == 'SUCCESS' && $result['size']) {
    $this->load->model('attachment_model');
    $this->attachment_model->siteid = max(1, (int)$this->input->get('siteid'));
    $filename = DR_UE_PATH.$result['url'];
    list($id, $url, $b) = $this->attachment_model->upload($this->uid, array(
        'file_ext' => $result['type'],
        'full_path' => $filename,
        'file_size' => $result['size'] / 1024,
        'client_name' => str_replace($result['type'], '', $result['original']),
    ));
    $result['id'] = UEDITOR_IMG_ID.'_img_'.$id;
    $result['url'] = $url;
    // 图片水印
    if (SITE_IMAGE_WATERMARK && SITE_IMAGE_CONTENT && in_array(trim($result['type'], '.'), array('jpg', 'gif', 'png', 'jpeg'))) {
        $result['url'] = dr_thumb($id, $imageinfo[0], $imageinfo[1], 1);
    }

    $result['url'] = str_replace(SITE_URL, '/', $result['url']);

}

return json_encode($result);