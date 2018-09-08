<?php

require_once('../Simple-Ajax-Uploader/extras/Uploader.php'); 

$uploader = new FileUpload('imgfile');   
$result   = $uploader->handleUpload('uploadDir/'); 

if (!$result) { 
  echo json_encode(array( 
          'success' => false, 
          'msg' => $uploader->getErrorMsg() 
       ));     
} else { 
  echo json_encode(array( 
          'success' => true, 
          'file' => $uploader->getFileName() 
       )); 
}  