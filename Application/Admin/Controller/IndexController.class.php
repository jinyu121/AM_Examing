<?php
/* 
    文件： indexController.class.php
    作用： 首页
    用法： 
    版本： 2014年4月12日13:29:53
*/

namespace Admin\Controller;

use Think\Controller;

class IndexController extends SafeBaseController {

    public function index(){
        $this->assign('title','欢迎');
        $this->assign('baseClass','index');
        $this->assign('name',session('name'));
        $this->assign('ipAddress',get_client_ip());
        $this->show();
    }
    
}