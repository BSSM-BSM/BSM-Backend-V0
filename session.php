<?php
session_start();
if(!(isset($_SESSION['member_code'])) && isset($_COOKIE['SSID'])){
    if($_COOKIE['SSID']!=NULL){
        require_once "database_connect.php";
        $SSID=Mysqli_real_escape_string(conn(), $_COOKIE['SSID']);
        $time=time();
        $session_search_query = "SELECT `member_code` FROM `session` WHERE `session`='$SSID' AND `cookie_expire_time`>=$time;";
        $result = db($session_search_query);
        if($result->num_rows==1){
            $row=$result->fetch_array(MYSQLI_ASSOC);
            $member_code=$row['member_code'];
            $session_login_query = "SELECT * FROM members WHERE member_code='$member_code'";
            $result = db($session_login_query);
            if($result->num_rows==1){
                $row=$result->fetch_array(MYSQLI_ASSOC);
                $_SESSION['member_code']=$row['member_code'];
                $_SESSION['member_id']=$row['member_id'];
                $_SESSION['member_nickname']=$row['member_nickname'];
                $_SESSION['member_level']=$row['member_level'];
            }
        }else{
            $session_expire_query = "UPDATE `session` SET `cookie_expire_time`=0 WHERE NOT `cookie_expire_time`=0 AND `cookie_expire_time`<$time;";
            db($session_expire_query);
            setcookie("SSID", "", 0, "/", 'bssm.kro.kr', true, true);
        }
    }
}

?>