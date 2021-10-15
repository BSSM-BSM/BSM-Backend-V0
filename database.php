<?php
  $root_dir=$_SERVER['DOCUMENT_ROOT'];
  $dir='./db_commands';
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
    case 'is_login':
      require_once $dir.'/is_login.php';
      break;
    case 'version':
      require_once $dir.'/version.php';
      break;
    case 'login':
      require_once $dir.'/login.php';
      break;
    case 'modify':
      require_once $dir.'/modify.php';
      break;
    case 'register':
      require_once $dir.'/register.php';
      break;
    case 'reset_pw':
      require_once $dir.'/reset_pw.php';
      break;
    case 'authentication':
      require_once $dir.'/authentication.php';
      break;
    case 'member':
      require_once $dir.'/member.php';
      break;
    case 'search':
      require_once $dir.'/search.php';
      break;
    case 'board':
      require_once $dir.'/board/board_view.php';
      break;
    case 'post':
      require_once $dir.'/board/post_view.php';
      break;
    case 'post_write':
      require_once $dir.'/board/post_write.php';
      break;
    case 'post_delete':
      require_once $dir.'/board/post_delete.php';
      break;
    case 'comment':
      require_once $dir.'/board/comment_view.php';
      break;
    case 'comment_write':
      require_once $dir.'/board/comment_write.php';
      break;
    case 'comment_delete':
      require_once $dir.'/board/comment_delete.php';
      break;
    case 'like':
      require_once $dir.'/board/like.php';
      break;
    case 'node-like':
      if($_POST['like']==null||$_POST['boardType']==null) {
        statusCode(2);
      }
      if($_POST['post_no']==null) {
          statusCode(17);
      }
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
      exec("node $_SERVER[DOCUMENT_ROOT]/db_commands/board/like.js $dbUser $dbPw $db $boardType $like_boardType $post_no $like $member_code", $output);
      echo implode("\n", $output);
      break;
    case 'food':
      require_once $dir.'/food.php';
      break;
    default:
      statusCode(2);
    break;
  }
?>