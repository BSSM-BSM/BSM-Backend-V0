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
        $content = '
        <!DOCTYPE HTML>
        <html lang="kr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body>
            <div style="display:flex;justify-content:center;">
                <div style="padding:25px 0;text-align:center;margin:0 auto;border:solid 5px;border-radius:25px;font-family:-apple-system,BlinkMacSystemFont,\'Malgun Gothic\',\'맑은고딕\',helvetica,\'Apple SD Gothic Neo\',sans-serif;background-color:#202124; color:#e8eaed;">
                    <img src="https://bssm.kro.kr/icons/logo.png" alt="로고" style="height:35px; padding-top:12px;">
                    <h1 style="font-size:28px;margin-left:25px;margin-right:25px;">BSM 회원가입 인증 코드입니다.</h1>
                    <h2 style="display:inline-block;font-size:20px;text-align:center;margin:0;color:#e8eaed;padding:15px;border-radius:7px;box-shadow:20px 20px 50px rgba(0, 0, 0, 0.5);background-color:rgba(192, 192, 192, 0.2);">'.$user_info['code'].'</h2>
                    <br><br><br>
                    <div style="background-color:rgba(192, 192, 192, 0.2);padding:10px;text-align:left;font-size:14px;">
                        <p style="margin:0;">- 본 이메일은 발신전용 이메일입니다.</p>
                        <p style="margin:0;">- 인증 코드는 한 사람당 한 개의 계정에만 쓸 수 있습니다.</p>
                    </div><br>
                    <footer style="padding:15px 0;bottom:0;width:100%;font-size:15px;text-align:center;font-weight:bold;">
                        <p style="margin:0;">부산 소프트웨어 마이스터고 학교 지원 서비스</p>
                        <p style="margin:0;">Copyright 2021. BSM TEAM all rights reserved.</p>
                    </footer>
                </div>
            </div>
        </body>
        </html>
        ';
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