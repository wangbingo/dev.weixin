

<?php

//echo sqrt(81000000000000);
//exit;

/*
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

if (!empty($postStr)){

$fp = fopen('/var/www/html/loc.txt', 'a+');

fwrite($fp, $postStr);

fclose($fp);
}else{
    echo "poststr is empty.";
}

*/

include ('lbs_fns.php');

define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){

            //$fp = fopen('/tmp/loc.txt', 'a+');

            //fwrite($fp, $postStr);

            //fclose($fp);

            //exit;
            /*
            <xml>
            <ToUserName><![CDATA[gh_d9424e8e6cc5]]></ToUserName>
            <FromUserName><![CDATA[o9kEMuNyg1_-xtG2j45lizHHrz58]]></FromUserName>
            <CreateTime>1436167671</CreateTime>
            <MsgType><![CDATA[event]]></MsgType>
            <Event><![CDATA[LOCATION]]></Event>
            <Latitude>30.543175</Latitude>
            <Longitude>104.052330</Longitude>
            <Precision>65.000000</Precision>
            </xml>
            */
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $latitude = $postObj->Latitude;
                $longitude = $postObj->Longitude;
                $time = time();
                $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";             
                if(!empty( $latitude ))
                {
                    $msgType = "text";
                    $LONG = 104.051438;
                    $LAT = 30.542164;
                    $long = ((double)$longitude-(double)$LONG)*100000;
                    $lat = ((double)$latitude-(double)$LAT)*100000;
                    $distance = round(sqrt( $long * $long  + $lat * $lat ));
                    //$distance = getdistance($latitude, $longitude, 30.657366, 104.065841);
                    //$distance = getdistance(121.40233369999998, 31.2014966, 121.44552099999998, 31.22323799999999);
                    //$distance = 100;
                    $contentStr = "亲爱的".$fromUsername."。您的经度是：".$longitude."，您的纬度是：".$latitude."。距离西信：".$distance."米";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }else{
                    echo "Input something...";
                }

        }else {
            echo "";
            exit;
        }
    }
        
    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
                
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}




?>