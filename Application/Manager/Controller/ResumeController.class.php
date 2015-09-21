<?php
namespace Manager\Controller;
use Think\Controller;
class ResumeController extends BaseController {

    const CV_NEW                          =   0;// 新投递
    const TEXT_CV_NEW                     =   "新投递";
    const CV_PASS                         =   1;// 简历通过筛选，等待面试
    const TEXT_CV_PASS                    =   "简历筛选(通过)";
    const CV_FAIL                         =   3;// 简历未通过筛选
    const TEXT_CV_FAIL                    =   "简历筛选(未通过)";
    const INTERVIEW_PASS                  =   5;// 实习阶段
    const TEXT_INTERVIEW_PASS             =   "面试(通过)";
    const INTERVIEW_FAIL                  =   6;// 面试未通过
    const TEXT_INTERVIEW_FAIL             =   "面试(未通过)";
    const INTERSHIP_PASS                  =   8;// 成功转正
    const TEXT_INTERSHIP_PASS             =   "成功转正";
    const INTERSHIP_FAIL                  =   9;// 实习淘汰
    const TEXT_INTERSHIP_FAIL             =   "实习淘汰";


    public function index(){
        echo "Resume控制器";
    }

    public function lists(){

        // echo self::$RECRUIT_STAGE;die;

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

    /**
     * 简历通过筛选
     */
    public function cv_pass(){

        if (self::$RECRUIT_STAGE != 1) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_1_INFO."\" 才能筛选简历！");
            return;
        }

        if (I('get.id') == '') {
            $this->error("id不能为空！");
            return;
        }

        $info = session('MANAGER_INFO');
        if ($info['d_ID'] == 9) {
            $this->error("总负责人不能处理简历！");
            return;
        }

        $cv_id = I('get.id');// 简历编号
        $resume = M('resume')->find($cv_id);

        if (!$resume) {
            $this->error("不存在的简历！");
            return;
        }

        // p($info);
        // p($resume);
        // die;
        if ($resume['accept_deploy'] == 0) {// 不接受调配
            if ($info['d_ID'] != $resume['willing_district']) {// 意愿与处理简历的管理员区域不符(避免管理员的非法操作)
                $this->error("该应聘者不接受调配，还是不要强人所难了！");
                return;
            }
        }

        $model = M('resume_handle');
        $old_handle_data = $model->find($cv_id);// 获取旧的处理结果数据

        if ($old_handle_data['district_'.$info['d_ID']] != '') {
            
            $this->error("该简历已处理！请勿重复操作！");
            return;
        }

        $handle['cv_id'] = $cv_id;
        $handle['district_'.$info['d_ID']] = self::CV_PASS;

        // var_dump(strrchr($old_handle_data['status_text'], '简历筛选(通过)'));
        // die;

        if (strrchr($old_handle_data['status_text'], self::TEXT_CV_PASS) == '') {// 防止重复添加状态
            $handle['status_text'] = $old_handle_data['status_text']." -> ".self::TEXT_CV_PASS;
        }
        $rst = $model->save($handle);

        // var_dump($rst);
        // die;

        if ($rst) {
            $this->success(self::TEXT_CV_PASS."，处理成功！", U('Manager/Resume/lists'));
        }else{
            $this->error(self::TEXT_CV_PASS."，处理失败！");
        }
    }

    /**
     * 简历未通过筛选
     */
    public function cv_fail(){
        if (self::$RECRUIT_STAGE != 1) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_1_INFO."\" 才能筛选简历！");
            return;
        }

        if (I('get.id') == '') {
            $this->error("id不能为空！");
            return;
        }

        $info = session('MANAGER_INFO');
        if ($info['d_ID'] == 9) {
            $this->error("总负责人不能处理简历！");
            return;
        }

        $cv_id = I('get.id');// 简历编号
        $resume = M('resume')->find($cv_id);

        if (!$resume) {
            $this->error("不存在的简历！");
            return;
        }

        // p($info);
        // p($resume);
        // die;
        if ($resume['accept_deploy'] == 0) {// 不接受调配
            if ($info['d_ID'] != $resume['willing_district']) {// 意愿与处理简历的管理员区域不符(避免管理员的非法操作)
                $this->error("该应聘者不接受调配，还是不要强人所难了！");
                return;
            }
        }

        $model = M('resume_handle');
        $old_handle_data = $model->find($cv_id);// 获取旧的处理结果数据

        if ($old_handle_data['district_'.$info['d_ID']] != '') {
            
            $this->error("该简历已处理！请勿重复操作！");
            return;
        }

        $handle['cv_id'] = $cv_id;
        $handle['district_'.$info['d_ID']] = self::CV_FAIL;

        // var_dump(strrchr($old_handle_data['status_text'], '简历筛选(通过)'));
        // die;

        $flag = true;
        if ($resume['accept_deploy'] == 1) {// 接受调配

            for ($i=1; $i <= 5; $i++) { 
                if ($i == $info['d_ID']) {
                    // 排除掉本区(因为本区还未写数据库)
                    continue;
                }

                if ($old_handle_data['district_'.$i] == '') {// 还有其它区未处理该简历
                    $flag = false;
                    break;
                }
            }
        }

        if ($flag) {// 所有区都已经处理完，或者不接受调配

            // 有且仅有1个区能进入到这里
            if (strrchr($old_handle_data['status_text'], self::TEXT_CV_PASS) == '') {// 如果此时该简历还未通过

                $handle['status_text'] = $old_handle_data['status_text']." -> ".self::TEXT_CV_FAIL;
            }
        }

        $rst = $model->save($handle);

        if ($rst) {
            $this->success(self::TEXT_CV_FAIL."，处理成功！", U('Manager/Resume/lists'));
        }else{
            $this->error(self::TEXT_CV_FAIL."，处理失败！");
        }
    }

    /**
     * 面试通过
     */
    public function interview_pass(){
        if (self::$RECRUIT_STAGE != 2) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_2_INFO."\" 才能更新面试状态！");
            return;
        }
    }

    /**
     * 面试未通过
     */
    public function interview_fail(){
        if (self::$RECRUIT_STAGE != 2) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_2_INFO."\" 才能更新面试状态！");
            return;
        }
    }

    /**
     * 实习转正
     */
    public function internship_pass(){
        if (self::$RECRUIT_STAGE != 3) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_3_INFO."\" 才能更新最终状态！");
            return;
        }
    }

    /**
     * 实习淘汰
     */
    public function internship_fail(){
        if (self::$RECRUIT_STAGE != 3) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_3_INFO."\" 才能更新最终状态！");
            return;
        }
    }
}