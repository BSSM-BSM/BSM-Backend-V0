<?php
if(isset($_SESSION['member_code'])){
    $json = json_encode(array('status' => 1, 'is_login' => 1));
    echo $json;
}else{
    $json = json_encode(array('status' => 1, 'is_login' => 0));
    echo $json;
}
?>