<?php
/**
 * Recruit公共函数
 */

/**
 * 预处理简历数据
 * @param mix &$temp 简历数据
 */
function pre_process_resume_data(&$temp){

	// 预处理
	$temp['province'] 			= str_replace('省', '', $temp['province']);
	$temp['city']			 	= str_replace('市', '', $temp['city']);
	$temp['race']				= str_replace('族', '', $temp['race']);
	$temp['college']			= str_replace('学院', '', $temp['college']);
	$temp['major']				= str_replace('专业', '', $temp['major']);
	$temp['building']			= str_replace('栋', '', $temp['building']);
	$temp['room']				= str_replace('房', '', $temp['room']);
}

/**
 * session里的简历标识数据->添加/覆盖->简历cookie数据
 * @param mix &$temp 简历cookie数据
 */
function process_sessionInfo_to_Data(&$temp){

	$info = session("RESUME_INFO");
	// 使用session里面的标识数据
	$temp['stu_id'] 		= $info['stu_id'];
	$temp['phone']			= $info['phone'];
	$temp['cv_flag'] 		= $info['cv_flag'];
}


function upload_photo($filename){
	
/*======================================上传照片 begin==================================*/
    // p(I('post.'));
    // p($_FILES);die;
    // return $_FILES;

    if($_FILES['photo']['error'] == 4){

    	$data['errcode'] = 4;
    	$data['errmsg'] = '上传的文件不合法';
    }else{
        $config = array(//图片上传配置
            'maxSize'    =>    3145728,    
            'rootPath'   =>    './Application/Recruit/Source/images',
            'savePath'   =>    '/photo/',    
            'saveName'   =>    $filename,
            'exts'       =>    array('jpg', 'png', 'jpeg'),    
            'autoSub'    =>    false,   //子目录，关闭    
            // 'subName'    =>    array('date','Ymd'),
            'replace'    =>    true,    //允许同名文件覆盖
        );
        $upload = new \Think\Upload($config);// 实例化上传类  
        $_rst   =   $upload->uploadOne($_FILES['photo']);

        if(!$_rst) {// 上传错误提示错误信息
        	$data['errcode'] = 4;
        	$data['errmsg'] = $upload->getError()."<br/>(￣ω￣)看来真相出问题了";
            // $this->error($upload->getError()."<br/>(￣ω￣)看来真相出问题了");
            // return;
        }else{// 上传成功
            // $photo_url = DOMAIN_URL."/hmtNMG/Application/Recruit/Source/images/".$_rst['savename'];
            // echo $photo_url;
            // die;
            
            $data['errcode'] = 0;
            $data['errmsg'] = "ok";
		    $data['photo_name'] = $_rst['savename'];
        }
    }
/*======================================上传照片 end==================================*/

	return $data;
}


?>