<?php
namespace Recruit\Controller;
use Think\Controller;
class ResumeController extends BaseController {
/**
 * 简历控制器，继承BaseController
 */

    public function index(){
        echo "Resume控制器";
    }

    /**
     * 登录编辑简历
     */
    public function login(){

    	// session("RESUME_INFO",null);
    	// session("RESUME_LOGIN_FLAG",null);
    	// cookie("RESUME_INFO",null);

    	if (IS_POST) {
    		// p(I('post.'));die;

    		$info['stu_id'] = I('post.stu_id');
    		$info['phone'] = I('post.phone');
    		$info['cv_flag'] = I('post.cv_flag');
    		// $info['stu_id'] = "201131100219";
    		// $info['phone'] = "18826481053";
    		// $info['cv_flag'] = "1234";

    		$log_model = M('resume_login_log');

			$log['stu_id'] = $info['stu_id'];// 可空
			$log['phone'] = $info['phone'];// 可空
			$log['cv_flag'] = $info['cv_flag'];// 可空
			$log['ip'] = get_client_ip();
			$log['cTime'] = getDatetime();

    		// 验证码匹配
    		// 学号不空
    		// 手机号不空，合法
    		// 简历id，4位数字
    		if (!check_verify(I('post.verify'))) {
    		    
    		    $log['status'] = 0;
    		    $log['status_msg'] = "验证码错误";
    		    goto show_rst;
    		}
    		if ($info['stu_id'] == "") {
    			
    			$log['status'] = 0;
    		    $log['status_msg'] = "学号为空";
    		    goto show_rst;
    		}
    		if ($info['phone'] == "") {
    			
    			$log['status'] = 0;
    		    $log['status_msg'] = "手机号为空";
    		    goto show_rst;
    		}else{
    			if (!isMobile($info['phone'])) {
					$log['status'] = 0;
				    $log['status_msg'] = "手机号不合法";
				    goto show_rst;
    			}
    		}
    		if ($info['cv_flag'] == "") {
    			
    			$log['status'] = 0;
    		    $log['status_msg'] = "简历id为空";
    		    goto show_rst;
    		}else{
    			if (!validate_cv_flag($info['cv_flag'])) {
					$log['status'] = 0;
				    $log['status_msg'] = "简历id不合法<br/>应为1000~9999的数字";
				    goto show_rst;
    			}
    		}

    		$map = $info;
			unset($map['cv_flag']);

    		$resume_model = M('resume');
			if ($resume_model->where($map)->find()) {

				if ($resume = $resume_model->where($info)->find()) {
					
					$log['status'] = 1;
					$log['status_msg'] = "老用户登录成功";
				}else{

					$log['status'] = 0;
					$log['status_msg'] = "简历id错误";
				}
			}else{

				// 新用户登录
				$log['status'] = 1;
				$log['status_msg'] = "新用户登录成功";
			}

			show_rst:
			$log_model->add($log);// 写日志

			// p($log);die;
    		switch ($log['status']) {
    			case 0:
    				// cookie("RESUME_INFO",null);
    				// cookie("RESUME_LOGIN_FLAG",null);
    				
    				session("RESUME_INFO",null);
    				session("RESUME_LOGIN_FLAG",null);

    				$this->error($log['status_msg']);
    				break;
    			case 1:
    				
    				session("RESUME_INFO", $info);

    				if (isset($resume)) {
    					// 将老用户简历信息转换成表单数据，并写cookie

    					$temp = format_sqlData_to_formData($resume);// 格式化数据库内简历数据->简历表单数据

    					process_sessionInfo_to_Data($temp);// session里的简历标识数据->添加->简历cookie数据
    					// p($temp);die;

						cookie($temp['stu_id']."_RESUME_INFO", json_encode($temp, JSON_UNESCAPED_UNICODE));
    				}

    				// p($log);die;

    				$this->success("登录成功", U('Recruit/Resume/edit'));
    				break;
    			default:
    				break;
    		}

    	}else {

    		$this->display();
    	}
    }

