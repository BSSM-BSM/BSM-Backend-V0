<?php
$member_id = $_POST['member_id'];
$member_pw = $_POST['member_pw'];
if(login_check($member_id, $member_pw)){
    $member_code=$row['member_code'];
    if($row['member_enrolled']==0){
        $_SESSION['member_auth']=$member_code;
        statusCode(8);
    }
    $_SESSION['member_code']=$member_code;
    $_SESSION['member_id']=$row['member_id'];
    $_SESSION['member_nickname']=$row['member_nickname'];
    $_SESSION['member_level']=$row['member_level'];
    if($_SESSION['member_id']==$member_id){
        $salt = bin2hex(random_bytes(32));
        $session_value=hash('sha3-256', $salt.microtime(true));
        $cookie_expire_time = time() + 1209600;
        setcookie("SSID", $session_value, $cookie_expire_time, "/", 'bssm.kro.kr', true, true);
        $session_query = "INSERT INTO `session` values (0, '$session_value', '$cookie_expire_time', '$member_code');";
        db($session_query);
        $json = json_encode(array('status' => 1, 'returnUrl' => $returnUrl));
        echo $json;
    }else{
        session_destroy();
        statusCode(3);
    }
}else{
    statusCode(4);
}
?>