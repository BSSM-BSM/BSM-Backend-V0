<?php
if(isset($_SESSION['member_auth'])){
    $member_code = $_SESSION['member_auth'];
    $code = $_POST['code'];
    if(!(valid_check('valid_code', 'code', $code))){
        $json = json_encode(array('status' => 9));
        echo $json;
        exit();
    }
    if(!(valid_check('valid_code', 'valid', 1))){
        $json = json_encode(array('status' => 10));
        echo $json;
        exit();
    }
    $codeInfo_check_query = "SELECT * FROM `valid_code` WHERE `code`= '$code'";
    $result = db($codeInfo_check_query)->fetch_array(MYSQLI_ASSOC);
    $member_level = $result['member_level'];
    $member_enrolled = $result['member_enrolled'];
    $member_class = $result['member_class'];
    $member_studentNo = $result['member_studentNo'];
    $authentication_query = "UPDATE `members` SET `member_level`='$member_level', `member_enrolled`='$member_enrolled', `member_class`='$member_class', `member_studentNo`='$member_studentNo' WHERE `member_code`=$member_code;";
    db($authentication_query);
    $code_expire_query = "UPDATE `valid_code` SET `valid`=0 WHERE `code`= '$code'";
    $result = db($code_expire_query);
    session_destroy();
    $json = json_encode(array('status' => 1));
    echo $json;
}else{
    $json = json_encode(array('status' => 2));
    echo $json;
    exit();
}
?>