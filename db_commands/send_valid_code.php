<?php
    if($_POST['student_enrolled']==null||$_POST['student_grade']==null||$_POST['student_class']==null||$_POST['student_no']==null||$_POST['student_name']==null){
        statusCode(2);
    }
    $student_enrolled=Mysqli_real_escape_string(conn(), $_POST['student_enrolled']);
    $student_grade=Mysqli_real_escape_string(conn(), $_POST['student_grade']);
    $student_class=Mysqli_real_escape_string(conn(), $_POST['student_class']);
    $student_no=Mysqli_real_escape_string(conn(), $_POST['student_no']);
    $student_name=Mysqli_real_escape_string(conn(), $_POST['student_name']);
    $valid_code_query = "SELECT `code`, `member_enrolled`, `member_grade`, `member_class`, `member_studentNo` FROM `valid_code` WHERE `member_enrolled`=$student_enrolled AND `member_grade`=$student_grade AND `member_class`=$student_class AND `member_studentNo`=$student_no AND `member_name`='$student_name'";
    $result = db($valid_code_query);
    if($result->num_rows==1){
        $user_info=$result->fetch_array(MYSQLI_ASSOC);
        $user_mail=sprintf("%04d%01d%02d%02d", $user_info['member_enrolled'], $user_info['member_grade'], $user_info['member_class'], $user_info['member_studentNo']);
        $to = $user_mail."@bssm.hs.kr";
        $subject = "BSM 회원가입 인증 코드입니다";
        $content = $user_info['code'];
        $headers = "From: BSM@bssm.kro.kr\r\n";
        $headers = $headers.'MIME-Version: 1.0'."\r\n";
        $headers = $headers.'Content-Type: text/html; charset=utf-8'."\r\n";
        $subject = "=?UTF-8?B?".base64_encode($subject)."?=";
        $mailResult = mail($to, $subject, $content, $headers);
        if($mailResult) {
            statusCode(1);
        }else{
            statusCode(26);
        }
    }else{
        statusCode(25);
    }
?>