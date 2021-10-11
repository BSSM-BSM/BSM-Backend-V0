<?php
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
    $page = 'index';
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
    default:
        require_once './index.php';
        break;
}
?>