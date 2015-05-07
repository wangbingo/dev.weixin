<?php
//数据库类型
$Config["DB_TYPE"] = "mysql"; //sqlite, mysql

//Mysql数据库连接设置
$Config["DB_HOST"]='127.0.0.1'; 
$Config["DB_NAME"]='weixin';
$Config["DB_USER"]='root'; 
$Config["DB_PWD"]='mario111';

//sqlite连接设置
$Config["SQLITE_DB_NAME"] = "";

//邮件设置
$Config["Mail_Host"]='';    //The server to connect. Default is localhost 
$Config["Mail_FromAddress"]='';
$Config["Mail_Username"]='';      //The username to use for SMTP authentication. 
$Config["Mail_Password"]='';        //The password to use for SMTP authentication. 
$Config["Charset"] = "utf-8";
$Config["Mail_On"]  = true;
$Config["Mail_SMTPAuth"] = true;      //Whether or not to use SMTP authentication. Default is FALSE 
$Config["Mail_FromName"] = "seem";
$Config["Mail_SendMethod"] = "SMTP"; //"MAIL|SENDMAIL|SMTP|QMAIL";

//RPC设置
//$Config["rpc_host"] = "192.168.1.45";
//$Config["rpc_port"] = 8086;

/********************************************************************************************************
 * 下面是全局变量设置，不需要修改
*********************************************************************************************************/
//版本
$Config["Version"] = "1.0.7";

//语言
$Config["LAN"] = "CH"; // CH EN

//分页显示记录数
$Config["pagesize"] = 30;

//cookie设置
$Config["Cookie_Pre"] = 'moa'; 			//设置 Cookie 名字的前缀
$Config["Cookie_Expire"] = 10000;  	//设置 Cookie 在客户端的存活时间 单位/hour
$Config["Cookie_Path"] = '/';				//设置 Cookie 的有效路径
$Config["Cookie_Domain"] = '';			//设置 Cookie 的作用域

//用户cookie信息设置
$UserInfo["LOGIN"] = 0;
$UserInfo["UID"] = 0;
$UserInfo["UNAME"] = '';
$UserInfo["RNAME"] = '';
$UserInfo["ROLE"] = 0;

///定义上传文件参数
$FileInfo["save_path"] = "/kis/attached/";
$FileInfo["photo_path"] = "/kis/user_photo/";
$FileInfo["max_size"] = 1024*1024*10; //10MB
$FileInfo["ext_arr"] = array('gif','jpg','png','bmp', 'rar','zip','txt','doc','xls','ppt','chm','hlp','pdf','vsd');

//MVC配置队列
$Config['actions'] = array();
$Config['views'] = array();
$Config['dlgs'] = array();

//状态类型
$Config['state_type'] = array('关闭', '新建', '系统分配','自由转派', '多人处理', '完成时限考核点'); //分配
$Config['state_type_close'] = 0;
$Config['state_type_new'] 	= 1;
$Config['state_type_assign'] = 2;
$Config['state_type_handle'] = 3;
$Config['state_type_examin'] = 4;
$Config['state_type_verify'] = 5;


//字段类型
$Config['field_type'] = array('单行文本','多行文本','多行富文本','单选下拉框','单选列表框','多选下拉框','多选选择框','时间日期');
$Config['is_query']=array('否', '是');

//系统权限
$Config["sys_grade_name"] = array('事务处理', '用户管理', '流程设计', '项目配置');
$Config["sys_grade"] = array(1,2,4,8);
$Config["sys_mgr_issue"] = 1;
$Config["sys_mgr_user"] = 2;
$Config["sys_mgr_flow"] = 4;
$Config["sys_mgr_project"] = 8;

//事务权限
$Config["issue_grade_name"] = array("注释", "添加附件", "编辑", "转移", "删除", "取消");
$Config["issue_grade"] = array(1, 2, 4, 8, 16, 32);
$Config["issue_config_comment"] = 1;
$Config["issue_config_attach"] 	= 2;
$Config["issue_config_edit"] 		= 4;
$Config["issue_config_tran"] 		= 8;
$Config["issue_config_del"] 		= 16;
$Config["issue_config_close"] 	= 32;

//事务优先级
$Config["priority"] = array('一般', '高', '紧急', '911');
//事务严重级别
$Config["serverity"] = array('次要', '严重', '致命');

//激活描述
$Config["active"] = array("关闭", "活跃");
//字段编辑规则
$Config["edit_rule"] = array('可选编辑','必须编辑');
?>
