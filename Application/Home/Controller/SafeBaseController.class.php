<?php
/* 
    文件： SafeBaseController.class.php
    作用： 提供构造方法，检查用户是否已经登录
    用法： 其他类继承该类即可
    版本： 2014年4月1日18:06:12
*/

namespace Home\Controller;

use Think\Controller;

class SafeBaseController extends Controller {

    public function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        if (!session('?userLogin'))
            $this->error('未登录。正在转向登录页面', '/Home/User/', 2);
        if (session('userLogin')==false)
            $this->error('未登录。正在转向登录页面', '/Home/User/', 2);
    }

}
