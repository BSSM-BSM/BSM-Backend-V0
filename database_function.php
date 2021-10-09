<?php
  function retype_check($a, $b){
    if ($a===null||$b===null) {
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
    if ($member_id===null||$member_pw===null) {
      $json = json_encode(array('status' => 2));
      echo $json;
      exit();
    }
    $login_query = "SELECT * FROM members WHERE member_id='$member_id'";
    $result = db($login_query);
    if($result->num_rows==1){
      global $row;
      $row=$result->fetch_array(MYSQLI_ASSOC);
      if($row['member_salt']==null){
        if($row['member_pw']==hash('sha3-256', $member_pw)){
          $_SESSION['member_reset']=$row['member_code'];
          $json = json_encode(array('status' => 24));
          echo $json;
          exit();
        }
      }
      if($row['member_pw']==hash('sha3-256', $row['member_salt'].$member_pw)){
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
  function valid_check($table, $a, $b, $c, $d){
    if ($table==null||$a==null||$b==null||$c==null||$d==null) {
      $json = json_encode(array('status' => 2));
      echo $json;
      exit();
    }
    $valid_query = "SELECT * FROM `$table` WHERE `$a`='$b' AND `$c`='$d'";
    $result = db($valid_query);
    if($result->num_rows){
      return true;
    }
    return false;
  }
?>
