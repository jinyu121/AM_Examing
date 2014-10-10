<?php
/* 
    文件： UserController.class.php
    作用： 提供与用户相关的功能（比如登录登出）
    用法： 
    版本： 2014年4月12日13:30:15
*/

namespace Admin\Controller;

use Think\Controller;

class UserController extends SafeBaseController {
    
    /*  ====================
        公共函数部分
            index           首页（无）
            login           登录页面
            logout          登出
            loginCheck      执行登录检查
            checkSign       查看签到记录
            addStudent      添加学生页面
        ====================
    */
    
    public function _initialize() {
        /* 屏蔽掉父类的构造函数 */
    }
    
    /* 首页 */
    public function index(){
        /* 直接进入，则去登录 */
        $this->checkSign();
    }
    
    /* 登录页面 */
    public function login(){
        if (session('adminLogin')==true)
            $this->error('无需重复登录');
        else{
            $this->assign('title','登录');
            $this->display('login');
        }
    }
    
    /* 登出 */
    public function logout(){
        /* 首先检查有没有登录*/
        parent::_initialize();
        
        session(null);
        $this->success('登出成功', U('Index/index'));
    }
    
    /* 执行登录检查 */
    public function loginCheck(){
        $iUser = D('User');
        $postData['name']=I('post.user_name','');
        $postData['password']=I('post.user_password','');
        $loginStatus=$iUser->loginCheck($postData);
        switch ($loginStatus['result']){
            case 'Success': $this->loginStatus0($loginStatus);break;
            case 'UsernameOrPassword': $this->loginStatus1(); break;
            default:$this->loginStatus1(); break;
        }
    }
    
    /* 查看签到记录 */
    public function checkSign(){
        /* 首先检查有没有登录*/
        parent::_initialize();
        
        $this->assign('title','查看签到');
        /* 获取已经登录名单 */
        $SignedModel=D('Home/User');
        $signed=$SignedModel->getSigned();
        $this->assign('signed',$signed);
        /* 获取还未登录名单 */
        $unsigned=$SignedModel->getUnsigned();
        $this->assign('unsigned',$unsigned);
        $this->display('checkSign');
    }
    
    /* 添加学生页面 */
    public function addStudent(){
        /* 首先检查有没有登录*/
        parent::_initialize();
        
        $this->assign('title','学生添加');
        $this->display();
    }
    
    /* 删除学生页面 */
    public function deleteStudent(){
        /* 首先检查有没有登录*/
        parent::_initialize();
        
        $pageLimit=2;
        $this->assign('title','学生删除');
        
        $User=D('Home/User');
        $list=$User->getAllStudent();
        $this->assign('students',$list);
        
        $this->display();
    }
    
    /* 分发器 */
    public function dispatcher(){
        /* 首先检查有没有登录*/
        parent::_initialize();
        
        $todo=base64_decode(I('post.todo',''));
        
        switch ($todo){
            case 'null'         :$this->error('请选择操作');break;
            case 'unBandage'    :$this->unBandage();break;
            case 'signByAdmin'  :$this->studentSignByAdmin();break;
            case 'deleteStudent':$this->studentDelete();break;
            case 'addStudentOne':$this->studentaddOne();break;
            case 'addStudentAll':$this->studentAddAll();break;
            default             :$this->error('请求错误');
        }
        
    }
    
    /*  ====================
        私有函数部分
            loginStatus0    登录成功执行逻辑
            loginStatus1    登录失败执行逻辑
            unBandage       解除绑定
            signByAdmin     管理员添加签到
            deleteStudent   删除学生
            addStudentOne   添加单个学生处理逻辑
            addStudentAll   添加所有学生处理逻辑
        ====================
    */
    
    private function loginStatus0($resultSet){
        session(null);
        session('name',$resultSet['name']);
        session('isAdmin',true);
        session('adminLogin',true);
        $this->success('登录成功', U('Index/index')); 
    }
    
    private function loginStatus1(){
        session(null);
        $this->error('登录失败：<br />用户名或密码错误');
    }
    
    /* 解除绑定 */
    private function unBandage(){
        /* 首先检查有没有登录*/
        parent::_initialize();
    
        $range=I('post.userid','0000');
        if ($range=='0000')
            $this->error('解除绑定失败<br />发生了错误');
            
        for ($i=0;$i<count($range);$i++)
            $range[$i]=intval(base64_decode($range[$i]));
            
        $signModel=M('Sign');
        
        $condition['id']=array('in',$range);
        /* 方案一：直接删除 */
        $signModel->where($condition)->delete();
        /* 方案二：修改IP数据为 0.0.0.0 */
        // $data['ip']='0.0.0.0';
        // $signModel->field('ip')->where($condition)->save($data);
        $this->success('解除绑定成功');
    }
    
    /* 管理员添加签到 */
    private function studentSignByAdmin(){
        /* 首先检查有没有登录*/
        parent::_initialize();
        
        $range=I('post.userid','0000');
        if ($range=='0000')
            $this->error('管理员添加签到失败<br />发生了错误');
            
        for ($i=0;$i<count($range);$i++){
            $data[]=array('id'=>intval(base64_decode($range[$i])),'ip'=>'0.0.0.0');
        }
        
        $signModel=M('Sign');
        $signModel->addAll($data);
        $this->success('手工签到成功');
    }
    
    
    /* 删除学生 */
    private function studentDelete(){
        /* 首先检查有没有登录*/
        parent::_initialize();
        
        $range=I('post.userid','0000');
        if ($range=='0000')
            $this->error('删除学生失败<br />发生了错误');
        
        for ($i=0;$i<count($range);$i++){
            $range[$i]=intval(base64_decode($range[$i]));
        }
        $data['id']=array('in',$range);
        
        $log=M('Sign');
        $log->where($data)->delete();
        
        $user=M('User');
        $user->where($data)->delete();
        
        $this->success('删除成功');
    }
    
    /* 添加单个学生处理逻辑 */
    private function studentAddOne(){
        /* 首先检查有没有登录*/
        parent::_initialize();
        
        $iUser = D('Home/User');
        $data['id']=trim(I('post.stu_id'));
        $data['name']=trim(I('post.stu_name'));
        if ($iUser->addOneStudent($data) != false){
            $this->success('添加学生成功');
        }else{
            $this->error('添加学生失败：<br />可能学号已经存在');
        }
    }
    
    /* 添加所有学生处理逻辑 */
    private function studentAddAll(){
        /* 首先检查有没有登录*/
        parent::_initialize();
        
        /* 初始化 */
        $config = array(
            'rootPath'  =>  './',
            'savePath'  =>  './Runtime/Temp/',
            //'exts'      =>  array('csv'),
            //'autoSub'   =>  false,
        );

        $upload = new \Think\Upload($config);   // 实例化上传类

        /* 上传文件  */
        $info   =   $upload->upload();
        if(!$info) {
            $this->error($upload->getError());
        }

        /* 获取文件信息 */
        $fileInfo=$info['file'];
        $fileInfo['fullname']=$fileInfo['savepath'].$fileInfo['savename'];  // 完整路径
        
        $iUser = D('Home/User');
        $result=$iUser->addAllStudent($fileInfo['fullname']);
        unlink($fileInfo['fullname']);
        $this->success('批量添加完成<br />成功'. $result['succ'] .'个，失败'.$result['fail'] .'个。');
        
    }
}