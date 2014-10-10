<?php
return array(
	/* 数据库信息 */
    'DB_TYPE'               =>  'mysql',        // 数据库类型
    'DB_HOST'               =>  'localhost',    // 数据库服务器地址
    'DB_NAME'               =>  'am',        	// 数据库名
    'DB_USER'               =>  'root',        	// 用户名
    'DB_PWD'                =>  '',        		// 密码
    'DB_PORT'               =>  '3306',         // 端口
    'DB_PREFIX'             =>  'am_',          // 数据库表前缀
    //'DB_DSN'              =>  '',             // 数据库连接DSN 用于PDO方式
    'DB_CHARSET'            =>  'utf8',         // 数据库的编码 默认为utf8

    /* 系统其他设置 */
    'URL_CASE_INSENSITIVE'  =>  true,           // URL不区分大小写
    'URL_MODEL'             =>  2,              // URL模式（0:普通 1:PATHINFO 2:REWRITE 3:兼容）
    'URL_HTML_SUFFIX'       =>  'hy',         	// 伪静态后缀
	'SERVER_CHARSET'       	=>  'gb2312', 		// 服务器编码

    /* 上传文件设置 */
    /* 普通用户 */
    'UPLOAD_MAX_SIZE'       =>  3141592,        //上传文件大小限制
    'UPLOAD_FILE_TYPE'      =>  array(
        'rar'	,	'zip'	,	'tar',
		'tar.gz', 	'txt'	,	'7z',
		'java'	, 	'cpp'	,	'c',
		'wim'	,	'doc'	,	'docx',
		'ppt'	,	'pptx'	, 	'xls',
		'xlsx'	,	'm'
        ),                                      //上传文件类型
    'UPLOAD_ROOT_PATH'      =>  './',           //上传文件根目录
    'UPLOAD_FILE_PATH'      =>  './Uploads/',   //上传文件保存位置
    /* 管理员 */
    'UPLOAD_PROBLEM_MAX_SIZE'       =>  2147483647,     //上传文件大小限制
    'UPLOAD_PROBLEM_FILE_TYPE'      =>  array('zip'),   //上传文件类型
    'UPLOAD_PROBLEM_ROOT_PATH'      =>  './',           //上传文件根目录
    'UPLOAD_PROBLEM_FILE_PATH'      =>  './Problems/',  //上传文件保存位置

);