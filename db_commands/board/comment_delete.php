<?php
if ($_POST['post_no']==null||$_POST['boardType']==null) {
    $json = json_encode(array('status' => 17));
    echo $json;
    exit();
}else{
    switch ($_POST['boardType']){
        case 'board':
        case 'anonymous':
            $boardType=$_POST['boardType'];
            $comment_boardType=$boardType.'_comment';
            break;
    }
    if(isset($_SESSION['member_code'])){
        $post_no = $_POST['post_no'];
        $comment_index = $_POST['comment_index'];
        $comment_check_query = "SELECT `member_code` FROM `$comment_boardType` WHERE `comment_index`= $comment_index AND `post_no`=$post_no";
        $result = db($comment_check_query)->fetch_array(MYSQLI_ASSOC);
        $member_code = $result['member_code'];
        if($member_code==$_SESSION['member_code']||$_SESSION['member_code']=1){
            $comment_delete_query = "UPDATE `$comment_boardType` SET `comment_deleted` = 1 WHERE `comment_index`= $comment_index AND `post_no`=$post_no";
            db($comment_delete_query);
            $update_comments_query = "UPDATE `$boardType` set `post_comments`=`post_comments`-1 where `post_no`=$post_no";
            db($update_comments_query);
            $json = json_encode(array('status' => 1));
            echo $json;
        }else{
            $json = json_encode(array('status' => 20));
            echo $json;
        }
    }else{
        $json = json_encode(array('status' => 19));
        echo $json;
        exit();
    }
}
?>