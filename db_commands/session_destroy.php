<?php
require_once "$_SERVER[DOCUMENT_ROOT]/database_connect.php";
session_start();
$res=session_destroy(); //모든 세션 변수 지우기
$SSID=$_COOKIE['SSID'];
$session_expire_query = "UPDATE `session` SET `cookie_expire_time`=0 WHERE `session`='$SSID';";
db($session_expire_query);
setcookie("SSID", "", 0, "/", 'bssm.kro.kr', true, true);
?>