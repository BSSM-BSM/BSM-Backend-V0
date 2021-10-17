<?php
require_once "$_SERVER[DOCUMENT_ROOT]/database_connect.php";
if(isset($_GET['returnUrl'])){
  $returnUrl = $_GET['returnUrl'];
}else{
  $returnUrl = '/';
}
session_start();
$res=session_destroy(); //모든 세션 변수 지우기
$SSID=Mysqli_real_escape_string(conn(), $_COOKIE['SSID']);
$session_expire_query = "UPDATE `session` SET `cookie_expire_time`=0 WHERE `session`='$SSID';";
db($session_expire_query);
setcookie("SSID", "", 0, "/", 'bssm.kro.kr', true, true);
if($res){
    header('Location:'.$returnUrl); // 로그아웃 성공 시 이전 페이지로 이동
}
?>
