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


			if ($msgType=="event"){
				$contentStr = "欢迎来到微信MOA订餐系统。\r\n\r\n输入“菜单”或者“menu”并发送，系统将显示本餐馆的美食菜谱。\r\n\r\n输入套餐编号1至5表示您将购买的套餐；\r\n\r\n最后，输入您的手机号码和当前位置即可完成订餐。";                 	
				$msgType = "text";
				$textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
				</xml>"; 						        
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $CreateTime, $msgType, $contentStr);
				echo $resultStr; 	                 	
			}

			if ($msgType=="location"){
				$sql="select * from `users` where  loginname='".$fromUsername."'";
				$result = mysql_query($sql);	      
				$rsNextman=mysql_fetch_assoc($result);
				if ($rsNextman){
					$sqlinsertLists="update users set longitude='".$postObj->Location_Y."',latitude='".$postObj->Location_X."' where loginname='".$fromUsername."'";
					mysql_query($sqlinsertLists);			
					if ($rsNextman["mobile"]=="0"){
						$contentStr = "我们已经收到您上报地理位置。\r\n经度是".$postObj->Location_Y."；\r\n纬度是".$postObj->Location_X."。\r\n请您再填写手机号码并发送给我们，谢谢。";   									      																					
					}else{
						$contentStr = "我们已经收到您上报地理位置。\r\n经度是".$postObj->Location_Y."；\r\n纬度是".$postObj->Location_X."。\r\n我们将在送餐前跟您联系。您之前留下的手机号码是".$rsNextman["mobile"]."。";   									      																					
					}																			        	
				}else{
					$sqlinsertLists="insert into users(loginname,longitude,latitude,create_time) values('".$fromUsername."','".$postObj->Location_Y."','".$postObj->Location_X."',now())";
					mysql_query($sqlinsertLists);		
					$contentStr = "我们已经收到您上报地理位置，经度是".$postObj->Location_Y."，纬度是".$postObj->Location_X."。您是第一次使用本系统，请您填写手机号码并发送给我们，谢谢。"; 											       

				}
				mysql_close($con);
				$msgType = "text";
				$textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
				</xml>"; 						        
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $CreateTime, $msgType, $contentStr);
				echo $resultStr; 								        
			}  

			if ($msgType=="text"){ 
				$keyword = $postObj->Content;
				$CreateTime = time();
				$FuncFlag =  1 ;

				if ($keyword=="?"||$keyword=="？"||$keyword=="帮助"){ 
					$contentStr = "欢迎来到微信MOA订餐系统。\r\n输入“菜单”或者“menu”并发送，系统将显示本餐馆的美食菜谱。\r\n输入套餐编号1至5表示您将购买的套餐；\r\n最后，输入您的手机号码和当前位置即可完成订餐。";
					$msgType = "text";
					$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>"; 						        
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $CreateTime, $msgType, $contentStr);
					echo $resultStr; 	                    	
				}

				if (preg_match("/^1[3458][0-9]{9}$/",$keyword)){ 
					$sql="select * from `users` where  loginname='".$fromUsername."'";
					$result = mysql_query($sql);	      
					$rsNextman=mysql_fetch_assoc($result);
					if ($rsNextman){       
						$sqlinsertLists="update users set mobile='".$postObj->Content."' where loginname='".$fromUsername."'";
						mysql_query($sqlinsertLists);						        
						if ($rsNextman["longitude"]=="0"){
							$contentStr = "我们已经收到您的手机号码“".$postObj->Content."”。请将您当前所在位置发送给我们，谢谢。";  	    																			
						}else{
							$contentStr = "我们已经收到您的手机号码“".$postObj->Content.",我们将在送餐前跟您电话联系。";  																			
						}	
					}else{
						$sqlinsertLists="insert into users(loginname,mobile,create_time) values('".$fromUsername."','".$postObj->Content."',now())";
						mysql_query($sqlinsertLists);					 
						$contentStr = "我们已经收到您的手机号码“".$postObj->Content."” 。您是第一次使用本系统。"; 
					}
					mysql_close($con);
					$msgType = "text";
					$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>"; 						        
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $CreateTime, $msgType, $contentStr);
					echo $resultStr; 		                    
				}

				if ($keyword=="1"||$keyword=="2"||$keyword=="3"||$keyword=="4"||$keyword=="5"){
					$sql="select * from `users` where  loginname='".$fromUsername."'";
					$result = mysql_query($sql);	      
					$rsNextman=mysql_fetch_assoc($result);
					if ($rsNextman){                    
						$sqlinsertLists="update users set demand='".$postObj->Content."',longitude='0',latitude='0',create_time=now() where loginname='".$fromUsername."'";
						mysql_query($sqlinsertLists);
						if ($rsNextman["mobile"]=="0"){
							$contentStr = "您选择预订了套餐“".$postObj->Content."”，请您填写手机号码并发送给我们，谢谢。";  																			
						}else{
							$contentStr = "您选择预订了套餐“".$postObj->Content."”。请将您当前所在位置发送给我们，谢谢。";  																			
						}													
					}else{
						$sqlinsertLists="insert into users(loginname,demand,create_time) values('".$fromUsername."','".$postObj->Content."',now())";
						mysql_query($sqlinsertLists);   		
						$contentStr = "您选择预订了套餐“".$postObj->Content."” 。您是第一次使用本系统，请您填写手机号码并发送给我们，谢谢。"; 												  									
					}
					mysql_close($con);
					$msgType = "text";
					$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>"; 						        
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $CreateTime, $msgType, $contentStr);
					echo $resultStr; 			
				}

				if ($keyword=="菜单"||$keyword=="menu"||$keyword=="m"||$keyword=="菜"){  

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