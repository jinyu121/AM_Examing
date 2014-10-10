<?php
/* 
    文件： ProblemController.class.php
    作用： 试题下载和答案上传
    用法： 
    版本： 2014年4月3日07:54:43
*/

namespace Home\Controller;
use Think\Controller;

class ProblemController extends SafeBaseController {

    public function upload(){
        $this->assign('title','答案上传');
        $files=$this->getAllFiles();
        $this->assign('files',$files);
        $this->display();
    }
    
    public function download(){
        $this->assign('title','试题下载');
        $problems=$this->getProblems();
        $this->assign('problems',$problems);
        $this->display();
    }
    
    public function uploadFile(){
        $config = array(
            'maxSize'   =>  C('UPLOAD_MAX_SIZE'),
            'rootPath'  =>  C('UPLOAD_ROOT_PATH'),
            'savePath'  =>  C('UPLOAD_FILE_PATH'),
            'saveName'  =>  array('iconv',array(C('DEFAULT_CHARSET'),C('SERVER_CHARSET'),'__FILE__')),
            'exts'      =>  C('UPLOAD_FILE_TYPE'),
            'autoSub'   =>  true,
            'replace'   =>  true,
            'subName'   =>  iconv(C('DEFAULT_CHARSET'),C('SERVER_CHARSET'),session('id').'_'.session('name')),
        );
        $upload = new \Think\Upload($config);// 实例化上传类
        /* 上传文件  */ 
        $info   =   $upload->upload();
        /* 上传错误提示错误信息 */
        if(!$info) {
            $this->error($upload->getError());
        }else{
            $this->success('上传成功！');
        }
    }
    
    public function deleteFile(){
        $path=C('UPLOAD_FILE_PATH').''.session('id').'_'.session('name').'/';
        $filename=I('post.fileName','');
        if ($filename==''){
            $this->error('参数错误');
        }
		$filename=iconv(C('DEFAULT_CHARSET'),C('SERVER_CHARSET'),$path."".base64_decode($filename));
		
        if (unlink( $filename )){
            $this->success('文件删除成功'); 
        }
        else{
            $this->error('文件删除失败'); 
        }
    }
    
    private function getAllFiles(){
        $path=C('UPLOAD_FILE_PATH').''.iconv(C('DEFAULT_CHARSET'),C('SERVER_CHARSET'),session('id').'_'.session('name')).'/';
        if (is_dir($path)){
            if ($dh=opendir($path)){
                while(($fileName=readdir($dh))){
                    $fileFullDir=$path."".$fileName;
                    if ((!is_dir($fileFullDir))&&($fileName!=".")&&($fileName!="..")){
                        $singleFileInfo['fileName']=iconv(C('SERVER_CHARSET'),C('DEFAULT_CHARSET'),$fileName);
                        $singleFileInfo['fileSize']=filesize($fileFullDir);
                        $singleFileInfo['fileTime']=filemtime($fileFullDir);
                        $fileInfo[]=$singleFileInfo;
                    }   
                }
                closedir($dh);
            }
        }
        return $fileInfo;
    }
    
    private function getProblems(){
        $path='./Problems/';
        if ($root=opendir($path)){
            while(($fileName=readdir($root))){
                $fileFullDir=$path."".$fileName.'/';
                if ((is_dir($fileFullDir))&&($fileName!=".")&&($fileName!="..")){
                    if($file = fopen($fileFullDir.'info.txt', "r")){
                        /* 获取下载地址 */
                        $singleProblem['downloadDir']=$fileFullDir;
                        /* 获取开始时间 */
                        $singleProblem['time']=trim(fgets($file));
                        /* 获取标题 */
                        $singleProblem['title']=trim(fgets($file));
                        /* 获取简介 */
                        $singleProblem['description']='';
                        while(!feof($file))
                            $singleProblem['description']=$singleProblem['description'].'<p>'.trim(fgets($file)).'</p>';
                        fclose($file);
                        if ($singleProblem['time']==null || $singleProblem['time']=='')
                            $singleProblem['time']="1900-01-01 00:00:00";
                        if ($singleProblem['title']=='')
                            $singleProblem['title']='没有标题';
                        if ($singleProblem['description']=='')
                            $singleProblem['description']='没有说明';
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
    
    public function downloadProblem(){
        /* 获取地址并解码 */
        $fileDir=I('post.dir');
        $fileDir=base64_decode($fileDir).'download.zip';
        /* 文件输出 */
        $file = fopen ( $fileDir, "r" );  
        /* 输出文件标签 */
        Header ( "Content-type: application/octet-stream" );  
        Header ( "Accept-Ranges: bytes" );  
        // Header ( "Accept-Length: " . filesize ( $fileDir) );  
        Header ( "Content-Disposition: attachment; filename = Problem.zip" );  
        /* 输出文件内容 */
        fpassthru($file);
        fclose ( $file );  
        return;
    }
	
}