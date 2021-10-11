<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
function getParam($index){
    $param = explode('/', explode('?',$_SERVER['REQUEST_URI'])[0]);
    if(isset($param[$index]))
        return $param[$index];
    else
        return NULL;
}
if(getParam(1)){
    $page = getParam(1);
}else{
    $page = '404';
}
switch($page){
    case 'database':
    case 'database.php':
        require_once './database.php';
        break;
    case 'logout':
    case 'logout.php':
        require_once './logout.php';
        break;
    case 'image_upload':
    case 'image_upload.php':
        require_once './image_upload.php';
        break;
    case 'app':
        if(getParam(2)){
            $page = getParam(2);
        }else{
            $page = 'index';
        }
        require_once './app/index.php';
        break;
    default:
        require_once './index.php';
        break;
}
?>