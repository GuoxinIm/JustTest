<?php
/**
 * 构建上传文件信息
 * @return unknown
 */
function getFiles(){
    $i=0;
    foreach($_FILES as $file){
        if(is_string($file['name'])){
            $files[$i] = $file;
            $i++;
        }else if(is_array($file['name'])){
            foreach($file['name'] as $key=>$val){
                $files[$i]['name'] = $file['name'][$key];
                $files[$i]['type'] = $file['type'][$key];
                $files[$i]['tmp_name'] = $file['tmp_name'][$key];
                $files[$i]['error'] = $file['error'][$key];
                $files[$i]['size'] = $file['size'][$key];
                $i++;
            }
        }
    }
    return $files;
}
function uploadFile($fileInfo,$path='./uploads',$flag=true,$maxSize=1048576,$allowExt=array('jpeg','jpg','png','gif')){
//     $flag = true;
//     $allowExt = array('jpeg','jpg','gif','png');
    //判断错误号
    if($fileInfo['error']==UPLOAD_ERR_OK){
        //检测上传文件大小
        if($fileInfo['size']>$maxSize){
            $res['mes'] =$fileInfo['name'].'上传文件过大';
        }
        $ext = getExt($fileInfo['name']);
        //检测上传文件类型
        if(!in_array($ext, $allowExt)){
            $res['mes']=$fileInfo['name'].'非法文件类型';
        }
        //检测是否为真实的图片类型
        if($flag){
            if(!getimagesize($fileInfo['tmp_name'])){
                $res['mes'] = $fileInfo['name'].'不是真实的图片类型';
            }
        }
        //检测文件是否是通过HTTP POST方式上传来的
        if(!is_uploaded_file($fileInfo['tmp_name'])){
            $res['mes'] = $fileInfo['name'].'文件不是通过HTTP POST方式上传来的';
        }
        if($res) return $res;
//         $path = './uploads';
        //如果不存在目录 创建
        if(!file_exists($path)){
            mkdir($path,0777,true);
            chmod($path,0777);
        }
        $uniName = getUniname();
        $destination = $path.'/'.$uniName.'.'.$ext;
        if(!move_uploaded_file($fileInfo['tmp_name'], $destination)){
            $res['mes'] = $fileInfo['name'].'文件移动失败';
        }
        $res['dest'] = $destination;
        return $res;
    }else{
        //匹配错误信息
        switch ($fileInfo ['error']) {
            case 1 :
                $res['mes'] = '上传文件超过了PHP配置文件中upload_max_filesize选项的值';
                break;
            case 2 :
                $res['mes'] = '超过了表单MAX_FILE_SIZE限制的大小';
                break;
            case 3 :
                $res['mes'] = '文件部分被上传';
                break;
            case 4 :
                $res['mes'] = '没有选择上传文件';
                break;
            case 6 :
                $res['mes'] = '没有找到临时目录';
                break;
            case 7 :
            case 8 :
                $res['mes'] = '系统错误';
                break;
        }
    }
}





















