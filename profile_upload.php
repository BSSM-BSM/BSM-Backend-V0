<?php
require_once "session.php";
function statusCode($status_code){
    $json = json_encode(array('status' => $status_code));
    echo $json;
    exit();
}
function resize_image($file, $newfile, $w, $h) {
    list($width, $height) = getimagesize($file);
    if(strpos(strtolower($file), ".jpg"))
       $src = imagecreatefromjpeg($file);
    else if(strpos(strtolower($file), ".png"))
       $src = imagecreatefrompng($file);
    else if(strpos(strtolower($file), ".gif"))
       $src = imagecreatefromgif($file);
    $dst = imagecreatetruecolor($w, $h);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);
    if(strpos(strtolower($newfile), ".jpg"))
       imagejpeg($dst, $newfile);
    else if(strpos(strtolower($newfile), ".png"))
       imagepng($dst, $newfile);
    else if(strpos(strtolower($newfile), ".gif"))
       imagegif($dst, $newfile);
}
if(isset($_SESSION['member_code'])){
    if ($_FILES['file']['name']) {
        if (!$_FILES['file']['error']) {
            $temp = explode(".", $_FILES["file"]["name"]);
            $newfilename = 'profile_'.$_SESSION['member_code'].'.'.end($temp);
            $destinationFilePath = '/resource/member/profile_images/'.$newfilename;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$destinationFilePath)) {
                statusCode(22);
            }else{
                resize_image($_SERVER['DOCUMENT_ROOT'].$destinationFilePath, $_SERVER['DOCUMENT_ROOT'].'/resource/member/profile_images/profile_'.$_SESSION['member_code'].'.png', 128, 128);
                $json = json_encode(array('status' => 1, 'file_path' => $destinationFilePath));
                echo $json;
            }
        }
        else {
            statusCode(22);
        }
    }
}else{
    statusCode(19);
}
?>