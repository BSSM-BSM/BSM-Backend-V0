<?php
if(islogin()){
    if ($_POST['post_comment']==null||$_POST['boardType']==null) {
        statusCode(2);
    }
    if ($_POST['post_no']==null) {
        statusCode(17);
    }else{
        $anonymous_board = false;
        switch ($_POST['boardType']){
            case 'board':
                $boardType=Mysqli_real_escape_string(conn(), $_POST['boardType']);
                $comment_boardType=$boardType.'_comment';
                break;
            case 'anonymous':
                $boardType=Mysqli_real_escape_string(conn(), $_POST['boardType']);
                $comment_boardType=$boardType.'_comment';
                $anonymous_board = true;
                break;
        }
        $member_code = $_SESSION['member_code'];
        if($anonymous_board){
            $member_nickname = 'ㅇㅇ';
        }else{
            $member_nickname = $_SESSION['member_nickname'];
        }
        $post_no = Mysqli_real_escape_string(conn(), $_POST['post_no']);
        $post_comment = Mysqli_real_escape_string(conn(), $_POST['post_comment']);
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
        statusCode(1);
    }
}
?>