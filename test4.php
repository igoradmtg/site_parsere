<?php
require_once(__DIR__ . '/inc/str.php');
$dirMagnet = '/usr/www/transmission_client/torrent';
$dirMagnet0 = '/usr/www/transmission_client/torrent0';
if (isset($_GET['filerename'])) {
    $fullName = $dirMagnet . '/' . $_GET['filerename'];
    if (file_exists($fullName)) {
        if (rename($fullName,$dirMagnet0 . '/' .$_GET['filerename'])) {
            echo 'Rename Ok';
        } else {
            echo 'Error rename';
        }
    } else {
        echo 'File not found';
    }
    exit;
}
if (isset($_GET['filedelete'])) {
    $fullName = $dirMagnet . '/' . $_GET['filedelete'];
    if (file_exists($fullName)) {
        if (unlink($fullName)) {
            echo 'Deleted';
        } else {
            echo 'Error delete file';
        }
    } else {
        echo 'File not found';
    }
    exit;
}

$arFiles = dir_to_array_nr($dirMagnet,false);

echo '<html><body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
';
$cnt = 1;
foreach($arFiles as $fileName){
    $magnet_url = file_get_contents($dirMagnet .'/'. $fileName);
    if ($magnet_url == false) continue;
    $dName = '';
    //echo "$fileName  -  $dName<br>";
    //echo '<pre>';
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
    if (isset($output['dn'])) {
        $dName = trim($output['dn']);

    }
     
    //echo '</pre>';
    echo  $fileName  . '  -  ' . '<span id="cnt'.$cnt.'"><a href="#" onClick="return snd(\''.$fileName.'\',\''.$cnt.'\')" target="_blank">' . $dName .'</a> <a href="#" <a href="#" onClick="return del(\''.$fileName.'\',\''.$cnt.'\')" target="_blank">Delete</a></span><br>';
    $cnt++;


}
?>
<script>
function snd(fname,id) {
$.ajax({
    url: '/test4.php',         /* Куда отправить запрос */
    method: 'get',             /* Метод запроса (post или get) */
    dataType: 'html',          /* Тип данных в ответе (xml, json, script, html). */
    data: {filerename: fname},     /* Данные передаваемые в массиве */
    success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
	     //alert(data); /* В переменной data содержится ответ от index.php. */
         $('#cnt'+id).text(data);
    }
});
return false;
}
function del(fname,id) {
$.ajax({
    url: '/test4.php',         /* Куда отправить запрос */
    method: 'get',             /* Метод запроса (post или get) */
    dataType: 'html',          /* Тип данных в ответе (xml, json, script, html). */
    data: {filedelete: fname},     /* Данные передаваемые в массиве */
    success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
	     //alert(data); /* В переменной data содержится ответ от index.php. */
         $('#cnt'+id).text(data);
    }
});
return false;
}
</script>
</body></html>