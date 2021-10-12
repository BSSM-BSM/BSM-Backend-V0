<?php
if ($_POST['searchQuery']==null||$_POST['searchType']==null||$_POST['searchType']==null) {
    $json = json_encode(array('status' => 15));
    echo $json;
    exit();
}else{
    switch ($_POST['searchType']){
        case 'board':
            $searchType=$_POST['searchType'];
            break;
        case 'anonymous':
            $searchType=$_POST['searchType'];
    }
    switch($searchType){
        case 'board':
        case 'anonymous':
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
?>