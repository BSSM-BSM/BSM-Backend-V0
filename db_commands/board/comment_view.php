<?php
if ($_POST['post_no']==null||$_POST['boardType']==null) {
    $json = json_encode(array('status' => 17));
    echo $json;
    exit();
}else{
    switch ($_POST['boardType']){
        case 'board':
            if(!(isset($_SESSION['member_code']))){
            $json = json_encode(array('status' => 21));
            echo $json;
            exit();
            }
            $boardType=$_POST['boardType'];
            $comment_boardType='comment';
            break;
        case 'blog':
            $boardType=$_POST['boardType'];
            $comment_boardType='blog_comment';
            break;
    }
    $post_no = $_POST['post_no'];
    $comment_query = "SELECT * from $comment_boardType where post_no=$post_no order by `order`";
    $result = db($comment_query);
    $arr_comment=array();
    for($i=0;$i<$result->num_rows;$i++){
        $comment=$result->fetch_array(MYSQLI_ASSOC);
        array_push($arr_comment, array('memberCode' => $comment['member_code'], 'memberNickname' => htmlspecialchars($comment['member_nickname'],ENT_QUOTES,'UTF-8'), 'comment' => htmlspecialchars($comment['comment'],ENT_QUOTES,'UTF-8'), 'commentDate' => $comment['comment_date']));
    }
    $json = json_encode(array('status' => 1, 'arr_comment' => $arr_comment));
    echo $json;
}
?>