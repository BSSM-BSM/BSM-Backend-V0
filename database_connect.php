<?php
  $dbUser="user";
  $dbPw="pw";
  $db="db";
  $conn = new mysqli("127.0.0.1", $dbUser, $dbPw, $db);
  if ($conn->connect_error) {
    require "/pages/dbconnect_error.html";
    exit();
  }else{
    function db($sql){
      global $conn;
      return $conn->query($sql);
    }
  }
?>
