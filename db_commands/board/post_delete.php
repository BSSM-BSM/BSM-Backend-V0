<?php
if ($_POST['post_no']==null||$_POST['boardType']==null) {
    $json = json_encode(array('status' => 17));
    echo $json;
    exit();
}else{
    switch ($_POST['boardType']){
        case 'board':
            $boardType=$_POST['boardType'];
            break;
        case 'blog':
            $boardType=$_POST['boardType'];
            break;
    }
    if(isset($_SESSION['member_code'])){
        $post_no = $_POST['post_no'];
        $post_check_query = "SELECT `member_code` FROM `$boardType` WHERE `post_no`= $post_no";
        $result = db($post_check_query)->fetch_array(MYSQLI_ASSOC);
        $member_code = $result['member_code'];
        if($member_code==$_SESSION['member_code']||$_SESSION['member_code']=1){
            $post_delete_query = "UPDATE `$boardType` SET `post_deleted` = 1 WHERE `post_no`= $post_no";
            db($post_delete_query);
            echo "<meta http-equiv='refresh' content='0; url=/board?boardType=".$boardType."'></meta>";
        }else{
            echo "<script>alert('게시글 작성자가 아닙니다.');history.go(-1);</script>";
        }
    }else{
        echo "<script>alert('정상적인 접근이 아닙니다 로그인 해주세요.');</script>";
        echo "<meta http-equiv='refresh' content='0; url=/login?returnUrl=$returnUrl'></meta>";
        exit();
    }
}
?>