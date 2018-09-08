<?php

/**
 * page callback to deal upload file
 */
function _avatar_uploader_handler() {
  if (user_access("administer users") || user_access("upload user avatar")) {
    require_once(avatar_uploader_lib_path('Simple-Ajax-Uploader/extras/Uploader.php')); 

    $upload_dir = _avatar_uploader_tmp_path();
    file_prepare_directory($upload_dir, FILE_CREATE_DIRECTORY);

    $uploader = new FileUpload('imgfile');
    //update filename as unique
    $uploader->newFileName = date('Ymdhis') . '_' . preg_replace('/[^A-Za-z0-9_.\-]/', '_', $uploader->getFileName());
    
    $allowedExtensions = array('png', 'gif', 'jpg', 'jpeg');
    $result   = $uploader->handleUpload($upload_dir, $allowedExtensions);
    if (!$result) { 
      echo json_encode(array( 
             'success' => false, 
             'msg' => $uploader->getErrorMsg()  
           ));  
    } else {
      echo json_encode(array( 
             'success'  => true, 
             'new_file' => $uploader->newFileName,
             'dire' =>$upload_dir,
             //use static link if client could access it
             'file_url' => url('au/view', array('query'=>array('file'=>$uploader->newFileName))) ,
           )); 
    }
  }
  else {
    echo json_encode(array('ok' => 0, 'error' => t('access denied')));
    exit;
  }
}

/**
 * page callback to display image
 */
function _avatar_uploader_view() {
  $filename   = $_GET['file'];
  $filename   = drupal_basename($filename); 
  $upload_dir = _avatar_uploader_tmp_path();
  
  $file = $upload_dir . '/' . $filename;
  //$type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
  $type = file_get_mimetype($file); //php 5.2
  header("Content-Type: $type");
  
  echo file_get_contents($file);
  exit;
}

/**
 * page callback for au/panel
 */
function _avatar_uploader_panel() {    
  global $user;
  $uid = $_REQUEST['uid'];
  
  if (user_access("administer users") || (user_access("edit own avatar") && $uid == $user->uid)) {
    $_SESSION['changing_user_avatar_uid'] = $uid;
    
    $dimesion = variable_get('user_picture_dimensions', '150x150');
    $wxh      = explode('x', $dimesion);

    echo avatar_uploader_get_panel(
         array(
          'width'  => $wxh[0], 
          'height' => $wxh[1]
        ));
  }
  else {
    echo t("access denied");
  }
  exit;
  
}

/**
 * page callback for crop submit
 */
function _avatar_uploader_do_crop() {
 $x    = $_POST['x'];
	$y    = $_POST['y'];
	$w    = $_POST['w'];
	$h    = $_POST['h'];
	$file = $_POST['file'];
 $id   = $_POST['id'];
 
 //get target width and height from client, so no hard-code here
 $targ_w = $_POST['p_width'];
 $targ_h = $_POST['p_height'];

 if (!is_numeric($targ_h) && !is_numeric($targ_w)) {
   echo json_encode(array('ok' => 0, 'error'=> t('No target dimension')));
   exit;
 }
 
	$jpeg_quality = 90;

 $src   = _avatar_uploader_tmp_path($file);
	//$type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $src);
	$type  = file_get_mimetype($src);
 $dst_r = ImageCreateTrueColor($targ_w, $targ_h);
	switch ($type) {
		case 'image/jpeg':
			$img_r = imagecreatefromjpeg($src);
			imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $targ_w, $targ_h, $w, $h);
			imagejpeg($dst_r, $src, $jpeg_quality);
			break;
		case 'image/png':
			$img_r = imagecreatefrompng($src);
			imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $targ_w, $targ_h, $w, $h);
			imagepng($dst_r, $src);
			break;
		case 'image/gif':
			$img_r = imagecreatefromgif($src);
			imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $targ_w, $targ_h, $w, $h);
			imagegif($dst_r, $src);
			break;			
	}
 
 //invoke custom process
 $custom_avatar_process = 'avatar_uploader_custom_process';
 if (is_callable($custom_avatar_process)) {
   $new_avatar = call_user_func($custom_avatar_process, $src, $_SESSION['changing_user_avatar_uid'], $id);
 }
 else {
   $new_avatar = avatar_uploader_save_profile_picture($src, $_SESSION['changing_user_avatar_uid'], $id);
 }
 
 //unset($_SESSION['changing_user_avatar_uid']);
 
 echo json_encode(array('ok' => 1, 'type' => $type, 'file' => $file, 'new_avatar' => $new_avatar));
}

/**
 * get tmp upload directory
 */
function _avatar_uploader_tmp_path($file = NULL) {
  $dir = file_directory_temp() . '/' . UPLOADER_TMP_DIR;
  if ($file) {
   $dir = "$dir/$file";
  }
  return $dir;
}
