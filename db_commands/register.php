<?php
$member_id = Mysqli_real_escape_string(conn(), $_POST['member_id']);
$member_pw = Mysqli_real_escape_string(conn(), $_POST['member_pw']);
$member_pw_check = Mysqli_real_escape_string(conn(), $_POST['member_pw_check']);
$member_nickname = Mysqli_real_escape_string(conn(), $_POST['member_nickname']);
$code = Mysqli_real_escape_string(conn(), $_POST['code']);
if(retype_check($member_pw, $member_pw_check)) {
    statusCode(5);
}
if(overlap_check('members', 'member_id', $member_id)){
    statusCode(6);
}
if(overlap_check('members', 'member_nickname', $member_nickname)){
    statusCode(7);
}
if(!(overlap_check('valid_code', 'code', $code))){
    statusCode(9);
}
if(!(valid_check('valid_code', 'valid', true, 'code', $code))){
    statusCode(10);
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
    statusCode(1);
}else {
    statusCode(11);
}
?>