    public function edit(){
    	// $this->show("1234");

    	if (IS_POST) {

    		// p(I('post.'));die;

    		$temp = I('post.');
    		unset($temp['verify']);// 去除验证码字段

    		// 验证数据
    		if (!check_verify(I('post.verify'))) {
    		    
    		    $this->error("验证码错误");
    		    return;
    		}
    		if ($temp['name'] == "") {
    		    
    		    $this->error("无名氏你好...(￣ω￣)");
    		    return;
    		}
    		if ($temp['gender'] == "") {
    		    
    		    $this->error("性别...无, 这让我很为难啊(￣ω￣)");
    		    return;
    		}
    		if ($temp['birthday'] == "") {
    		    
    		    $this->error("至少告诉我你生日啊(￣ω￣)");
    		    return;
    		}
    		if ($temp['province'] == "" || $temp['city'] == "") {
    		    
    		    $this->error("\"省\"和\"市\"一个都不能少(￣ω￣)");
    		    return;
    		}
    		if ($temp['race'] == "") {
    		    
    		    $this->error("民族呢喂(￣ω￣)");
    		    return;
    		}
    		if ($temp['college'] == "" || $temp['major'] == "") {
    		    
    		    $this->error("\"学院\"和\"专业\"一个都不能少(￣ω￣)");
    		    return;
    		}
    		if ($temp['building'] == "" || $temp['room'] == "") {
    		    
    		    $this->error("(ง •̀_•́)ง嗯! 我真的需要知道你在哪栋哪个房间");
    		    return;
    		}
    		if ($temp['accept_deploy'] == "") {
    		    
    		    $this->error("快告诉我你接不接受工作地点调配(ง •̀_•́)ง<br/>接受能增加简历通过率哦(￣ω￣)");
    		    return;
    		}
    		if (!isset($temp['free_time'])) {
    		    
    		    $this->error("你这么忙你家里人知道么(￣ω￣)<br/>空闲晚数与简历通过率成正比, 相信我(ง •̀_•́)ง");
    		    return;
    		}
    		if ($temp['hobbies_n_expertise'] == "") {
    		    
    		    $this->error("你不会只是腿毛特长吧(￣ω￣)<br/>快写（╯' - ')╯（ ┻━┻ ");
    		    return;
    		}
    		if ($temp['reason'] == "") {
    		    
    		    $this->error("来, 告诉大家你想加入我们的原因┬—┬ ノ( ' - 'ノ)");
    		    return;
    		}
    		if ($temp['skill_level'] == "") {
    		    
    		    $this->error("只会开机关机也写一下啊, 干(╯°O°)╯（ ┻━┻");
    		    return;
    		}
    		if ($temp['joined_group'] == "") {
    		    
    		    $this->error("没加入其它组织么? 那写个\"无\"吧┬—┬ ノ( ' - 'ノ)");
    		    return;
    		}
    		if ($temp['self_assessment'] == "") {
    		    
    		    $this->error("自我评价都不写还想交简历(╯°Д°)╯（ ┻━┻");
    		    return;
    		}

    		pre_process_resume_data($temp);// 预处理简历数据

    		$info = session("RESUME_INFO");
    		// p($info);
    		
    		// 组装写入数据库的简历信息
    		$resume['name'] 					= $temp['name'];
    		$resume['gender'] 					= $temp['gender'];
    		$resume['birthday']				 	= $temp['birthday'];
    		$resume['birth_place']			 	= $temp['province'] ."省".$temp['city']."市";
    		$resume['race'] 					= $temp['race'];
    		$resume['college']				 	= $temp['college'];
    		$resume['major']				 	= $temp['major'];
    		$resume['short_phone']			 	= $temp['short_phone'];

    		$district = M('district')->getField('district_id,district_name');
    		// p($district);die;
    		$resume['address']				 	= $district[$temp['district']]."区".$temp['building']."栋".$temp['room']."房";

    		$resume['willing_district']		 	= $temp['willing_district'];
    		$resume['accept_deploy']		 	= $temp['accept_deploy'];
    		$resume['free_time']		 		= implode(',', $temp['free_time']);
    		$resume['hobbies_n_expertise']	 	= $temp['hobbies_n_expertise'];
    		$resume['reason']				 	= $temp['reason'];
    		$resume['skill_level']			 	= $temp['skill_level'];
    		$resume['joined_group']			 	= $temp['joined_group'];
    		$resume['self_assessment']		 	= $temp['self_assessment'];

    		$resume_model = M('resume');
    		if (!$resume_model->where($info)->find()) {
    			// 新用户
    			
	    		$resume['cTime']		 		= getDatetime();
	    		$resume['lastEditTime']		 	= $resume['cTime'];

	    		process_sessionInfo_to_Data($resume);// session里的简历标识数据->添加->简历数据

	    		// echo "add";
	    		$rst = $resume_model->add($resume);
	    		$tips = "简历创建";
    		}else {

    			$resume['lastEditTime']		 	= getDatetime();

    			// echo "save";
    			$rst = $resume_model->where($info)->save($resume);
    			$tips = "简历更新";
    		}

    		// var_dump($rst);
    		// p($resume);die;

    		if ($rst) {

	    		process_sessionInfo_to_Data($temp);// session里的简历标识数据->覆盖->简历cookie数据

				cookie($info['stu_id']."_RESUME_INFO", json_encode($temp, JSON_UNESCAPED_UNICODE));

				$this->success($tips."成功"."<br/>┬—┬ ノ( ' - 'ノ)简历投递截止前仍可以修改简历哦");
    		}else {

    			$this->error($tips."失败: ".$resume_model->getError()."<br/>(╯°O°)╯（ ┻━┻再来一次");
    		}

    	}else {

    		// echo DOMAIN_URL.U('Recruit/Resume/login');
    		// p($_SERVER);
    		// p(session());
    		// die;
    		if (!session('RESUME_LOGIN_FLAG')) {
    			
	    		if (strcmp($_SERVER['HTTP_REFERER'], DOMAIN_URL.U('Recruit/Resume/login')) != 0) {
	    			// 限制来源只能是login.html
	    			redirect(U('Recruit/Resume/login'));
	    		}else{
	    			session('RESUME_LOGIN_FLAG', true);
	    		}
    		}else{

    			// p(session());
    			// p(cookie());die;
    		}

			$this->display();
    	}
    }

    /**
     * 保存简历信息
     * 写cookie
     */
    public function save(){

    	if (IS_AJAX) {
    		
			// $this->ajaxReturn(I('post.'), 'json');
			// return;

    		$temp = I('post.');
    		unset($temp['verify']);// 去除验证码字段
    		
    		pre_process_resume_data($temp);// 预处理
    		process_sessionInfo_to_Data($temp);// session里的简历标识数据->覆盖->简历cookie数据

    		// p($temp);die;

    		// $this->ajaxReturn($temp, 'json');
    		// return;

			cookie($temp['stu_id']."_RESUME_INFO", json_encode($temp, JSON_UNESCAPED_UNICODE));
			$this->ajaxReturn(true, 'json');
    	}

    }

    /**
     * 获取验证码
     */
    public function verify(){

        // 调用function
        // verify();

        $config =    array(
            'fontSize'    =>    15,    // 验证码字体大小
            'useNoise'    =>    false, // 关闭验证码杂点
            'imageW'      =>    0,     // 验证码宽度
            'imageH'      =>    0,     // 验证码高度
            'codeSet'     =>    '123456789',//验证码字符
            'length'      =>    3,     // 验证码位数
        );
        $Verify =     new \Think\Verify($config);
        $Verify->entry();
    }
}