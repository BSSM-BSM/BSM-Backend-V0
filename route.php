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
if($page=='database'||$page=='database.php')
    require_once './database.php';
else
    require_once './index.php';
?>