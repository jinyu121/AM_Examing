<?php
/* 
    文件： UserController.class.php
    作用： 提供与用户相关的功能（比如登录登出）
    用法： 
    版本： 2014年4月3日07:43:08
*/

namespace Home\Controller;

use Think\Controller;

class UserController extends Controller {

    public function _initialize() {
        $this->assign('title','登录');
    }

    public function index(){
        /* 直接进入，则去登录 */
       $this->display('login');
    }
    
    public function login(){
        /* 显示一个登录界面 */
        $this->display();
    }
    
    public function loginCheck(){
        $iUser = D('User');
        $postData['name']=I('post.user_name','');
        $postData['id']=I('post.user_id','');
        $loginStatus=$iUser->loginCheck($postData);
        switch ($loginStatus['result']){
            case 'Success': $this->loginStatus0($loginStatus);break;
            case 'UsernameOrPassword': $this->loginStatus1(); break;
            case 'MultiLogin': $this->loginStatus2(); break;
            case 'AnotherLogin': $this->loginStatus3(); break;
            default:$this->loginStatusDefault(); break;
        }
    }
    
    public function logout(){
        session(null);
        $this->success('登出成功', U('Home/User/index'));
    }
    
    private function loginStatus0($resultSet){
        session(null);
        session('id',$resultSet['id']);
        session('name',$resultSet['name']);
        session('isAdmin',false);
        session('userLogin',true);
        $this->success('登录成功', '/'); 
    }
    private function loginStatus1(){
        session(null);
        $this->error('登录失败：<br />用户名或密码错误');
    }
    private function loginStatus2(){
        session(null);
        $this->error('登录失败：<br />已经在其他机器上登录');
    }
    private function loginStatus3(){
        session(null);
        $this->error('登录失败：<br />请换一台机器尝试登录');
    }
    private function loginStatusDefault(){
        session(null);
        $this->error('登录失败：<br />未知错误');
    }
    
}