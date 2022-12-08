<?php
require('inc/str.php');
require('inc/func.php');
require('inc/simplehtmldom/simple_html_dom.php');

$isLoadPage = true;
$isLoadPage2 = true;
$maxPage = 50;
$startMagnetFile = 80000;// dvp dap 
delete_all_files_in_dir('tmp');
delete_all_files_in_dir('tmpb');
if ($isLoadPage) {
for($page=1;$page<=$maxPage;$page++) {
    $tmpFile='tmpb/tmp_'.add_zero($page,5).'.html';
    $url = 'https://rarbgprx.org/search/'.$page.'/?search=exxxtrasmall&category=xxx';// deep%20throat
    echo $url . PHP_EOL;
    get_file_url($url,$tmpFile);
}
}
if ($isLoadPage2) {
$arUrls = [];
for($page=1;$page<=$maxPage;$page++) {
    $tmpFile='tmpb/tmp_'.add_zero($page,5).'.html';
    echo $tmpFile . PHP_EOL;
    $fbody = file_get_contents($tmpFile);
    if ($fbody==false) continue;
    $html = str_get_html($fbody);
    $elemets = $html->find('a');
    foreach($elemets as $el) {
        $href = trim($el->href);
        if (strpos($href,'/torrent/')===false) continue;
        echo $href . PHP_EOL;
        $newUrl = 'https://www.rarbgo.to' . $href;
        if (in_array($newUrl,$arUrls)==false) {
            $arUrls[] = $newUrl;
        }         
    }
}
$cnt = 1;
foreach($arUrls as $url) {
    $tmpFile='tmp/tmp_'.add_zero($cnt,5).'.html';
    echo $url . PHP_EOL;
    get_file_url($url,$tmpFile);
    $cnt++;
}

}
$cnt = $startMagnetFile;
$files = dir_to_array_nr('tmp',true);
foreach($files as $fname) {
    $fbody = file_get_contents($fname);
    if ($fbody==false) continue;
    $html = str_get_html($fbody);
    $elemets = $html->find('a');
    foreach($elemets as $el) {
        $href = trim($el->href);
        if (strpos($href,'magnet:')!==0) continue;
        echo $href . PHP_EOL;
        file_put_contents('magn/rarbg'.add_zero($cnt,5).'.magnet',$href);
        $cnt++;
    }

}
