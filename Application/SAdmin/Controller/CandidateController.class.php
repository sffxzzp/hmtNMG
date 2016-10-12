<?php
namespace Sadmin\Controller;
use Think\Controller;
class CandidateController extends BaseController {

    public function index(){
    	echo "CandidateController";
    }

    /**
     * 应聘者登录日志
     */
    public function login_log(){
        if (IS_POST) {
            $model = M('resume_login_log');
            $result = $model->select();
            echo json_encode($result);
        }
        else {
            $this->display();
        }
    }

    /**
     * 简历(各区)处理情况
     */
    public function cv_status(){
        $info = session('MANAGER_INFO');
        if ($info['d_ID'] == 9) {// 总负责人
            //先取当前招聘阶段
            $this->display();
        }
        else {//非总负责人
            redirect(U('Manager/Resume/lists'));
        }
    }
}