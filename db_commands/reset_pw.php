<?php
if(isset($_SESSION['member_reset'])){
    $member_code = $_SESSION['member_reset'];
    $reset_member_pw = $_POST['reset_member_pw'];
    $reset_member_pw_check = $_POST['reset_member_pw_check'];
    if (retype_check($reset_member_pw, $reset_member_pw_check)) {
        $json = json_encode(array('status' => 12));
        echo $json;
        exit();
    }
    //비밀번호 해시및 salt처리
    $salt = bin2hex(random_bytes(32));
    $member_pw = hash('sha3-256', $salt.$reset_member_pw);
    $pw_reset_query = "UPDATE `members` SET `member_pw`='$member_pw', `member_salt`='$salt' WHERE `member_code`=$member_code;";
    db($pw_reset_query);
    require "$_SERVER[DOCUMENT_ROOT]/db_commands/session_destroy.php";
    $json = json_encode(array('status' => 1));
    echo $json;
}else{
    $json = json_encode(array('status' => 2));
    echo $json;
    exit();
}
?>