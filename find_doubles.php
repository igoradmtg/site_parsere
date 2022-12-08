<?php
require('inc/str.php');
$files = dir_to_array_nr('magn',true);
$strSave ='';
$arXt = [];
foreach($files as $fname) {
    $fbody = file_get_contents($fname);
    if ($fbody==false) continue;
    $ar_url = parse_url($fbody);
    //print_r($ar_url);
    if (!isset($ar_url['query'])) {
        continue;
    }
    $magnet_url_query = $ar_url['query'];
    parse_str($magnet_url_query, $output);
    //print_r($output);
    if (!isset($output['xt'])) {
        continue;
    }
    $strDelete = '';
    $xt = trim($output['xt']);
    echo "$fname $xt" . PHP_EOL;
    $isDelete = false;
    if (in_array($xt,$arXt)) {
        $isDelete = true;
        $strDelete = 'DELETE';
    } else {
        $arXt[] = $xt;
    }
    if (isset($output['dn'])) {
        $dName = trim($output['dn']);
    }
    $strSave .= "$strDelete $fname $dName \r\n";
    if ($isDelete) {
        unlink($fname);
    }

}
file_put_contents('find_doubles.txt',$strSave);