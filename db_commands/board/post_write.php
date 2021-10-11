<?php
if ($_POST['post_title']==null||$_POST['post_content']==null||$_POST['boardType']==null) {
    $json = json_encode(array('status' => 2));
    echo $json;
    exit();
}else{
    $anonymous_board = false;
    switch ($_POST['boardType']){
        case 'board':
            $boardType=$_POST['boardType'];
            break;
        case 'anonymous':
            $boardType=$_POST['boardType'];
            $anonymous_board = true;
            break;
    }
    if(isset($_SESSION['member_code'])){
        require_once "$root_dir/lib/html_purifier.php";
        $member_code = $_SESSION['member_code'];
        if($anonymous_board){
            $member_nickname = 'ㅇㅇ';
        }else{
            $member_nickname = $_SESSION['member_nickname'];
        }
        $post_title = $_POST['post_title'];
        $post_content = html_purifier($_POST['post_content']);
        if(isset($_POST['post_no'])){
            $post_no=$_POST['post_no'];
            $post_check_query = "SELECT `member_code` FROM `$boardType` WHERE `post_no`= $post_no";
            $result = db($post_check_query)->fetch_array(MYSQLI_ASSOC);
            $member_code_check = $result['member_code'];
            if($member_code_check==$member_code||$_SESSION['member_code']=1){
                $post_modify_query = "UPDATE `$boardType` SET `post_title`='$post_title', `post_content`='$post_content' WHERE `post_no`=$post_no";
                db($post_modify_query);
            }else{
                $json = json_encode(array('status' => 20));
                echo $json;
                exit();
            }
        }else{
            $post_write_query = "INSERT INTO `$boardType` (member_code, member_nickname, post_title, post_content, post_date) values ($member_code, '$member_nickname', '$post_title', '$post_content', now())";
            db($post_write_query);
        }
        $json = json_encode(array('status' => 1));
        echo $json;
    }else{
        $json = json_encode(array('status' => 19));
        echo $json;
        exit();
    }
}
?>