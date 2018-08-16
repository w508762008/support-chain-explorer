<?php
return array(
	//'配置项'=>'配置值'

url_html_suffix=>'',	
// URL地址不区分大小写
'URL_CASE_INSENSITIVE' => true,
//REWRITE模式
'URL_MODEL' => 2,
'DEFAULT_MODULE' => 'Home',
'MODULE_ALLOW_LIST' => array('Home'),
'MONGO'=> array(
'DB_TYPE'   => 'mongo', // 数据库类型
'DB_HOST'   => 'localhost', // 服务器地址
'DB_NAME'   => 'XMAX2', // 数据库名
'DB_USER'   => '', // 用户名
'DB_PWD'    => '', // 密码
'DB_PORT'   => 27017, // 端口
'DB_PREFIX' => '', // 数据库表前缀 
'DB_CHARSET'=> 'utf8', // 字符集
'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
),
'TMPL_L_DELIM'     => '<{',  // 模板引擎普通标签开始标记
'TMPL_R_DELIM'     => '}>',  // 模板引擎普通标签结束标记
);