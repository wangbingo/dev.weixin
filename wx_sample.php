<?php
/**
  * wechat php test
  */
require_once("common/global.php");
global $Config;
define("MYSQLPWD", $Config["DB_PWD"]); 
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
    $con=mysql_connect("127.0.0.1", "root", MYSQLPWD);
		mysql_select_db("weixin") or die("Unable to select database");
		mysql_query("SET NAMES UTF8");    		
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $msgType = $postObj->MsgType;
                 
                if ($msgType=="image"){
		
						        $textTpl = "<xml>
																<ToUserName><![CDATA[%s]]></ToUserName>
																<FromUserName><![CDATA[%s]]></FromUserName>
																<CreateTime>%s</CreateTime>
																<MsgType><![CDATA[%s]]></MsgType>
																<Content><![CDATA[%s]]></Content>
																<FuncFlag>0</FuncFlag>
																</xml>";                 
		
		
											$contentStr = "欢迎来到微信MOA系统：\r\n\r\n您刚才上传的图片已经保存在微信服务器上，浏览地址是：".$postObj->PicUrl; 
						          $time = time();
						          $msgType = "text";
						          $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						          echo $resultStr;	
                	
                }  
                 
                if ($msgType=="text"){ 
                   $keyword = $postObj->Content;
                   $CreateTime = time();
                   $FuncFlag =  1 ;


                   if ($keyword=="景点"){  
                    
							        $newTplHeader = "<xml>
							            <ToUserName><![CDATA[{$fromUsername}]]></ToUserName>
							            <FromUserName><![CDATA[{$toUsername}]]></FromUserName>
							            <CreateTime>{$CreateTime}</CreateTime>
							            <MsgType><![CDATA[news]]></MsgType>
							            <ArticleCount>%s</ArticleCount><Articles>";
							        $newTplItem = "<item>
							            <Title><![CDATA[%s]]></Title>
							            <Description><![CDATA[%s]]></Description>
							            <PicUrl><![CDATA[%s]]></PicUrl>
							            <Url><![CDATA[%s]]></Url>
							            </item>";
							        $newTplFoot = "</Articles>
							            <FuncFlag>%s</FuncFlag>
							            </xml>"; 
  													 
  										
  										$sql="select * from images order by ids limit 0,10";
  										$result=mysql_query($sql);	
  										$rsNextman=mysql_fetch_assoc($result);	
  										
  										$itemsCount=0;
  										$Content = "";
  										while($rsNextman){
  										  $itemsCount= $itemsCount+1;
  										  $Content .=sprintf($newTplItem,$rsNextman["title"],$rsNextman["description"],$rsNextman["picUrl"],$rsNextman["url"]);  										
  											$rsNextman=mysql_fetch_assoc($result);  
  										}
 										 
							        $header = sprintf($newTplHeader,$itemsCount);
							        $footer = sprintf($newTplFoot,$FuncFlag);
							        echo $header . $Content . $footer; 													                     
                     
                   }
		

                	
                }                  
                 
                 


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