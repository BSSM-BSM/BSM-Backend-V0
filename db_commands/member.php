<?php
if ($_POST['member_code']==null) {
    statusCode(14);
}else{
    $member_code = Mysqli_real_escape_string(conn(), $_POST['member_code']);
    $member_query = "SELECT * from members WHERE member_code=$member_code";
    $result = db($member_query);
    $member_info=$result->fetch_array(MYSQLI_ASSOC);
    $json = json_encode(array('status' => 1, 'member_code' => $member_info['member_code'], 'member_nickname' => $member_info['member_nickname'], 'member_level' => $member_info['member_level'], 'member_created' => $member_info['member_created'], 'member_enrolled' => $member_info['member_enrolled'], 'member_grade' => $member_info['member_grade'], 'member_class' => $member_info['member_class'], 'member_studentNo' => $member_info['member_studentNo'], 'member_name' => $member_info['member_name']));
    echo $json;
}
?>