<?php
/* 
    文件： indexController.class.php
    作用： 首页
    用法： 
    版本： 2014年4月1日18:42:12
*/

namespace Home\Controller;

use Think\Controller;

class IndexController extends SafeBaseController {

    public function index(){
        $this->assign('title','欢迎');
        $this->assign('baseClass','index');
        $this->assign('name',session('name'));
        $this->assign('id',session('id'));
        $this->assign('ipAddress',get_client_ip());
        $this->show();
    }
    
}