<?php
  require_once "session.php";
  if(isset($_POST['returnUrl'])){
    $returnUrl = $_POST['returnUrl'];
  }else{
    $returnUrl = '/';
  }
  require_once "database_connect.php";
  require_once "database_function.php";
  $command_type = $_POST['command_type'];
  switch($command_type){
    case 'version':
      if ($_POST['os']==NULL||$_POST['app']==NULL) {
        $json = json_encode(array('status' => 2));
        echo $json;
        exit();
      }else{
        switch($_POST['app']){
          case 'web':
            $json = json_encode(array('status' => 1, 'versionCode' => 1, 'versionName' => '0.3.4'));
            echo $json;
            break;
          case 'app':
            switch($_POST['os']){
              case 'android':
                $json = json_encode(array('status' => 1, 'versionCode' => 1, 'versionName' => 'Beta 0.1'));
                echo $json;
                break;
              default:
                $json = json_encode(array('status' => 2));
                echo $json;
                break;
            }
            break;
          default:
            $json = json_encode(array('status' => 2));
            echo $json;
            break;
        }
      }
      break;
    case 'login':
      $member_id = $_POST['member_id'];
      $member_pw = hash('sha3-256', $_POST['member_pw']);
      if(login_check($member_id, $member_pw)){
        $member_code=$row['member_code'];
        if($row['member_enrolled']==0){
          $_SESSION['member_auth']=$member_code;
          $json = json_encode(array('status' => 8));
          echo $json;
          exit();
        }
        $_SESSION['member_code']=$member_code;
        $_SESSION['member_id']=$row['member_id'];
        $_SESSION['member_nickname']=$row['member_nickname'];
        $_SESSION['member_level']=$row['member_level'];
        if($_SESSION['member_id']==$member_id){
          $session_value=hash('sha3-256', microtime(true));
          $cookie_expire_time = time() + 604800;
          setcookie("SSID", $session_value, $cookie_expire_time, "/", 'bssm.kro.kr', true, true);
          $session_query = "INSERT INTO `session` values (0, '$session_value', '$cookie_expire_time', '$member_code');";
          db($session_query);
          $json = json_encode(array('status' => 1, 'returnUrl' => $returnUrl));
          echo $json;
        }else{
          session_destroy();
          $json = json_encode(array('status' => 3));
          echo $json;
        }
      }else{
        $json = json_encode(array('status' => 4));
        echo $json;
      }
    break;
    case 'modify':
        $member_id = $_SESSION['member_id'];
        $member_pw = hash('sha3-256', $_POST['member_pw']);
        if(login_check($member_id, $member_pw)){
          $modify_type = $_POST['modify_type'];
          if ($_POST['modify_type']==null) {
            $json = json_encode(array('status' => 2));
            echo $json;
            exit();
          }else{
            switch($modify_type){
              case 'pw':
                $modifymember_pw = hash('sha3-256', $_POST['modifymember_pw']);
                $modifymember_pw_check = hash('sha3-256', $_POST['modifymember_pw_check']);
                if (retype_check($modifymember_pw, $modifymember_pw_check)) {
                  $json = json_encode(array('status' => 12));
                  echo $json;
                  exit();
                }
                $modify_query = "UPDATE members set member_pw='$modifymember_pw' where member_id='$member_id'";
                db($modify_query);
                if(login_check($member_id, $modifymember_pw)){
                  session_destroy();
                  $json = json_encode(array('status' => 1, 'returnUrl' => '/login?returnUrl='.$returnUrl));
                  echo $json;
                }else{
                  $json = json_encode(array('status' => 13));
                  echo $json;
                }
              break;
            }
          }
        }else{
          $json = json_encode(array('status' => 4));
          echo $json;
        }
    break;
    case 'register':
      $member_id = $_POST['member_id'];
      $member_pw = hash('sha3-256', $_POST['member_pw']);
      $member_pw_check = hash('sha3-256', $_POST['member_pw_check']);
      $member_nickname = $_POST['member_nickname'];
      $code = $_POST['code'];
      if (retype_check($member_pw, $member_pw_check)) {
        $json = json_encode(array('status' => 5));
        echo $json;
        exit();
      }
      if(overlap_check('members', 'member_id', $member_id)){
        $json = json_encode(array('status' => 6));
        echo $json;
        exit();
      }
      if(overlap_check('members', 'member_nickname', $member_nickname)){
        $json = json_encode(array('status' => 7));
        echo $json;
        exit();
      }
      if(!(valid_check('valid_code', 'code', $code))){
        $json = json_encode(array('status' => 9));
        echo $json;
        exit();
      }
      if(!(valid_check('valid_code', 'valid', 1))){
        $json = json_encode(array('status' => 10));
        echo $json;
        exit();
      }
      $codeInfo_check_query = "SELECT * FROM `valid_code` WHERE `code`= '$code'";
      $result = db($codeInfo_check_query)->fetch_array(MYSQLI_ASSOC);
      $member_level = $result['member_level'];
      $member_enrolled = $result['member_enrolled'];
      $member_grade = $result['member_grade'];
      $member_class = $result['member_class'];
      $member_studentNo = $result['member_studentNo'];
      $member_name = $result['member_name'];
      $register_query = "INSERT INTO `members` values (0, '$member_level', '$member_id', '$member_pw', '$member_nickname', now(), '$member_enrolled', '$member_grade', '$member_class', '$member_studentNo', '$member_name');";
      db($register_query);
      if(login_check($member_id, $member_pw)){
        $code_expire_query = "UPDATE `valid_code` SET `valid`=0 WHERE `code`= '$code'";
        $result = db($code_expire_query);
        $json = json_encode(array('status' => 1));
        echo $json;
      }else {
        $json = json_encode(array('status' => 11));
        echo $json;
        exit();
      }
    break;
    case 'authentication':
      if(isset($_SESSION['member_auth'])){
        $member_code = $_SESSION['member_auth'];
        $code = $_POST['code'];
        if(!(valid_check('valid_code', 'code', $code))){
          $json = json_encode(array('status' => 9));
          echo $json;
          exit();
        }
        if(!(valid_check('valid_code', 'valid', 1))){
          $json = json_encode(array('status' => 10));
          echo $json;
          exit();
        }
        $codeInfo_check_query = "SELECT * FROM `valid_code` WHERE `code`= '$code'";
        $result = db($codeInfo_check_query)->fetch_array(MYSQLI_ASSOC);
        $member_level = $result['member_level'];
        $member_enrolled = $result['member_enrolled'];
        $member_class = $result['member_class'];
        $member_studentNo = $result['member_studentNo'];
        $authentication_query = "UPDATE `members` SET `member_level`='$member_level', `member_enrolled`='$member_enrolled', `member_class`='$member_class', `member_studentNo`='$member_studentNo' WHERE `member_code`=$member_code;";
        db($authentication_query);
        $code_expire_query = "UPDATE `valid_code` SET `valid`=0 WHERE `code`= '$code'";
        $result = db($code_expire_query);
        session_destroy();
        $json = json_encode(array('status' => 1));
        echo $json;
      }else{
        $json = json_encode(array('status' => 2));
        echo $json;
        exit();
      }
    break;
    case 'member':
      if ($_POST['member_code']==null) {
        echo "<script>alert('멤버코드가 없습니다.');history.go(-1);</script>";
        exit();
      }else{
        $member_code = $_POST['member_code'];
        $member_query = "SELECT * from members WHERE member_code=$member_code";
        $result = db($member_query);
        $member_info=$result->fetch_array(MYSQLI_ASSOC);
        $json = json_encode(array('status' => 1, 'member_code' => $member_info['member_code'], 'member_id' => $member_info['member_id'], 'member_nickname' => $member_info['member_nickname'], 'member_level' => $member_info['member_level'], 'member_created' => $member_info['member_created'], 'member_enrolled' => $member_info['member_enrolled'], 'member_grade' => $member_info['member_grade'], 'member_class' => $member_info['member_class'], 'member_studentNo' => $member_info['member_studentNo'], 'member_name' => $member_info['member_name']));
        echo $json;
      }
    break;
    case 'search':
      if ($_POST['searchQuery']==null||$_POST['searchType']==null||$_POST['searchType']==null) {
        echo "<script>alert('검색어가 없습니다.');history.go(-1);</script>";
        exit();
      }else{
        switch ($_POST['searchType']){
          case 'board':
            $searchType=$_POST['searchType'];
            break;
          case 'blog':
            $searchType=$_POST['searchType'];
        }
        switch($searchType){
          case 'board':
          case 'blog':
            $searchQuery = $_POST['searchQuery'];
            $query = "SELECT `post_no`, `post_title`, `member_nickname`, `post_date` FROM $searchType WHERE MATCH(post_title, post_content) AGAINST('$searchQuery' IN BOOLEAN MODE) AND `post_deleted`=0 ORDER BY `post_no` DESC";
            $result = db($query);
            $arr_searchResult=array();
            for($i=1;$i<=$result->num_rows;$i++){
              $searchResult=$result->fetch_array(MYSQLI_ASSOC);
              $arr_searchResult[$i]['postNo']=$searchResult['post_no'];
              $arr_searchResult[$i]['postTitle']=htmlspecialchars($searchResult['post_title'],ENT_QUOTES,'UTF-8');
              $arr_searchResult[$i]['memberNickname']=htmlspecialchars($searchResult['member_nickname'],ENT_QUOTES,'UTF-8');
              $arr_searchResult[$i]['postDate']=$searchResult['post_date'];
            }
            $json = json_encode($arr_searchResult);
            echo $json;
            break;
          default:
            echo "<script>alert('잘못된 검색 대상입니다.');history.go(-1);</script>";
            exit();
            break;
        }
      }
      break;
    case 'board':
      if($_POST['boardType']==null){

      }else{
        switch ($_POST['boardType']){
          case 'board':
            if(!(isset($_SESSION['member_code']))){
              $json = json_encode(array('status' => 21));
              echo $json;
              exit();
            }
            $boardType=$_POST['boardType'];
            break;
          case 'blog':
            $boardType=$_POST['boardType'];
            break;
        }
        if(isset($_POST['page_no'])){
          if($_POST['page_no']>0){
            $page_no = $_POST['page_no'];
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

        $arr_board=array();
        for($i=0;$i<$result->num_rows;$i++){
          $board=$result->fetch_array(MYSQLI_ASSOC);
          array_push($arr_board, array('boardType' => $boardType, 'postNo' => $board['post_no'], 'postTitle' => htmlspecialchars($board['post_title'],ENT_QUOTES,'UTF-8'), 'postComments' => $board['post_comments'], 'memberCode' => $board['member_code'], 'memberNickname' => htmlspecialchars($board['member_nickname'],ENT_QUOTES,'UTF-8'), 'postDate' => $board['post_date'], 'postHit' => $board['post_hit'], 'post_like' => $board['like']));
        }
        $page_num="";
        if ($page_no > 1){
          $pre_page=$page_no-1;
          $page_num=$page_num."<a href='/board?boardType=$boardType&page_no=1'>처음</a>";
          $page_num=$page_num."<a href='/board?boardType=$boardType&page_no=$pre_page'>◀</a>";
        }
        for($i=$block_start;$i<=$block_end;$i++){
          if($page_no==$i){
            $page_num=$page_num."<p class='active'>$i</p>";
          }else{
            $page_num=$page_num."<a href='/board?boardType=$boardType&page_no=$i'>$i</a>";
          }
        }
        if($page_no<$total_page){
          $next_page=$page_no+1;
          $page_num=$page_num."<a href='/board?boardType=$boardType&page_no=$next_page'>▶</a>";
          $page_num=$page_num."<a href='/board?boardType=$boardType&page_no=$total_page'>마지막</a>";
        }
        $json = json_encode(array('status' => 1, 'arr_board' => $arr_board, 'page_num' => $page_num), JSON_PRETTY_PRINT);
        echo $json;
      }
    break;
    case 'post':
      if($_POST['boardType']==null){

      }else{
        switch ($_POST['boardType']){
          case 'board':
            if(!(isset($_SESSION['member_code']))){
              $json = json_encode(array('status' => 21));
              echo $json;
              exit();
            }
            $boardType=$_POST['boardType'];
            $like_boardType='board_like';
            break;
          case 'blog':
            $boardType=$_POST['boardType'];
            $like_boardType='blog_like';
            break;
          case 'music':
            $boardType=$_POST['boardType'];
            $like_boardType='music_like';
            break;
        }
        if ($_POST['post_no']==null) {
          $json = json_encode(array('status' => 17));
          echo $json;
          exit();
        }else{
          $post_no = $_POST['post_no'];
          $post_query = "SELECT * from $boardType where post_no=$post_no";
          $result = db($post_query);
          $post=$result->fetch_array(MYSQLI_ASSOC);
          if($post['post_deleted']==0){
            $post_hit_query = "UPDATE $boardType set post_hit=post_hit+1 where post_no=$post_no";
            db($post_hit_query);
            $post_title=htmlspecialchars($post['post_title'],ENT_QUOTES,'UTF-8');
            $post_content=$post['post_content'];
            $member_code=$post['member_code'];
            $member_nickname=$post['member_nickname'];
            $post_comments=$post['post_comments'];
            $post_hit=$post['post_hit'];
            $post_date=$post['post_date'];
            $post_like=$post['like'];

            $like_check_query = "SELECT `like` FROM `$like_boardType` WHERE `post_no`= $post_no AND `member_code`=".$_SESSION['member_code'];
            $result = db($like_check_query);
            if($result->num_rows){
              $like_check = db($like_check_query)->fetch_array(MYSQLI_ASSOC);
              $like=$like_check['like'];
            }else{
              $like=0;
            }
            $json = json_encode(array('status' => 1, 'post_title' => $post_title, 'post_content' => $post_content, 'member_code' => $member_code, 'member_nickname' => $member_nickname, 'post_comments' => $post_comments, 'post_hit' => $post_hit, 'post_like' => $post_like, 'like' => $like, 'post_date' => $post_date));
            echo $json;
          }else{
            $json = json_encode(array('status' => 18));
            echo $json;
          }
        }
      }
    break;
    case 'post_write':
      if ($_POST['post_title']==null||$_POST['post_content']==null||$_POST['boardType']==null) {
        $json = json_encode(array('status' => 2));
        echo $json;
        exit();
      }else{
        switch ($_POST['boardType']){
          case 'board':
            $boardType=$_POST['boardType'];
            break;
          case 'blog':
            if($_SESSION['member_code']!=1){
              $json = json_encode(array('status' => 2));
              echo $json;
              exit();
            }
            $boardType=$_POST['boardType'];
        }
        if(isset($_SESSION['member_code'])){
          require_once '/lib/html_purifier.php';
          $member_code = $_SESSION['member_code'];
          $member_nickname = $_SESSION['member_nickname'];
          $post_title = $_POST['post_title'];
          $post_content = html_purifier($_POST['post_content']);
          if(isset($_POST['post_no'])){
            $post_no=$_POST['post_no'];
            $post_check_query = "SELECT `member_code` FROM `$boardType` WHERE `post_no`= $post_no";
            $result = db($post_check_query)->fetch_array(MYSQLI_ASSOC);
            $member_code_check = $result['member_code'];
            if($member_code_check==$member_code){
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
    break;
    case 'post_delete':
      if ($_POST['post_no']==null||$_POST['boardType']==null) {
        echo "<script>alert('게시글 번호가 없습니다.');history.go(-1);</script>";
        exit();
      }else{
        switch ($_POST['boardType']){
          case 'board':
            $boardType=$_POST['boardType'];
            break;
          case 'blog':
            $boardType=$_POST['boardType'];
        }
        if(isset($_SESSION['member_code'])){
          $post_no = $_POST['post_no'];
          $post_check_query = "SELECT `member_code` FROM `$boardType` WHERE `post_no`= $post_no";
          $result = db($post_check_query)->fetch_array(MYSQLI_ASSOC);
          $member_code = $result['member_code'];
          if($member_code==$_SESSION['member_code']){
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
    break;
    case 'comment':
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
    break;
    case 'comment_write':
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
      break;
    case 'like':
      if ($_POST['like']==null||$_POST['boardType']==null) {
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
            $like_boardType='board_like';
            break;
          case 'music':
            $boardType=$_POST['boardType'];
            $like_boardType='music_like';
        }
        if(isset($_SESSION['member_code'])){
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
          $json = json_encode(array('status' => 2));
          echo $json;
          exit();
        }
      }
      break;
    case 'food':
      require_once 'db_commands/food.php';
      break;
    default:
      $json = json_encode(array('status' => 2));
      echo $json;
      exit();
    break;
  }
?>