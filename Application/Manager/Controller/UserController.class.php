<?php
namespace Manager\Controller;
use Think\Controller;
class UserController extends BaseController {

    public function index(){
    	echo "Manager控制器";
    }

    /**
     * 简历管理员登录
     * 
     */
    public function login(){
        // p(session());

    	if (IS_POST) {

	        // p(I('post.'));die;
    		if (!check_verify(I('post.verify'))) {
    			
    			$this->error('验证码不正确！');
    			return;
    		}

    		$temp = I('post.');
    		if ($temp['username'] == "" || $temp['pwd'] == "") {
    			$this->error("账号或密码不能为空！");
    			return;
    		}

            // p(I('post.'));
    		$login_data['username'] = $temp['username'];
    		$login_data['password'] = md5($temp['pwd']);

            // p($login_data);die;

    		$model = M('district_admin');
    		if (!$user_data = $model->where($login_data)->find()) {
    			$this->error("账号或密码不正确！");
    			return;
    		}

    		// p($model);
    		// p($login_data);
    		// p($user_data);
    		// die;

    		switch ($user_data['status']) {
    			case 0:

    				session('MANAGER_INFO', null);
    				session('MANAGER_LOGIN_FLAG', null);
                    session('MANAGER_EXPIRE', null);

    				$this->error("此账号目前关闭！");

    				break;
    			case 1:

    				$info['d_ID'] = $user_data['district_ID'];
    				$info['d_name'] = $user_data['district_name'];
    				// 通过验证，合法，写session
					session("MANAGER_INFO", $info);
                    session('MANAGER_LOGIN_FLAG', true);
	                session('MANAGER_EXPIRE', NOW_TIME);
    				
    				$this->success('登录成功！', U('Manager/Resume/index'));

    				break;
    			
    			default:

    				$this->error("非法操作！");

    				break;
    		}
    		
    	}else{

    		$this->display();
    	}
    }

    /**
     * 退出
     */
    public function quit(){
        
        session('MANAGER_INFO', null);
        session('MANAGER_LOGIN_FLAG', null);
        
        redirect(U("Manager/User/login"));
    }
}