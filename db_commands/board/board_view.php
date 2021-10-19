<?php
if($_POST['boardType']==null){
    statusCode(2);
}else{
    $anonymous_board = false;
    switch ($_POST['boardType']){
        case 'board':
            if(islogin()){
                $boardType=Mysqli_real_escape_string(conn(), $_POST['boardType']);
                break;
            }
        case 'anonymous':
            $boardType=Mysqli_real_escape_string(conn(), $_POST['boardType']);
            $anonymous_board = true;
            break;
    }
    if(isset($_POST['page_no'])){
        if($_POST['page_no']>0){
            $page_no = Mysqli_real_escape_string(conn(), $_POST['page_no']);
        }else{
            $page_no = 1;
        }
    }else{
        $page_no = 1;
    }
    $posts_query = "SELECT `post_no` from $boardType where `post_deleted`=0";
    $result = db($posts_query);
    $total_record = $result->num_rows;
    $list = 25; 
    $block_cnt = 25; 
    $block_num = ceil($page_no / $block_cnt); 
    $block_start = (($block_num - 1) * $block_cnt) + 1;
    $block_end = $block_start + $block_cnt - 1;
    $total_page = ceil($total_record / $list);
    if($block_end > $total_page){ 
        $block_end = $total_page; 
    }
    $total_block = ceil($total_page / $block_cnt);
    $page_start = ($page_no - 1) * $list;
    $board_query = "SELECT `post_no`, `post_deleted`, `post_title`, `post_comments`, `member_code`, `member_nickname`, `post_date`, `post_hit`, `like` from $boardType where `post_deleted`=0 order by post_no desc limit $page_start, $list";
    $result = db($board_query);

    $members_level=memberLevel();
    $arr_board=array();
    for($i=0;$i<$result->num_rows;$i++){
        $board=$result->fetch_array(MYSQLI_ASSOC);
        if($members_level[$board['member_code']]>0){
            $member_level = $members_level[$board['member_code']];
        }else{
            $member_level = '0';
        }
        if($anonymous_board){
            $board['member_code']=-1;
        }
        array_push($arr_board, array('boardType' => $boardType, 'postNo' => $board['post_no'], 'postTitle' => htmlspecialchars($board['post_title'],ENT_QUOTES,'UTF-8'), 'postComments' => $board['post_comments'], 'memberCode' => $board['member_code'], 'memberNickname' => htmlspecialchars($board['member_nickname'],ENT_QUOTES,'UTF-8'), 'memberLevel' => $member_level, 'postDate' => $board['post_date'], 'postHit' => $board['post_hit'], 'post_like' => $board['like']));
    }
    $page_num="";
    if ($page_no > 1){
        $pre_page=$page_no-1;
        $page_num=$page_num."<a href='/board/$boardType?page_no=1'>처음</a>";
        $page_num=$page_num."<a href='/board/$boardType?page_no=$pre_page'>◀</a>";
    }
    for($i=$block_start;$i<=$block_end;$i++){
        if($page_no==$i){
        $page_num=$page_num."<p class='active'>$i</p>";
        }else{
        $page_num=$page_num."<a href='/board/$boardType?page_no=$i'>$i</a>";
        }
    }
    if($page_no<$total_page){
        $next_page=$page_no+1;
        $page_num=$page_num."<a href='/board/$boardType?page_no=$next_page'>▶</a>";
        $page_num=$page_num."<a href='/board/$boardType?page_no=$total_page'>마지막</a>";
    }
    $json = json_encode(array('status' => 1, 'arr_board' => $arr_board, 'page_num' => $page_num), JSON_PRETTY_PRINT);
    echo $json;
}
?>