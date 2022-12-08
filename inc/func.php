<?php

// Возвращает запрос SQLITE для создания таблицы данных
function get_create_table_main() {
    return "CREATE TABLE IF NOT EXISTS tablelinks(
    url TEXT PRIMARY KEY 
    ) WITHOUT ROWID";
}

// Сохраняем данные в базу данных
function save_text_to_db($url) {
    GLOBAL $fname_sqlite,$sqlite_last_insert_rowid;
    $sqlite_last_insert_rowid = false;
    if(!file_exists($fname_sqlite)) {
        // Создаем все таблицы нужные для работы
        $db = new SQLite3($fname_sqlite);
        $db->query(get_create_table_main()); 
    } else {$db = new SQLite3($fname_sqlite);}
    $st=$db->prepare('insert into tablelinks (url) values (?)');
    if ($st == false) {
        add_log("Error prepare $fname_sqlite");
        return false;
    }
    $st->bindParam(1, $url, SQLITE3_TEXT);
    $r = $st->execute();
    if ($r==false) {
        add_log("Error update sqlite3");
        return false;
    }
    $sqlite_last_insert_rowid = $db->lastInsertRowid();
    $db->close();
    return true;
}

// Поиск данных в таблице
function sqlite_get_url($url) {
    GLOBAL $fname_sqlite;
    if(!file_exists($fname_sqlite)) {add_log("Not found file $fname_sqlite");return false;} else {$db = new SQLite3($fname_sqlite);}
    $st=$db->prepare('SELECT * FROM tablelinks WHERE url = ? ');
    if ($st==false) return false;
    $st->bindParam(1, $url, SQLITE3_TEXT);
    $r = $st->execute();
    if ($r==false) {add_log($db->lastErrorMsg());return false;}
    $ret=false;
    while ($row = $r->fetchArray(SQLITE3_ASSOC)) {
        $ret=$row;
    }
    $db->close();
    return $ret;
}

function get_file_url($url,$file) {
    $ch = curl_init();
    $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36'; 

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $out = curl_exec($ch);
    curl_close($ch);
    $fp = fopen($file, 'wb');
    if ($fp == false) {
        return false;
    }
    fwrite($fp, $out);
    fclose($fp);  
    return true;
}
function post_url($url,$post_array) {
    $ch = curl_init();
    $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36'; 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_array);
    $out = curl_exec($ch);
    curl_close($ch);
    return $out;
}

