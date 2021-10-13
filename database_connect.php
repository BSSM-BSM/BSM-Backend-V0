<?php
  $conn = new mysqli("127.0.0.1", "php", "bcfbaa33ecbdfc4866810ef6213f6c3cc9eef466a0791ec6338b6ccaf5f1676b", "bssm_project");
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
