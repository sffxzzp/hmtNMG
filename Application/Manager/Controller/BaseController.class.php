<?php
namespace Manager\Controller;
use Common\Controller\CommonController;
class BaseController extends CommonController {
/**
 * 项目总控制器，直接继承CommonController
 */

	const LOGIN_FROM 			= 'manager/user/login';

	protected static $default_nav;// 默认初始导航
	protected static $navbar = array();// 导航条
    
	protected function _initialize(){

		parent::_initialize();
		
		// 设置导航
		$target_c = U("Manager/Resume/lists");
		switch (self::$RECRUIT_STAGE) {
			case 0:
				// 设置默认初始导航
				self::$default_nav['name'] = "待筛选";
				self::$default_nav['url'] = $target_c."?target=pendingcvs";

				// 设置导航条
				self::$navbar['pendingcvs']['name'] = "待筛选";
				self::$navbar['pendingcvs']['url'] = $target_c."?target=pendingcvs";
				self::$navbar['pendingcvs']['operation'] = '';// 简历投递阶段，操作为空
				break;
			case 1:
				// 设置默认初始导航
				self::$default_nav['name'] = "待筛选";
				self::$default_nav['url'] = $target_c."?target=pendingcvs";

				// 设置导航条
				self::$navbar['pendingcvs']['name'] = "待筛选";
				self::$navbar['pendingcvs']['url'] = $target_c."?target=pendingcvs";
				self::$navbar['pendingcvs']['operation'][0]['name'] = '简历通过';
				self::$navbar['pendingcvs']['operation'][0]['url']  = U('Manager/Resume/cv_pass');
				self::$navbar['pendingcvs']['operation'][1]['name'] = '简历未通过';
				self::$navbar['pendingcvs']['operation'][1]['url']  = U('Manager/Resume/cv_fail');
				self::$navbar['failedcvs']['name'] = "未通过筛选";
				self::$navbar['failedcvs']['url'] = $target_c."?target=failedcvs";
				self::$navbar['passedcvs']['name'] = "等待面试";
				self::$navbar['passedcvs']['url'] = $target_c."?target=passedcvs";
				break;
			case 2:
				// 设置默认初始导航
				self::$default_nav['name'] = "等待面试";
				self::$default_nav['url'] = $target_c."?target=passedcvs";

				// 设置导航条
				self::$navbar['passedcvs']['name'] = "等待面试";
				self::$navbar['passedcvs']['url'] = $target_c."?target=passedcvs";
				self::$navbar['passedcvs']['operation'][0]['name'] = '面试通过';
				self::$navbar['passedcvs']['operation'][0]['url']  = U('Manager/Resume/interview_rst').'?pass';
				self::$navbar['passedcvs']['operation'][1]['name'] = '面试未通过';
				self::$navbar['passedcvs']['operation'][1]['url']  = U('Manager/Resume/interview_rst').'?fail';
				self::$navbar['failedcvs']['name'] = "未通过筛选";// 可以霸面
				self::$navbar['failedcvs']['url'] = $target_c."?target=failedcvs";
				self::$navbar['failedcvs']['operation'][0]['name'] = '霸面通过';
				self::$navbar['failedcvs']['operation'][0]['url']  = U('Manager/Resume/interview_rst').'?pass';
				self::$navbar['failedcvs']['operation'][1]['name'] = '霸面未通过';
				self::$navbar['failedcvs']['operation'][1]['url']  = U('Manager/Resume/interview_rst').'?fail';
				self::$navbar['interviewfailed']['name'] = "未通过面试";
				self::$navbar['interviewfailed']['url'] = $target_c."?target=interviewfailed";
				self::$navbar['interviewpass']['name'] = "实习生";
				self::$navbar['interviewpass']['url'] = $target_c."?target=interviewpass";
				break;
			case 3:
				// 设置默认初始导航
				self::$default_nav['name'] = "实习生";
				self::$default_nav['url'] = $target_c."?target=interviewpass";

				// 设置导航条
				self::$navbar['interviewpass']['name'] = "实习生";
				self::$navbar['interviewpass']['url'] = $target_c."?target=interviewpass";
				self::$navbar['interviewpass']['operation'][0]['name'] = '实习转正';
				self::$navbar['interviewpass']['operation'][0]['url']  = U('Manager/Resume/internship_rst').'?pass';
				self::$navbar['interviewpass']['operation'][1]['name'] = '实习淘汰';
				self::$navbar['interviewpass']['operation'][1]['url']  = U('Manager/Resume/internship_rst').'?fail';
				self::$navbar['interviewfailed']['name'] = "未通过面试";
				self::$navbar['interviewfailed']['url'] = $target_c."?target=interviewfailed";
				self::$navbar['internshippass']['name'] = "转正";
				self::$navbar['internshippass']['url'] = $target_c."?target=internshippass";
				self::$navbar['internshipfailed']['name'] = "实习淘汰";
				self::$navbar['internshipfailed']['url'] = $target_c."?target=internshipfailed";
				break;
			case 4:
				// 设置默认初始导航
				self::$default_nav['name'] = "转正";
				self::$default_nav['url'] = $target_c."?target=internshippass";

				// 设置导航条
				self::$navbar['internshippass']['name'] = "转正";
				self::$navbar['internshippass']['url'] = $target_c."?target=internshippass";
				self::$navbar['internshipfailed']['name'] = "实习淘汰";
				self::$navbar['internshipfailed']['url'] = $target_c."?target=internshipfailed";
				// self::$navbar['allfailed']['name'] = "所有淘汰者";
				// self::$navbar['allfailed']['url'] = $target_c."?target=allfailed";
				// self::$navbar['allfailed']['operation'] = '';// 转正阶段，操作为空
				break;
			default:
				// 不可能执行到这里
				break;
		}

		// 判断登录是否过期
		if (session('?MANAGER_EXPIRE') && (NOW_TIME - session('MANAGER_EXPIRE') > self::SESSION_EXPIRE_USER_DEFINED)) {
			// 用户2次操作时间间隔已经超过session过期时间间隔
			session('MANAGER_INFO', null);
			session('MANAGER_LOGIN_FLAG', null);
            session('MANAGER_EXPIRE', null);

			$this->error("登录过期！请重新登录！", U(self::LOGIN_FROM));
			return;
		}

		$CUR_ASK_FROM = strtolower(MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME);

		if (strcmp(self::LOGIN_FROM, $CUR_ASK_FROM) == 0) {
			
			if (session('MANAGER_LOGIN_FLAG')) {
				// 重复登录，跳转到主页
				$this->error("您已经登录！", U('Manager/Resume/index'), 1);
				
				// 更新销毁session时间
				session('MANAGER_EXPIRE', NOW_TIME);
			}else{
				// 进入登录页面
			}
		}else{


			if (session('MANAGER_LOGIN_FLAG')) {
				// 进入目标页面
				
				// 更新销毁session时间
				session('MANAGER_EXPIRE', NOW_TIME);
			}else{

				// 未登录，跳转到登录页面
				$this->error("请先登录！", U(self::LOGIN_FROM), 1);
			}
		}
	}

}