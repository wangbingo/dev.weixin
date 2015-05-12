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
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $msgType = $postObj->MsgType;


                $textTpl = " <xml>
 															<ToUserName><![CDATA[%s]]></ToUserName>
  														<FromUserName><![CDATA[%s]]></FromUserName>
  														<CreateTime>%s</CreateTime>
  														<MsgType><![CDATA[news]]></MsgType>
  														<ArticleCount>2</ArticleCount>
  															<Articles>
  															  <item>
  															    <Title><![CDATA[图片一]]></Title> 
  															    <Description><![CDATA[图片一描述]]></Description>
  															    <PicUrl><![CDATA[http://www.image1.cn/upload/docnews/12/20130527/12_1369639156.jpg]]></PicUrl>
  															    <Url><![CDATA[http://www.image1.cn/h/12/20130521/14685_5.html]]></Url>
  															  </item>
  															  <item>
  															    <Title><![CDATA[图片二]]></Title> 
  															    <Description><![CDATA[图片二描述]]></Description>
  															    <PicUrl><![CDATA[http://www.image1.cn/upload/docnews/12/20130521/p17r36qv371jpt1j9u17kp49f15ub6.jpg]]></PicUrl>
  															    <Url><![CDATA[http://www.image1.cn/h/12/20130521/14685_3.html]]></Url>
  															  </item>
  														  </Articles>
   														<FuncFlag>1</FuncFlag>
  													 </xml> "; 
								$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time);
						    echo $resultStr; 
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