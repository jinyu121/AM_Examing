### 安装说明 ###
0. 安装 Apache 、 MySQL 、PHP 。
1. 修改 php.ini 文件：
	1. __必须__ zlib.output_compression = On
	2. __必须__ session.auto_start = 1
	3. __可选__ upload_max_filesize = 100M
	4. __必须__ date.timezone = PRC
2. 修改 httpd.conf 文件：
	1. __推荐__ 开启 rewrite_module 模块 
	2. __推荐__ AllowOverride All
3. 重启 Apache 。
4. 配置 Application/Common/Conf/config.php 文件：
    - 填入数据库相关信息。
    - 如果 2.1 中开启了 rewrite 模块，那么将 URL 模式 设置为 1 或 2 ，否则只能设置为 1 。 
5. 将 upload 文件夹里面的全部文件上传
6. 将 Database.sql 导入到数据库
7. 打开浏览器，进入 http://网址或IP地址/Admin ，使用默认用户名 “admin” 密码 “admin” （不含外层引号）进行上传试题、管理学生操作。


### 注意 ###
1. 要求 PHP 版本大于5.3.0 。
2. 请使用 Chrome 、 Opera 、Firefox 、 IE 10 等浏览器。
