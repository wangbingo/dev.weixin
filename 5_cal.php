<?php
/**
  * wechat php test
  */

require_once("common/global.php") ; 
global $Config;
define("MYSQLPWD",$Config["DB_PWD"]);

//define your token
define("TOKEN", "weixin888");
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
    
    $con=mysql_connect("127.0.0.1","root",MYSQLPWD); 	
    mysql_select_db("weixin") or die("Unable to select database");
    mysql_query("SET NAMES UTF8");
  
 		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
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
              		
              		if (is_numeric($keyword)){
		              		$sql="select * from score where ids=".$keyword ;
		              		$result=mysql_query($sql);
		 
		              		$rsNextman=mysql_fetch_assoc($result);
		              		if ($rsNextman){
				                	$contentStr = "准考证号是 ".$keyword." ,".$rsNextman["names"]."同学! \r\n您本次语文成绩是".$rsNextman["yw"]."分;\r\n您本次数学成绩是".$rsNextman["sx"]."分\r\n您本次英语成绩是".$rsNextman["yy"]."分\r\n您本次化学成绩是".$rsNextman["hx"]."分\r\n您本次物理成绩是".$rsNextman["wl"]."分";
				                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				                	echo $resultStr;                			
		              		}else{
				                	$contentStr = "没有找到该准考证号码，Welcome to ".$keyword." 世界! 这是一个来自".$fromUsername."消息，它发送给公众帐号:".$toUsername;
				                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				                	echo $resultStr;              			
		              		}     
		              		mysql_close($con);         			
              			
              		}else{
              			
              			if ($keyword=="?"||$keyword=="？"){
			                	$contentStr = "欢迎使用高考成绩模拟查询系统，输入您的准考证编号(1-10)并点击发送，可查看到您的高考成绩。\r\n\r\n另外，我们还提供了黄道吉日的查询，输入诸如2013.6.8此类的日期，点击发送，可看到您指定日期的黄道吉日查询结果。";
			                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
			                	echo $resultStr;               				
              			}else{
              			
              					$V=explode(".",$keyword); 
              					if (count($V)==3){
              							$sql="select * from jixiong where years=".$V[0]." and months=".$V[1]." and days=".$V[2];
              							$result=mysql_query($sql);
              							$rsNextman=mysql_fetch_assoc($result);
              							if ($rsNextman){
					                		$contentStr = $keyword."的黄道吉日是：\r\n\r\n宜：".$rsNextman["ji"]."\r\n\r\n忌：".$rsNextman["xiong"];              							
              							}else{
						                	$contentStr = "无此数据";              							
              							} 
              							
              							

					                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					                	echo $resultStr;               					
              					
              					}else{
					                	$contentStr = "非数字型，Welcome to ".$keyword." 世界! 这是一个来自".$fromUsername."消息，它发送给公众帐号:".$toUsername;
					                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					                	echo $resultStr;               					
              					}
              			
              			
              				
              				
              			} 
              				 
              				  
              				  
   
		                	             			
              		}
              		

              		

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