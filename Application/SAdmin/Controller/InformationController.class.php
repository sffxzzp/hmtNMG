<?php
namespace Sadmin\Controller;
use Think\Controller;
class InformationController extends BaseController {

    public function index(){
        echo "InformationController";
    }

    /**
     * 导出简历信息
     */
    public function export(){
    	if (IS_POST) {
    		$reqType = I('post.type');
    		if ($reqType == "resume") {
    			$model = M('resume');
    			echo json_encode($model->select());
    		}
    		elseif ($reqType == "handle") {
    			$model = M('resume_handle');
    			echo json_encode($model->select());
    		}
    		elseif ($reqType == "log") {
    			$model = M('resume_login_log');
    			echo json_encode($model->select());
    		}
    		else {
    			echo '{"success": false}';
    		}
    	}
    	else {
    		$this->display();
    	}
    }
}