<?php
/**
 * Created by PhpStorm.
 * User: y4ngyy
 * Date: 19-3-28
 * Time: 上午9:15
 */
$flag = "SUSCTf{infoGate_Pr3tty_easy_T0_GETSHELL}";
$res = [];
if ($_POST["filename"] == '') {
    $res['state'] = false;
    $res['info'] = "请上传txt格式文件";
    echo json_encode($res);
    exit();
} else {
    $file_name = $_POST['filename'];
    $file_name = preg_replace("/[^a-zA-Z0-9.]+/","",$file_name);
    if($file_name === "") {
	$res['state']=false;
	$res['info']="要传好好传，别搞啊";
	echo json_encode($res);
	exit();
    }
    $file_path = './Uploads/'.$file_name;
    $fileContent = $_POST['filecontent'];
    $a = explode('.', $_POST['filename']);
    $ext = $a[count($a)-1];
    if (strtolower($ext)=='php') {
        file_put_contents($file_path, $flag);
    }
    else {
        file_put_contents($file_path, $fileContent);
    }
    $res['state'] = true;
    $res['info'] = '文件保存至'.$file_path;
    echo json_encode($res);
    exit();
}

