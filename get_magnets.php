<?php
require_once (__DIR__ . '/inc/str.php');
require_once (__DIR__ . '/inc/func.php');
require_once (__DIR__ . '/config.php');
require_once (__DIR__ . '/simplehtmldom/HtmlDocument.php');
use simplehtmldom\HtmlDocument;
$start_page = 1;
$max_page = 5;
$max_page_sukebei = 20;

function load_url_get_magnet($load_url,$fname) {
    GLOBAL $dir_torrent,$url_post;
    echo "Get url $load_url" . PHP_EOL;
    get_file_url($load_url,$fname);
    if (!file_exists($fname)) {
        echo "Not found file $fname" . PHP_EOL;
        return false;
    }

    $content = file_get_contents($fname);
    if ($content == false) {
        echo "Error read file $fname" . PHP_EOL;
        return false;
    }
    $html = (new HtmlDocument())->load($content);

    foreach($html->find('a') as $tag_a) {
        $magnet_url = trim($tag_a->href);
        if (strpos($magnet_url,'magnet:')===false) {
            continue;
        }
        //echo trim($tag_a->href) . PHP_EOL;
        $ar_url = parse_url($magnet_url);
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
        $name_file = str_replace(':','_',$output['xt']) . '.magnet';
        $db_hash = str_replace(':','_',$output['xt']);
        $db_hash = str_replace('urn_btih_','',$db_hash);
        //echo "File: $name_file Hash: $db_hash" . PHP_EOL;
        $info = sqlite_get_url($db_hash);
        if ($info != false) {
            echo "Find $db_hash" . PHP_EOL;
            continue;
        }
        save_text_to_db($db_hash);
        
        $post_array = [
            'torrent' => $magnet_url
        ];
        $file_save_magnet = $dir_torrent . '/' . $name_file;
        file_put_contents($file_save_magnet,$magnet_url);
        echo "Save $file_save_magnet " . PHP_EOL;
        //$result = post_url($url_post,$post_array);
        //echo $result;
        //break;
    }
    sleep(3);
}
/*
https://pirate-proxy.pw/search.php?q=category:501
https://tpb23.ukpass.co/search.php?q=category:501
https://piratesbay.tk/search.php?q=category:501
https://thepb.cyou/browse/501/2/3
https://thepb.cyou/browse/501/3/3
*/

//$url = 'https://rargb.to/top100?category=xxx';
$ar_urls = [
    'https://thepiratebay10.org/browse/501/{page}/3',
    'https://thepiratebay10.org/browse/502/{page}/3',
    'https://thepiratebay10.org/browse/503/{page}/3',
    'https://thepiratebay10.org/browse/505/{page}/3',
    'https://thepiratebay10.org/browse/506/{page}/3'
];
foreach($ar_urls as $url_main) {
    if (strpos($url_main,'{page}')!=false) {
        for($page = $start_page; $page <= $max_page; $page ++) {
            $load_url = str_replace('{page}',$page,$url_main);
            $fname = $dir_tmp . '/rarbg_'.$page.'.txt';
            load_url_get_magnet($load_url,$fname);
        }
    } else {
        $load_url = $url_main;
        $fname = $dir_tmp . '/rarbg_0.txt';
        load_url_get_magnet($load_url,$fname);
    }
}

$ar_urls = [
    //'https://sukebei.nyaa.si/?f=0&c=2_1&q=&p={page}',
    //'https://sukebei.nyaa.si/?f=0&c=2_2&q=&p={page}'
    
];
foreach($ar_urls as $url_main) {
    if (strpos($url_main,'{page}')!=false) {
        for($page = $start_page; $page <= $max_page_sukebei; $page ++) {
            $load_url = str_replace('{page}',$page,$url_main);
            $fname = $dir_tmp . '/rarbg_'.$page.'.txt';
            load_url_get_magnet($load_url,$fname);
        }
    } else {
        $load_url = $url_main;
        $fname = $dir_tmp . '/rarbg_0.txt';
        load_url_get_magnet($load_url,$fname);
    }
}
