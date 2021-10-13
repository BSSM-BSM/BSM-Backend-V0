<?php
if(islogin()){
    if($_POST['like']==null||$_POST['boardType']==null) {
        statusCode(2);
    }
    if($_POST['post_no']==null) {
        statusCode(17);
    }else{
        switch ($_POST['boardType']){
            case 'board':
                $boardType=$_POST['boardType'];
                $like_boardType='board_like';
                break;
            case 'anonymous':
                $boardType=$_POST['boardType'];
                $like_boardType='anonymous_like';
                break;
            case 'music':
                $boardType=$_POST['boardType'];
                $like_boardType='music_like';
                break;
        }
        $member_code = $_SESSION['member_code'];
        $post_no = $_POST['post_no'];
        $like = $_POST['like'];
        if($like>0){
            $like=1;
        }else if($like<0){
            $like=-1;
        }else{
            $like=0;
        }
        $like_check_query = "SELECT `like` FROM `$like_boardType` WHERE `post_no`= $post_no AND `member_code`=$member_code";
        $result = db($like_check_query);
        if($result->num_rows){//대상 글에 좋아요 또는 싫어요를 누른 적이 있으면
            $like_query = "UPDATE `$like_boardType` SET `like`=$like WHERE `post_no`=$post_no AND `member_code`=$member_code;";
            $like_check = db($like_check_query)->fetch_array(MYSQLI_ASSOC);
            if($like_check['like']==$like){//좋아요 또는 싫어요를 한번 더
                $like_query = "UPDATE `$like_boardType` SET `like`=0 WHERE `post_no`=$post_no AND `member_code`=$member_code;";
                $return_like=0;
                if($like>0){//좋아요를 취소
                    $update_like_query = "UPDATE `$boardType` SET `like`=`like`-1 WHERE `post_no`=$post_no";
                }else{//싫어요를 취소
                    $update_like_query = "UPDATE `$boardType` SET `like`=`like`+1 WHERE `post_no`=$post_no";
                }
            }else if($like_check['like']==0){//취소한 좋아요 또는 싫어요를 다시 누름
                $return_like=$like;
                $update_like_query = "UPDATE `$boardType` SET `like`=`like`+$like WHERE `post_no`=$post_no";
            }else{//좋아요에서 싫어요 또는 싫어요에서 좋아요
                if($like_check['like']>0){//좋아요에서 싫어요
                    $update_like_query = "UPDATE `$boardType` SET `like`=`like`-2 WHERE `post_no`=$post_no";
                    $return_like=-1;
                }else{//싫어요에서 좋야요
                    $update_like_query = "UPDATE `$boardType` SET `like`=`like`+2 WHERE `post_no`=$post_no";
                    $return_like=1;
                }
            }
        }else{//대상 글에 좋아요 또는 싫어요를 한번도 누른 적이 없으면
            $like_query = "INSERT INTO `$like_boardType` (`post_no`, `like`, `member_code`) values ($post_no, $like, $member_code);";
            $update_like_query = "UPDATE `$boardType` SET `like`=`like`+$like WHERE `post_no`=$post_no";
            $return_like=$like;
        }
        db($like_query);
        db($update_like_query);
        $like_view_query = "SELECT `like` FROM `$boardType` WHERE `post_no`= $post_no";
        $result = db($like_view_query)->fetch_array(MYSQLI_ASSOC);
        $post_like = $result['like'];
        $json = json_encode(array('status' => 1, 'like' => $return_like, 'post_like' => $post_like));
        echo $json;
    }else{
        statusCode(2);
    }
}
?>