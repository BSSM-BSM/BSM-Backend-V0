<?php
header('Content-Type: text/html; charset=UTF-8');
function load($url, $data, $isPost, $strcookie){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    if($isPost){
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($ch, CURLOPT_POST, true);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($strcookie));
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
$data = array(
    'caseBy' => 'login',
    'pw' => $_POST['pw'],
	'lgtype' => 'S',
	'hakgwa' => '공통과정',
	'hak' => $_POST['hak'],
	'ban' => $_POST['ban'],
	'bun' => $_POST['bun']
);
$response = load("https://bssm.meistergo.co.kr/inc/common_json.php", $data, true, '');
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
$cookies = array();
foreach($matches[1] as $item) {
    parse_str($item, $cookie);
    $cookies = array_merge($cookies, $cookie);
}
$strcookie = "Cookie: PHPSESSID=".$cookies["PHPSESSID"];
$data = array(
    'caseBy' => 'listview',
    'pageNumber' => 1,
	'onPageCnt' => '20',
);
echo mb_convert_encoding(load("https://bssm.meistergo.co.kr/ss/ss_a40j.php", $data, true, $strcookie), "UTF-8","EUC-KR");
load("https://bssm.meistergo.co.kr/logout.php", array(), false, $strcookie);
?>