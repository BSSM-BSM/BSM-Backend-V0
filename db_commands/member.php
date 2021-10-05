<?php
if ($_POST['member_code']==null) {
    echo "<script>alert('멤버코드가 없습니다.');history.go(-1);</script>";
    exit();
}else{
    $member_code = $_POST['member_code'];
    $member_query = "SELECT * from members WHERE member_code=$member_code";
    $result = db($member_query);
    $member_info=$result->fetch_array(MYSQLI_ASSOC);
    $json = json_encode(array('status' => 1, 'member_code' => $member_info['member_code'], 'member_id' => $member_info['member_id'], 'member_nickname' => $member_info['member_nickname'], 'member_level' => $member_info['member_level'], 'member_created' => $member_info['member_created'], 'member_enrolled' => $member_info['member_enrolled'], 'member_grade' => $member_info['member_grade'], 'member_class' => $member_info['member_class'], 'member_studentNo' => $member_info['member_studentNo'], 'member_name' => $member_info['member_name']));
    echo $json;
}
?>