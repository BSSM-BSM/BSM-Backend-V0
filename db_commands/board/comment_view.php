<?php
if ($_POST['post_no']==null||$_POST['boardType']==null) {
    statusCode(17);
}else{
    $anonymous_board = false;
    switch ($_POST['boardType']){
        case 'board':
            if(islogin()){
                $boardType=Mysqli_real_escape_string(conn(), $_POST['boardType']);
                $comment_boardType=$boardType.'_comment';
                break;
            }
        case 'anonymous':
            $boardType=Mysqli_real_escape_string(conn(), $_POST['boardType']);
            $comment_boardType=$boardType.'_comment';
            $anonymous_board = true;
            break;
    }
    $post_no = Mysqli_real_escape_string(conn(), $_POST['post_no']);
    $comment_query = "SELECT * from $comment_boardType where post_no=$post_no and `comment_deleted`=0 order by `order`";
    $result = db($comment_query);
    $arr_comment=array();
    for($i=0;$i<$result->num_rows;$i++){
        $comment=$result->fetch_array(MYSQLI_ASSOC);
        if($anonymous_board){
            $comment['member_code']=0;
        }
        array_push($arr_comment, array('comment_idx' => $comment['comment_index'],'memberCode' => $comment['member_code'], 'memberNickname' => htmlspecialchars($comment['member_nickname'],ENT_QUOTES,'UTF-8'), 'comment' => htmlspecialchars($comment['comment'],ENT_QUOTES,'UTF-8'), 'commentDate' => $comment['comment_date']));
    }
    $json = json_encode(array('status' => 1, 'arr_comment' => $arr_comment));
    echo $json;
}
?>