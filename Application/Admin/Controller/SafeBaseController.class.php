<?php
/* 
    文件： SafeBaseController.class.php
    作用： 提供构造方法，检查用户是否已经登录
    用法： 其他类继承该类即可
    版本： 2014年4月12日13:29:48
*/

namespace Admin\Controller;
use Think\Controller;

class SafeBaseController extends Controller {

    public function _initialize() {
        if (!session('?adminLogin'))
            $this->error('未登录或登录超时', U('User/login'),1);
        if (session('adminLogin')==false)
            $this->error('未登录或登录超时', U('User/login'),1);
    }
    
}
