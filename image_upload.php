<?php
require_once "session.php";
function statusCode($status_code){
    $json = json_encode(array('status' => $status_code));
    echo $json;
    exit();
}
if(isset($_SESSION['member_code'])){
    if ($_FILES['file']['name']) {
        if (!$_FILES['file']['error']) {
            $temp = explode(".", $_FILES["file"]["name"]);
            $newfilename = round(microtime(true)).'.'.end($temp);
            $destinationFilePath = '/resource/board/upload_images/'.$newfilename;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$destinationFilePath)) {
                statusCode(22);
            }
            else{
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