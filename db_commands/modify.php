<?php
$member_id = $_SESSION['member_id'];
$member_pw = $_POST['member_pw'];
if(login_check($member_id, $member_pw)){
    $modify_type = $_POST['modify_type'];
    if ($_POST['modify_type']==null){
        statusCode(2);
    }else{
        switch($modify_type){
            case 'pw':
                $modify_member_pw = $_POST['modify_member_pw'];
                $modify_member_pw_check = $_POST['modify_member_pw_check'];
                if(retype_check($modify_member_pw, $modify_member_pw_check)){
                    statusCode(12);
                }
                //비밀번호 해시및 salt처리
                $salt = bin2hex(random_bytes(32));
                $member_pw = hash('sha3-256', $salt.$modify_member_pw);
                $modify_query = "UPDATE members SET member_pw='$member_pw', `member_salt`='$salt' WHERE member_id='$member_id'";
                db($modify_query);
                if(login_check($member_id, $modify_member_pw)){
                    require "$_SERVER[DOCUMENT_ROOT]/db_commands/session_destroy.php";
                    $json = json_encode(array('status' => 1, 'returnUrl' => '/'));
                    echo $json;
                }else{
                    statusCode(13);
                }
                break;
        }
    }
}else{
    statusCode(4);
}
?>