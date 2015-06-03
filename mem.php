<?php

define('APP_ID', 'wxa2fcb958aa1654e1');

define('APP_SECRET', 'ae2f3bb2ad98fa86eccc6d147fb5e095');


function mem_token() {

    $mmc = memcache_init();

    $token = $mmc->get('token');

    if (!empty($token)) {

        return $token;

    } else {

        $token = get_token();

//                               不压缩
        $mmc->set('token', $token, 0, 7000);
    }

    return $token;

}



//获取当前令牌
function get_token() {

    $ch = curl_init();

    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APP_ID.'&secret='.APP_SECRET;

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_setopt($ch, CURLOPT_TIMEOUT, 3);

    $output = curl_exec($ch);

    curl_close($ch);

    $obj = json_decode($output, true);

    return $obj['access_token'];

}









