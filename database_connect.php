<?php
  $conn = new mysqli("127.0.0.1", "user", "pw", "db");
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
