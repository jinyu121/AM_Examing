<?php
/* 
    文件： ConfigController.class.php
    作用： 设置
    用法： 
    版本： 2014年4月18日15:06:50
*/

namespace Admin\Controller;

use Think\Controller;

class ConfigController extends SafeBaseController {
    public function admins(){
        $this->assign('title','管理员管理');
        $Admin=D('Admin');
        $admins=$Admin->getAllAdmin();
        $this->assign('admins',$admins);
        $this->display();
    }
    
    public function dashboard(){
        $this->assign('title','控制板');
        $this->display();
    }
    
    public function dispatcher(){
        $todo=base64_decode(I('post.todo',''));
        switch ($todo){
            case 'addAdmin'     :$this->adminAdd();break;
            case 'deleteAdmin'  :$this->adminDelete();break;
            default             :$this->error('请求错误');
        }
        
    }
    
    private function adminAdd(){
        if (trim(I('post.name',''))=='' || I('post.password')=='')
            $this->error('错误：管理员用户名或密码为空');
        $Admin=D('Admin');
        $Admin->create();
        $Admin->add();
        $this->success('添加成功',U('admins'));
    }
    
    private function adminDelete(){
        $Admin=D('Admin');
        $data['name']=base64_decode(I('post.name',''));
        $data['password']=I('post.password','');
        if ($data['name'==''] || $data['password']=='')
            $this->error('用户名或密码错误');
        $dbresult=$Admin->limit(1)->find($data['name']);
        if ($dbresult['password']==$data['password'])
            $Admin->limit(1)->delete($data['name']);
        $this->success('删除成功');
    }
}