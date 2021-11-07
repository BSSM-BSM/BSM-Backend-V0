<?php
if ($_POST['os']==NULL||$_POST['app']==NULL) {
    statusCode(2);
}else{
    switch($_POST['app']){
        case 'web':
            $json = json_encode(array('status' => 1, 'versionCode' => 1, 'versionName' => '0.5'));
            echo $json;
            break;
        case 'app':
            switch($_POST['os']){
            case 'android':
                $json = json_encode(array('status' => 1, 'versionCode' => 6, 'versionName' => 'Beta 0.2.3'));
                echo $json;
                break;
            default:
                statusCode(2);
            }
            break;
        default:
            statusCode(2);
            break;
    }
}
?>