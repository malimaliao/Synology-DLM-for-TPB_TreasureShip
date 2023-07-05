<?php
include_once "../search.php";

$test = new TreasureShip();

# test1
# print_r($test -> getPage('https://test.pro/'));


# test2
#print_r($test -> VerifyAccount('https://test.pro/',''));


# test3
/**
$tpb_default_b64 = "aHR0cHM6Ly9waXJhdGViYXkucHJv";
$regex = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';
if(preg_match($regex,'')){
    echo 'TPB网址(匹配成功)';
}else{
    echo 'TPB网址(匹配失败)'.base64_decode($tpb_default_b64);;
}
 */

# test 4

/**
$response = file_get_contents("tpb_debug.log");
$regexp2 = "<tr>(.+?)<\/tr>";
if (preg_match_all("/$regexp2/is", $response, $matches2, PREG_SET_ORDER)) {
    echo 'ok';
    foreach ($matches2 as $match2) {
        echo '+1';
    }
}else{
    echo 'no';
}
*/

# test 5
#/**
#echo date("Y-m-d H:i:s", strtotime('-1 years'));
echo $test -> format_tpb_trackers('./tracker.txt');
#*/

# test101
#/**
$curl_101 = curl_init();
$test -> prepare($curl_101,'godfather','','');
$res_101 = curl_exec($curl_101);
if (curl_errno($curl_101)) {
    $res_101 =  curl_error($curl_101);
}
curl_close($curl_101);
$out_101 = $test -> parse('101',$res_101);

echo 'count: '.$out_101;
#*/


echo PHP_EOL.'<br>=== end ===';

?>