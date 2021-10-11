<?php
require_once "session.php";
if(isset($_SESSION['member_code'])){
    if ($_FILES['file']['name']) {
        if (!$_FILES['file']['error']) {
            $temp = explode(".", $_FILES["file"]["name"]);
            $newfilename = round(microtime(true)).'.'.end($temp);
            $destinationFilePath = '/resource/board/upload_images/'.$newfilename;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $destinationFilePath)) {
                $json = json_encode(array('status' => 22));
                echo $json;
                exit();
            }
            else{
                $json = json_encode(array('status' => 1, 'file_path' => $destinationFilePath));
                echo $json;
            }
        }
        else {
            $json = json_encode(array('status' => 22));
            echo $json;
            exit();
        }
    }
}else{
    $json = json_encode(array('status' => 19));
    echo $json;
    exit();
}
?>