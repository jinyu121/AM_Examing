<?php

namespace Home\Model;
use Think\Model;

class UserModel extends Model {

    public function loginCheck($postData){
        $SignTable=M('Sign');
        
        /* 确定查询条件 */
        $conditionStep1['id'] = $postData['id'];
        $conditionStep1['name'] = $postData['name'];
        $conditionStep2['id']=$postData['id'];
        $conditionStep3['ip']=get_client_ip();
        
        /* 查询 */
        $step1=$this->where($conditionStep1)->find();
        
        /* 用户名或密码错误 */
        if (null==$step1 || false==$step1){
            $resultSet['result']='UsernameOrPassword';
            return $resultSet;
        }
        
        /* 检查是否已经登录 */
        
        /* 检查是否在别的地方登录 */
        $step2=$SignTable->field('id,ip')->where($conditionStep2)->find();
        if (isset($step2['ip'])){
            if ($step2['ip']!=get_client_ip() && $step2['ip']!='0.0.0.0'){
                $resultSet['result']='MultiLogin';
                return $resultSet;
            }
        }
        
        /* 检查这台机器上是否登录过别人 */
        $step3=$SignTable->field('id,ip')->where($conditionStep3)->find();
        if (isset($step3['id'])){
            if ($step3[id]!=$postData['id']){
                $resultSet['result']='AnotherLogin';
                return $resultSet;
            }
        }
        
        $data['ip']=get_client_ip();
        $data['id']=$postData['id'];
        /* 记录下信息 */
        if (isset($step2['ip'])){
            if ($step2['ip']=='0.0.0.0'){
                $SignTable->where($conditionStep2)->save($data);
            }
        }else{
            $SignTable->data($data)->add();
        }
        
        /* debug:
        *   dump($step1);
        *   dump($step2);
        *   dump($step3);
        *   die();
        */
        
        /* 正常退出，登录成功 */
        $resultSet['result']='Success';
        $resultSet['id']=$postData['id'];
        $resultSet['name']=$postData['name'];
        return $resultSet;
    }
    
    public function getAllStudent(){
        return $this->field('id,name')->select();
    }
    
    public function getSigned(){
        $table='__USER__ user, __SIGN__ sign';
        $field=array('user.id','user.name','sign.ip','sign.time');
        $where='user.id=sign.id';
        return $this->table($table)->field($field)->where($where)->order('id')->select();
    }
    
    public function getUnsigned(){
        $UserTable = M('User');
        $SignTable = M('Sign');
        $subQuery=$SignTable->field(array('id'=>'signedID'))->select(false);
        return $UserTable->where('id not in '.$subQuery)->order('id')->select();
    }
    
    public function addOneStudent($data){
		$data['id']=trim($data['id']);
		$data['name']=trim($data['name']);
        if ($data['id']=='' || $data['name']=='')
            return false;
		$data['id']=0+$data['id'];
        return $this->data($data)->add();
    }
    
    public function addAllStudent($fileName){
        $suc=0;
        $fai=0;
        if($file = fopen($fileName, "r")){
            while(!feof($file)){
                $temp=trim(fgets($file));
                $oneRecord=explode(',',$temp);
                $oneData['id']=$oneRecord[0];
                $oneData['name']=$oneRecord[1];
                if ($this->add($oneData)!=false){
                    $suc+=1;
                }else{
                    $fai+=1;
                };
            }
            fclose($file);
        }
        
        return array('succ'=>$suc,'fail'=>$fai);
    }
    
}