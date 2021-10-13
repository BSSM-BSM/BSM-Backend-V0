<?php
if(islogin()){
    if ($_POST['post_no']==null||$_POST['boardType']==null) {
        statusCode(2);
    }else{
        switch ($_POST['boardType']){
            case 'board':
            case 'anonymous':
                $boardType=$_POST['boardType'];
                break;
        }
        $post_no = $_POST['post_no'];
        $post_check_query = "SELECT `member_code` FROM `$boardType` WHERE `post_no`= $post_no";
        $result = db($post_check_query)->fetch_array(MYSQLI_ASSOC);
        $member_code = $result['member_code'];
        if($member_code==$_SESSION['member_code']||$_SESSION['member_code']=1){
            $post_delete_query = "UPDATE `$boardType` SET `post_deleted` = 1 WHERE `post_no`= $post_no";
            db($post_delete_query);
            statusCode(1);
        }else{
            statusCode(24);
        }
    }
}
?>