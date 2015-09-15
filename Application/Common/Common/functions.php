<?php
/**
 * 公共函数
 */

// 格式化打印数组
function p($array){
    dump($array, 1, '<pre>', 0);
}

/**
 * 获取当前日期时间datetime，用于插入数据库
 * @return date 格式如：1991-01-01 14:08:27
 */
function getDatetime(){

    return date('Y-m-d H:i:s',time());
}

/**
 * 检测输入的验证码是否正确
 * @param string $code 户输入的验证码字符串
 */
function check_verify($code, $id = ''){

    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
* 验证手机号是否正确
* @author 范鸿飞
* @param INT $mobile
*/
function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}

/**
 * 验证简历id是否合法
 * @param  int $cv_flag 简历id
 * @return bool         true/false
 */
function validate_cv_flag($cv_flag){
	if (!is_numeric($cv_flag)) {
		// echo "非数字";
		return false;
	}

	if (1000 <= $cv_flag && $cv_flag <= 9999) {
		return true;
	}else{
		// echo "不在范围内";
		return false;
	}
}

/**
 * 格式化数据库内简历数据->简历表单数据
 * @param  mix $resume 数据库内简历数据
 * @return mix         简历表单数据
 */
function format_sqlData_to_formData($resume){

	$birth_place = explode('省', $resume['birth_place']);
	$province = $birth_place[0];
	// p($birth_place);
	$birth_place = explode('市', $birth_place[1]);
	$city = $birth_place[0];
	// p($birth_place);
	$resume['province']				= $province;
	$resume['city']					= $city;

	$address = explode('区', $resume['address']);
	$district = $address[0];
	// p($address);
	$address = explode('栋', $address[1]);
	$building = $address[0];
	// p($address);
	$address = explode('房', $address[1]);
	$room = $address[0];
	// p($address);
	$resume['district']				= $district;
	$resume['building']				= $building;
	$resume['room']					= $room;
	
	$resume['free_time']			= explode(',', $resume['free_time']);

	return $resume;
}

?>