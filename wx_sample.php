

<?php

//将微信发过来的xml数据接收到，赋值给$postStr
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

if (!empty($postStr)){

$fp = fopen('/var/www/html/loc.txt', 'a+');

fwrite($fp, $postStr);

fclose($fp);
}else{
    echo "poststr is empty.";
}

//var_dump();
?>