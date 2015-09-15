<?php
namespace Manager\Controller;
use Common\Controller\CommonController;
class BaseController extends CommonController {
/**
 * 项目总控制器，直接继承CommonController
 */

	const LOGIN_FROM 			= 'manager/user/login';
    
	protected function _initialize(){

		// session('MANAGER_LOGIN_FLAG',null);

		$CUR_ASK_FROM = strtolower(MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME);

		if (strcmp(self::LOGIN_FROM, $CUR_ASK_FROM) == 0) {
			
			if (session('MANAGER_LOGIN_FLAG')) {
				// 重复登录，跳转到主页
				$this->error("您已经登录！", U('Manager/Resume/lists'), 1);
				
				// 更新销毁session时间
				session(array('name'=>'MANAGER_INFO','expire'=>3600));
				session(array('name'=>'MANAGER_LOGIN_FLAG','expire'=>3600));
			}else{
				// 进入登录页面
			}
		}else{

			if (session('MANAGER_LOGIN_FLAG')) {
				// 进入目标页面
				
				// 更新销毁session时间
				session(array('name'=>'MANAGER_INFO','expire'=>3600));
				session(array('name'=>'MANAGER_LOGIN_FLAG','expire'=>3600));
			}else{

				// 未登录，跳转到登录页面
				$this->error("请先登录！", U(self::LOGIN_FROM), 1);
			}
		}
	}

}