<?php

$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

file_put_contents('/var/www/html/demo.txt', $postStr);

//var_dump($postStr);

