<?php
/*
    文件： ProblemController.class.php
    作用： 试题相关操作
    用法：
    版本： 2014年4月3日07:54:43
*/

namespace Admin\Controller;
use Think\Controller;
use ZipArchive;     //使用系统的解压缩类

class ProblemController extends SafeBaseController {
    /*  ====================
        公共函数部分
            index           首页
            uploadProblem   试题上传处理
            deleteProblem   试题删除处理
            countUserFiles  查看用户提交
        ====================
    */

    /* 首页 */
    public function index(){
        $this->upload();
    }
    
    public function upload(){
        $this->assign('title','试题上传');
        $problems=$this->getProblems();
        $this->assign('problems',$problems);
        $this->display('upload');
    }
    
    /* 试题上传处理 */
    public function uploadProblem(){
        /* 初始化 */
        $config = array(
            'maxSize'   =>  C('UPLOAD_PROBLEM_MAX_SIZE'),
            'rootPath'  =>  C('UPLOAD_PROBLEM_ROOT_PATH'),
            'savePath'  =>  C('UPLOAD_PROBLEM_FILE_PATH'),
            'exts'      =>  C('UPLOAD_PROBLEM_FILE_TYPE'),
            'autoSub'   =>  false,
            //'saveName'  =>  '',
            //'replace'   =>  true,
        );

        $upload = new \Think\Upload($config);   // 实例化上传类
        $zip    = new ZipArchive();             // 实例化解压缩类

        /* 上传文件  */
        $info   =   $upload->upload();
        if(!$info) {
            $this->error($upload->getError());
        }

        /* 解压缩 */
        $fileInfo=$info['file'];
        $fileInfo['fullname']=$fileInfo['savepath'].$fileInfo['savename'];  // 完整路径
        $fileInfo['fullpath']=$fileInfo['savepath'].explode(".",$fileInfo['savename'])[0];  // 完整路径
        if ($zip->open($fileInfo['fullname'])) {
            if(!$zip->getStream('info.txt')){           // 检查里面的文件
                $zip->close();
                unlink( $fileInfo['fullname'] );
                $this->error('没有信息描述文件<br />info.txt ');
            }
            if(!$zip->getStream('download.zip')){       // 检查里面的文件
                $zip->close();
                unlink( $fileInfo['fullname'] );
                $this->error('没有下载压缩包<br />download.zip ');
            }
            $zip->extractTo($fileInfo['fullpath']);     // 解压缩
            $zip->close();                              //把文件关上
        }
        unlink( $fileInfo['fullname'] );
        $this->success('试题上传成功');
    }

    /* 试题删除处理 */
    public function deleteProblem(){
        $path=C('UPLOAD_PROBLEM_FILE_PATH');
        if (I('post.dir')=='')
            $this->error('题目删除失败');
        $problemPath=$path.base64_decode(I('post.dir'));

        if ($this->deldir($problemPath)){
            $this->success('题目删除成功');
        }
        else{
            $this->error('题目删除失败，请立即手动删除此题目：'.base64_decode(I('post.dir')));
        }
    }

    /* 查看用户提交 */
    public function countUserFiles(){
        $this->assign('title','查看提交');
        $uploadFiles=$this->getFiles();
        $this->assign('files',$uploadFiles);
        $this->display();
    }


    /*  ====================
        私有函数部分
            getProblems     获取所有试题
            getFiles        获取所有提交
            deldir          删除文件夹
        ====================
    */

    /* 获取所有试题 */
    private function getProblems(){
        $path=C('UPLOAD_PROBLEM_FILE_PATH');
        if ($root=opendir($path)){
            while(($fileName=readdir($root))){
                $problemFullDir=$path."".$fileName.'/';
                if ((is_dir($problemFullDir))&&($fileName!=".")&&($fileName!="..")){
                    if($file = fopen($problemFullDir.'info.txt', "r")){
                        /* 获取下载地址 */
                        $singleProblem['dir']=$fileName.'/';
                        /* 获取开始时间 */
                        $singleProblem['time']=fgets($file);
                        /* 获取标题 */
                        $singleProblem['title']=fgets($file);
                        /* 获取简介 */
                        $singleProblem['description']='';
                        $i=1;
                        while(!feof($file) && i<=3)
                            $singleProblem['description']=$singleProblem['description'].fgets($file);
                        fclose($file);
                        $singleProblem['description']=$singleProblem['description'].' ...';

                        if ($singleProblem['title']=='')
                            $singleProblem['title']='没有标题';
                        if ($singleProblem['description']==' ...')
                            $singleProblem['description']='没有说明';
                        if ($singleProblem['time']==null)
                            $singleProblem['time']="1900-01-01 00:00:00";
                        /* 保存起来 */
                        $problemSet[]=$singleProblem;
                        fclose($file);
                    }
                }
            }
            closedir($root);
        }
        return $problemSet;
    }

    /* 获取所有提交 */
    private function getFiles(){
        $path=C('UPLOAD_FILE_PATH');
        if ($root=opendir($path)){
            while($subFloder=readdir($root)){
                $fullSubFloder=$path."".$subFloder;
                if (is_dir($fullSubFloder)&&($subFloder!=".")&&($subFloder!="..")){
                    if ($subDir=openDir($fullSubFloder)){
                        while ($filePointer=readdir($subDir)){
                            if ((($filePointer!=".")&&($filePointer!=".."))){
                                $singleFileInfo['fileUploader']=iconv(C('SERVER_CHARSET'),C('DEFAULT_CHARSET'),$subFloder);
                                $singleFileInfo['fileName']=iconv(C('SERVER_CHARSET'),C('DEFAULT_CHARSET'),$filePointer);
                                //$singleFileInfo['fileSize']=filesize($fileFullDir);
                                //$singleFileInfo['fileTime']=filemtime($fileFullDir);
                                $updatedFiles[]=$singleFileInfo;
                            }
                        }
                        closedir($filePointer);
                    }
                }
            }
            closedir($path);
        }
        return $updatedFiles;
    }

    /* 删除整个文件夹 */
    private function deldir($dir) {
        /* 先删除目录下的文件 */
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

}