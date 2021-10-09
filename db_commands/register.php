<?php
$member_id = $_POST['member_id'];
$member_pw = $_POST['member_pw'];
$member_pw_check = $_POST['member_pw_check'];
$member_nickname = $_POST['member_nickname'];
$code = $_POST['code'];
if(retype_check($member_pw, $member_pw_check)) {
    $json = json_encode(array('status' => 5));
    echo $json;
    exit();
}
if(overlap_check('members', 'member_id', $member_id)){
    $json = json_encode(array('status' => 6));
    echo $json;
    exit();
}
if(overlap_check('members', 'member_nickname', $member_nickname)){
    $json = json_encode(array('status' => 7));
    echo $json;
    exit();
}
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
//비밀번호 해시및 salt처리
$salt = bin2hex(random_bytes(32));
$member_pw = hash('sha3-256', $salt.$member_pw);

$codeInfo_check_query = "SELECT * FROM `valid_code` WHERE `code`= '$code'";
$result = db($codeInfo_check_query)->fetch_array(MYSQLI_ASSOC);
$member_level = $result['member_level'];
$member_enrolled = $result['member_enrolled'];
$member_grade = $result['member_grade'];
$member_class = $result['member_class'];
$member_studentNo = $result['member_studentNo'];
$member_name = $result['member_name'];
$register_query = "INSERT INTO `members` values (0, '$member_level', '$member_id', '$member_pw', '$salt', '$member_nickname', now(), '$member_enrolled', '$member_grade', '$member_class', '$member_studentNo', '$member_name');";
db($register_query);
if(login_check($member_id, $member_pw_check)){
    $code_expire_query = "UPDATE `valid_code` SET `valid`=0 WHERE `code`= '$code'";
    $result = db($code_expire_query);
    $json = json_encode(array('status' => 1));
    echo $json;
}else {
    $json = json_encode(array('status' => 11));
    echo $json;
    exit();
}
?>