<?php
namespace Manager\Controller;
use Think\Controller;
class ResumeController extends BaseController {
    public function index(){
        echo "Resume控制器";
    }

    public function lists(){

    	$info = session('MANAGER_INFO');

    	$model = M('resume');

    	if ($info['d_ID'] == 9) {// 总负责人
    		
            $all_CVs = $model->join('district ON resume.willing_district = district.district_id')->select();
        }else {

	    	$first['willing_district'] = $info['d_ID'];
        	$first_willing_CVs = $model->join('district ON resume.willing_district = district.district_id')->where($first)->select();

            $other['willing_district']  = array('neq',$info['d_ID']);
            $other['accept_deploy']  = array('eq',1);
            $other_accecpt_deploy_CVs = $model->join('district ON resume.willing_district = district.district_id')->where($other)->select();

            $all_CVs = array_merge($first_willing_CVs, $other_accecpt_deploy_CVs);
        }
        
        // p($all_CVs);die;

        $this->assign('data', $all_CVs);
        $this->display();
    }
}