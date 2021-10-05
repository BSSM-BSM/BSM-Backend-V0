<?php
$member_id = $_SESSION['member_id'];
$member_pw = hash('sha3-256', $_POST['member_pw']);
if(login_check($member_id, $member_pw)){
    $modify_type = $_POST['modify_type'];
    if ($_POST['modify_type']==null)
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
?>