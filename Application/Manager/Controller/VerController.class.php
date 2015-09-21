<?php
namespace Manager\Controller;
use Think\Controller;
class VerController extends Controller {
	
	/**
	 * 获取验证码
	 */
	public function verify(){

	    $config =    array(
	        'fontSize'    =>    15,    // 验证码字体大小
	        'useNoise'    =>    false, // 关闭验证码杂点
	        'imageW'      =>    0,     // 验证码宽度
	        'imageH'      =>    0,     // 验证码高度
	        'codeSet'     =>    '123456789',//验证码字符
	        'length'      =>    4,     // 验证码位数
	    );
	    $Verify =     new \Think\Verify($config);
	    $Verify->entry();
	}

}

?>