<?php
if(isset($_SESSION['member_auth'])){
    $member_code = $_SESSION['member_auth'];
    $code = Mysqli_real_escape_string(conn(), $_POST['code']);
    if(!(overlap_check('valid_code', 'code', $code))){
        statusCode(9);
    }
    if(!(valid_check('valid_code', 'valid', true, 'code', $code))){
        statusCode(10);
    }
    $codeInfo_check_query = "SELECT * FROM `valid_code` WHERE `code`= '$code'";
    $result = db($codeInfo_check_query)->fetch_array(MYSQLI_ASSOC);
    $member_level = $result['member_level'];
    $member_enrolled = $result['member_enrolled'];
    $member_grade = $result['member_grade'];
    $member_class = $result['member_class'];
    $member_studentNo = $result['member_studentNo'];
    $member_name = $result['member_name'];
    $authentication_query = "UPDATE `members` SET `member_level`='$member_level', `member_enrolled`='$member_enrolled', `member_grade`='$member_grade', `member_class`='$member_class', `member_studentNo`='$member_studentNo', `member_name`='$member_name' WHERE `member_code`=$member_code;";
    db($authentication_query);
    $code_expire_query = "UPDATE `valid_code` SET `valid`=0 WHERE `code`= '$code'";
    $result = db($code_expire_query);
    session_destroy();
    statusCode(1);
}else{
    statusCode(2);
}
?>