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
                 
                  

                if ($msgType=="text"){ 
                   $keyword = $postObj->Content;
                   $CreateTime = time();
                   $FuncFlag =  1 ;
                    
                   $sql2="select * from city where cityname='".$keyword."'";
                   $resultq=mysql_query($sql2);
                   $rsNextqman=mysql_fetch_assoc($resultq);
                    
                     
                   if ($rsNextqman){
                    	$ch = curl_init("http://www.weather.com.cn/data/cityinfo/".$rsNextqman["cityid"].".html");
                    	curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
                    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
											curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																									'Content-Type: application/json')                                                                       
																 ); 
											$resultq=curl_exec($ch);
											$resultq2=json_decode($resultq, true);
											$contentStr=  $resultq2['weatherinfo']['city']."\r\n最高气温".$resultq2['weatherinfo']['temp2']."\r\n最低气温".$resultq2['weatherinfo']['temp1']."\r\n".$resultq2['weatherinfo']['weather']."\r\n最近一次预报时间为".$resultq2['weatherinfo']['ptime'];														
                   }else{
                    	$contentStr="无此城市的天气预报信息";                      	
                   }
                    
                     
                     

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