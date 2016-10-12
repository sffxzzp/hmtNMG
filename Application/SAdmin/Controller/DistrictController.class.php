<?php
namespace Sadmin\Controller;
use Think\Controller;
class DistrictController extends BaseController {
    
    public function index(){
        echo "DistrictController";
    }

    /**
     * 重置(各区)管理员密码
     */
    public function reset_pwd(){
    	if (IS_POST) {
    		$post_data = I('post.');
    		$model = M('district_admin');
    		$superadmin = $model->where('district_ID = 9')->select();
    		$superadmin = $superadmin[0];
    		if (md5($post_data['verify_pwd']) == $superadmin['password']) {
    			$data['password'] = md5($post_data['new_pwd']);
    			$model->where('district_ID = '.$post_data['target_admin'])->save($data);
    			$this->success = '修改成功！';
    		}
    	}
    	$this->display();
    }
}