<?php

namespace Admin\Model;
use Think\Model;

class AdminModel extends Model {
    public function loginCheck($postData){
    
        /* 确定查询条件 */
        $condition['name'] = $postData['name'];
        $condition['password'] = $postData['password'];
        
        /* 查询 */
        $step1=$this->where($condition)->find();

        /* 用户名或密码错误 */
        if (null==$step1 || false==$step1){
            $step1['status']='UsernameOrPassword';
            return $result;
        }
        
        /* 正常退出，登录成功 */
        $resultSet['result']='Success';
        $resultSet['name']=$postData['name'];
        return $resultSet;
    }
    
    public function getAllAdmin(){
        return $this->field('name')->select();
    }
    
}