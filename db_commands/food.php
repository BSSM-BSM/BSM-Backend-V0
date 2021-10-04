<?php
if ($_POST['food_date']==NULL) {
    exit();
}else{
    $food_date = $_POST['food_date'];
    $food_query = "SELECT * from `food` where food_date='$food_date'";
    $result = db($food_query);
    $food=$result->fetch_array(MYSQLI_ASSOC);
    if(isset($food['morning'])){
        $morning=$food['morning'];
    }else{
        $morning="급식이 없습니다.";
    }
    if(isset($food['lunch'])){
        $lunch=$food['lunch'];
    }else{
        $lunch="급식이 없습니다.";
    }
    if(isset($food['dinner'])){
        $dinner=$food['dinner'];
    }else{
        $dinner="급식이 없습니다.";
    }
    $json = json_encode(array('status' => 1, 'food_date' => $food_date, 'morning' => $morning, 'lunch' => $lunch, 'dinner' => $dinner));
    echo $json;
}
?>