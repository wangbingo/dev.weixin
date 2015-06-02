<?php

define('APP_ID', 'wxa2fcb958aa1654e1');

define('APP_SECRET', 'ae2f3bb2ad98fa86eccc6d147fb5e095');

function get_file_token() {

    var_dump(exists_token());

    exit;

    if (exists_token()){

        if (expire_token()) {

            unlink('token.txt');

            $token = get_token();

            file_put_contents('token.txt', $token);

            return $token;

        } else {

            $token = file_get_contents('token.txt');

            return $token;

        }

    } else {
        
        $token = get_token();

        file_put_contents('token.txt', $token);

        return $token;

    }

}

//判断令牌存放文件是否存在
function exists_token() {
    if (file_exists('token.txt')) {
        return true;
    } else {
        return false;
    }
}

//判断令牌文件是否过期
function expire_token() {
    $ctime = filectime('token.txt');

    if ((time() - $ctime) >= 7000) {
        return true;
    } else {
        return false;
    }
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

    //var_dump($obj['access_token']);

    return $obj['access_token'];

}


$ttttt = get_file_token();

exit;

