<?php
/**
    * wechat php test
    */

//define your token
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
                    /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                    the best way is to check the validity of xml by yourself */
                    libxml_disable_entity_loader(true);
                    $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                    $fromUsername = $postObj->FromUserName;
                    $toUsername = $postObj->ToUserName;
                    $msgType = trim($postObj->MsgType);
                    $keyword = trim($postObj->Content);
                    $time = time();
                    $textTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                <FuncFlag>0</FuncFlag>
                                </xml>";             
                    if(!empty( $keyword ))
                    {
                        $msgType = "text";
                        $contentStr = "Welcome to ".$keyword." 世界! \r\n这是一个来自".$fromUsername."消息。\r\n它发送给公众帐号:".$toUsername."\r\n时间是：".date("Y-m-d H-i-s", $time);
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }else{
                        if($msgType=="event") {
                            $msgType = "text";
                            $contentStr = "Welcome to 微信开发者的世界! 输入?号可看到帮助说明。这是一个来自".$fromUsername."消息，它发送给公众帐号:".$toUsername;
                            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                            echo $resultStr;                      
                        }else{
                            echo "Input something...";
                        }
                    }

                }else {

                    echo "";
                    exit;
                }
            }

            private function checkSignature()
            {
                $signature = $_GET["signature"];
                $timestamp = $_GET["timestamp"];
                $nonce = $_GET["nonce"];    

                $token = TOKEN;
                $tmpArr = array($token, $timestamp, $nonce);
                sort($tmpArr);
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
