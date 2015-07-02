

<?php

//将微信发过来的xml数据接收到，赋值给$postStr
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

var_dump($postStr);

exit();


//fopen(文件路径，打开模式)  打开指定目录的某个文件（追加的模式）

//用户写入文件的，不删除原有的内容，向文件的最后，追加新的内容。

//fwrite(向哪个资源当中写入，)

$fp = fopen('/var/www/html/loc.txt', 'a+');

fwrite($fp, $postStr);

fclose($fp);

//var_dump();
