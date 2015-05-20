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
			$msgFromType= $postObj->MsgType;

			if ($msgFromType=="event"){  
				$contentStr = "欢迎来到微信MOA在线点歌系统：\r\n\r\n输入问号“?”或者“点歌”，可查看歌曲列表。\r\n\r\n输入歌曲的数字序号就可在线点歌"; 
				$msgType = "text";
				$time = time();
				$textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>0</FuncFlag>
				</xml>"; 						        
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
				echo $resultStr;                   	
			}

			if ($msgFromType=="text"){ 
				$keyword = trim($postObj->Content);
				if ($keyword=="?"||$keyword=="？"||$keyword=="点歌"){ 
					$contentStr = "歌曲列表：\r\n1、王菲-红豆\r\n2、王菲-棋子\r\n3、王菲-容易受伤的女人\r\n4、王菲-天空\r\n5、王菲-我愿意\r\n6、王菲-旋木\r\n7、王菲-执迷不悔\r\n输入以上歌曲的数字序号就可在线点歌"; 
					$msgType = "text";
					$time = time();
					$textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>"; 						        
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr;                 		

				}else{

					if (is_numeric($keyword)){
						switch ($keyword)
						{
							case "1":
							$Description  = "1、红豆"; 
							break;
							case "2":
							$Description = "2、棋子";
							break;
							case "3":
							$Description = "3、容易受伤的女人";
							break;
							case "4":
							$Description = "4、天空";
							break;
							case "5":
							$Description = "5、我愿意";
							break;
							case "6":
							$Description = "6、旋木";
							break;	
							case "7":
							$Description = "7、执迷不悔";
							break;												
							default:
							$Description  = "1、红豆";  
							$keyword  = "1";
						}
						$MusicUrl = "http://www.hngsmlk.com.cn/MP3/".$keyword.".mp3";

						$msgType="music";
						$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Music>
						<Title><![CDATA[%s]]></Title>
						<Description><![CDATA[王菲经典歌曲]]></Description>
						<MusicUrl><![CDATA[%s]]></MusicUrl>
						<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
						</Music>
						<FuncFlag>0</FuncFlag>
						</xml>";  
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $Description, $MusicUrl, $MusicUrl);
						echo $resultStr;   																	

					}else{

						$contentStr = "歌曲列表：\r\n\r\n1、王菲-红豆\r\n2、王菲-棋子\r\n3、王菲-容易受伤的女人\r\n4、王菲-天空\r\n5、王菲-我愿意\r\n6、王菲-旋木\r\n7、王菲-执迷不悔\r\n\r\n输入以上歌曲的数字序号就可在线点歌"; 
						$msgType = "text";
						$textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>"; 						        
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						echo $resultStr; 

					}




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