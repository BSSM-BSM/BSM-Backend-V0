<?php
if ($_POST['post_comment']==null||$_POST['boardType']==null) {
    $json = json_encode(array('status' => 2));
    echo $json;
    exit();
}
if ($_POST['post_no']==null) {
    $json = json_encode(array('status' => 17));
    echo $json;
    exit();
}else{
    switch ($_POST['boardType']){
        case 'board':
            $boardType=$_POST['boardType'];
            $comment_boardType='comment';
            break;
        case 'blog':
            $boardType=$_POST['boardType'];
            $comment_boardType='blog_comment';
            break;
    }
    if(isset($_SESSION['member_code'])){
        $member_code = $_SESSION['member_code'];
        $member_nickname = $_SESSION['member_nickname'];
        $post_no = $_POST['post_no'];
        $post_comment = $_POST['post_comment'];
        // $comment_depth = $_POST['comment_depth'];
        // $comment_parent = $_POST['comment_parent'];
        $comment_order_query = "SELECT `order` from `$comment_boardType` where `depth`=0 and `post_no`=$post_no order by `comment_index` desc limit 1";
        $result = db($comment_order_query)->fetch_array(MYSQLI_ASSOC);
        if($result['order']==NULL){
            $order = 1;
        }else{
            $order = $result['order']+1;
        }
        $comment_write_query = "INSERT INTO `$comment_boardType` (post_no, depth, `order`, parent_no, member_code, member_nickname, comment, comment_date) values ($post_no, 0, $order, NULL, $member_code, '$member_nickname', '$post_comment', now());";
        db($comment_write_query);
        $update_comments_query = "UPDATE `$boardType` set `post_comments`=`post_comments`+1 where `post_no`=$post_no";
        db($update_comments_query);
        $json = json_encode(array('status' => 1));
        echo $json;
    }else{
        $json = json_encode(array('status' => 19));
        echo $json;
        exit();
    }
}
?>