<?php
  function retype_check($a, $b){
    if ($a==null||$b==null) {
      $json = json_encode(array('status' => 2));
      echo $json;
      exit();
    }
    if($a==$b){
      return false;
    }else{
      return true;
    }
  }
  function login_check($member_id, $member_pw){
    if ($member_id==null||$member_pw==null) {
      $json = json_encode(array('status' => 2));
      echo $json;
      exit();
    }
    $login_query = "SELECT * FROM members WHERE member_id='$member_id'";
    $result = db($login_query);
    if($result->num_rows==1){
      global $row;
      $row=$result->fetch_array(MYSQLI_ASSOC);
      if($row['member_pw']==$member_pw){
        return true;
      }
    }
    return false;
  }
  function overlap_check($table, $a, $b){
    if ($table==null||$a==null||$b==null) {
      $json = json_encode(array('status' => 2));
      echo $json;
      exit();
    }
    $overlap_query = "SELECT * FROM `$table` WHERE $a='$b'";
    $result = db($overlap_query);
    if($result->num_rows){
      return true;
    }
    return false;
  }
  function valid_check($table, $a, $b){
    if ($table==null||$a==null||$b==null) {
      $json = json_encode(array('status' => 2));
      echo $json;
      exit();
    }
    $valid_query = "SELECT * FROM `$table` WHERE `$a`='$b'";
    $result = db($valid_query);
    if($result->num_rows){
      return true;
    }
    return false;
  }
?>
