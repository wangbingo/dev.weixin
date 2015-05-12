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
                    	$contentStr = "欢迎来到微信MOA系统。\r\n\r\n发送当前位置到系统，然后输入银行、超市、餐馆就可查询周边信息点。\r\n\r\n发送城市名称可查询当地的最新天气预报";                 	                	
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
                 
														 $mylongitude= $postObj->Location_Y;
														 $mylatitude= $postObj->Location_X;	
														 $ch = curl_init("http://api.map.baidu.com/ag/coord/convert?from=2&to=4&x=".$mylongitude."&y=".$mylatitude);                                                                      
														 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
														 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
														 curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																				      'Content-Type: application/json')                                                                       
														 );                                                                                                                   
														 $resultjw = curl_exec($ch);
														 $resultjw2 = json_decode($resultjw, true);  
														 $ch = curl_init("http://api.map.baidu.com/geocoder/v2/?ak=E277b7910be1c22f86c4beec256173b6&location=".base64_decode($resultjw2['y']).",".base64_decode($resultjw2['x'])."&output=json&pois=0");                                                                      
														 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
														 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
														 curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																							'Content-Type: application/json')                                                                       
														 );                                                                                                                   
														$result = curl_exec($ch);
														$result2 = json_decode($result, true);  


                 
												$sql="select * from `users` where  loginname='".$fromUsername."'";
												$result = mysql_query($sql);	      
												$rsNextman=mysql_fetch_assoc($result);
								        if ($rsNextman){
														$sqlinsertLists="update users set longitude='".$postObj->Location_Y."',latitude='".$postObj->Location_X."' where loginname='".$fromUsername."'";
														mysql_query($sqlinsertLists);			
																												
												
				                					$contentStr = "我们已经收到您上报地理位置：".$result2['result']['formatted_address']."\r\n所在省份是".$result2['result']['addressComponent']['province']."\r\n经度是".$postObj->Location_Y."；\r\n纬度是".$postObj->Location_X."。\r\n您再输入银行、超市、餐馆就可查询周边信息点。";   									      																					
				                					//$contentStr = "http://api.map.baidu.com/geocoder/v2/?ak=E277b7910be1c22f86c4beec256173b6&location=".base64_decode($resultjw2['y']).",".base64_decode($resultjw2['x'])."&output=json&pois=0";
				                					
								        }else{
														$sqlinsertLists="insert into users(loginname,longitude,latitude,create_time) values('".$fromUsername."','".$postObj->Location_Y."','".$postObj->Location_X."',now())";
														mysql_query($sqlinsertLists);		
				                		$contentStr = "我们已经收到您上报地理位置：".$result2['result']['formatted_address']."\r\n所在省份是".$result2['result']['addressComponent']['province']."\r\n经度是".$postObj->Location_Y."，纬度是".$postObj->Location_X."。您是第一次使用本系统，您再输入银行、超市、餐馆就可查询周边信息点，谢谢。"; 											       
				                		//$contentStr = "http://api.map.baidu.com/geocoder/v2/?ak=E277b7910be1c22f86c4beec256173b6&location=".base64_decode($resultjw2['y']).",".base64_decode($resultjw2['x'])."&output=json&pois=0";				                		
																				         	
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


									 if ($keyword=="天气"){

											$sql="select * from `users` where  loginname='".$fromUsername."'";
											$result = mysql_query($sql);	      
											$rsNextman=mysql_fetch_assoc($result);
											if ($rsNextman){
													if ($rsNextman["longitude"]=="0"){
									           $contentStr = "请您先将当前所在位置发送给我们，然后再查找该地区的".$keyword."，谢谢。"; 																	         		
													}else{
														 $mylongitude= $rsNextman["longitude"];
														 $mylatitude= $rsNextman["latitude"];	
														  	
														 $ch = curl_init("http://api.map.baidu.com/ag/coord/convert?from=2&to=4&x=".$mylongitude."&y=".$mylatitude);                                                                      
														 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
														 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
														 curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																				      'Content-Type: application/json')                                                                       
														 );                                                                                                                   
														 $resultjw = curl_exec($ch);
														 $resultjw2 = json_decode($resultjw, true);  
														 $ch = curl_init("http://api.map.baidu.com/geocoder/v2/?ak=E277b7910be1c22f86c4beec256173b6&location=".base64_decode($resultjw2['y']).",".base64_decode($resultjw2['x'])."&output=json&pois=0");                                                                      
														 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
														 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
														 curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																							'Content-Type: application/json')                                                                       
														 );                                                                                                                   
														$result = curl_exec($ch);
														$result2 = json_decode($result, true);  
														
														$sql2="select * from `city` where  cityname='".$result2['result']['addressComponent']['city']."'";
														$resultq = mysql_query($sql2);	      
														$rsNextqman=mysql_fetch_assoc($resultq);
														if ($rsNextqman){
																 $ch = curl_init("http://www.weather.com.cn/data/cityinfo/".$rsNextqman["cityid"].".html");                                                                      
																 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
																 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
																 curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																									'Content-Type: application/json')                                                                       
																 );                                                                                                                   
																$resultq = curl_exec($ch);
																$resultq2 = json_decode($resultq, true);
																$contentStr=  $resultq2['weatherinfo']['city']."\r\n最高气温".$resultq2['weatherinfo']['temp2']."\r\n最低气温".$resultq2['weatherinfo']['temp1']."\r\n".$resultq2['weatherinfo']['weather']."\r\n最近一次预报时间为".$resultq2['weatherinfo']['ptime'];														
														}else{
															$contentStr="无此城市的天气预报信息"; 
														}												     	  	
												  }
										  }else{
            							$contentStr = "请您先将当前所在位置发送给我们，然后再查找该地区的".$keyword."，谢谢。"; 																	         
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
									  

									 if (mb_substr($keyword,-1,1,'utf-8')=="市"){

														
														$sql2="select * from `city` where  cityname = '".$keyword."'";
														$resultq = mysql_query($sql2);	      
														$rsNextqman=mysql_fetch_assoc($resultq);
														if ($rsNextqman){
																 $ch = curl_init("http://www.weather.com.cn/data/cityinfo/".$rsNextqman["cityid"].".html");                                                                      
																 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
																 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
																 curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																									'Content-Type: application/json')                                                                       
																 );                                                                                                                   
																$resultq = curl_exec($ch);
																$resultq2 = json_decode($resultq, true);
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

									  

									 if ($keyword=="银行"||$keyword=="餐馆"||$keyword=="餐厅"||$keyword=="饭店"||$keyword=="超市"||$keyword=="酒店"||$keyword=="加油站"||$keyword=="加油"){

											$sql="select * from `users` where  loginname='".$fromUsername."'";
											$result = mysql_query($sql);	      
											$rsNextman=mysql_fetch_assoc($result);
											if ($rsNextman){
													if ($rsNextman["longitude"]=="0"){
									           $contentStr = "请您先将当前所在位置发送给我们，然后再查找附近地区的".$keyword."，谢谢。"; 																	         		
													}else{
														 $mylongitude= $rsNextman["longitude"];
														 $mylatitude= $rsNextman["latitude"];	
														 $ch = curl_init("http://api.map.baidu.com/ag/coord/convert?from=2&to=4&x=".$mylongitude."&y=".$mylatitude);                                                                      
														 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
														 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
														 curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																				      'Content-Type: application/json')                                                                       
														 );                                                                                                                   
														 $resultjw = curl_exec($ch);
														 $resultjw2 = json_decode($resultjw, true);  
														 $ch = curl_init("http://api.map.baidu.com/place/v2/search?&query=".urlencode($keyword)."&page_size=20&location=".base64_decode($resultjw2['y']).",".base64_decode($resultjw2['x'])."&radius=1000&output=json&ak=E277b7910be1c22f86c4beec256173b6");                                                                      
														 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                     
														 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
														 curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
																							'Content-Type: application/json')                                                                       
														 );                                                                                                                   
														$result = curl_exec($ch);
														$result2 = json_decode($result, true);  
														if ($result2['status']!="0"){
															$echostrtmpt=""; 
														}
														else{
															$echostrtmpt="";
															foreach ($result2['results'] as $tweet) {
																$echostrtmpt= $echostrtmpt. "\r\n\r\n".$tweet['name']."  ".$tweet['address']."  ".$tweet['telephone'];
															}
													  }
														$contentStr =	"附近的".$keyword."有：".	$echostrtmpt;													     	  	
												  }
										  }else{
            							$contentStr = "请您先将当前所在位置发送给我们，然后再查找附近地区的".$keyword."，谢谢。"; 																	         
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


                   if ($keyword=="?"||$keyword=="？"||$keyword=="帮助"){ 
                    	$contentStr = "欢迎来到微信MOA系统。\r\n\r\n发送当前位置到系统，然后输入银行、超市、餐馆就可查询周边信息点。\r\n\r\n发送城市名称可查询当地的最新天气预报";                 	
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