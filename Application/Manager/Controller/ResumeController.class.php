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
    const INTERNSHIP_PASS                 =   8;// 成功转正
    const TEXT_INTERNSHIP_PASS            =   "成功转正";
    const INTERNSHIP_FAIL                 =   9;// 实习淘汰
    const TEXT_INTERNSHIP_FAIL            =   "实习淘汰";


    public function index(){
        $info = session('MANAGER_INFO');
        if ($info['d_ID'] == 9) {// 总负责人
            redirect(U('Sadmin/Candidate/cv_status'));
        }
        else {//非总负责人
            redirect(U('Manager/Resume/lists'));
        }
    }

    /**
     * 简历列表
     */
    public function lists(){

        // 如果没有target参数，或者有target但是target不在导航内
        if (!I('get.target') || !array_key_exists(I('get.target'), self::$navbar)) {
            $default_nav = self::$default_nav;
            redirect($default_nav['url']);
        }

        // 根据target，从数据库取相应内容
        switch (I('get.target')) {
          case 'pendingcvs':// 空
            $status = array('EXP','IS NULL');
            break;
          case 'passedcvs':
            $status = self::CV_PASS;
            break;
          case 'failedcvs':
            $status = self::CV_FAIL;
            break;
          case 'interviewpass':
            $status = self::INTERVIEW_PASS;
            break;
          case 'interviewfailed':
            $status = self::INTERVIEW_FAIL;
            break;
          case 'internshippass':
            $status = self::INTERNSHIP_PASS;
            break;
          case 'internshipfailed':
            $status = self::INTERNSHIP_FAIL;
            break;
          default:
            break;
        }

        // var_dump($status);

        // echo self::$RECRUIT_STAGE;die;

    	$info = session('MANAGER_INFO');

    	$model = M('resume');

    	if ($info['d_ID'] == 9) {// 总负责人
    		
            for ($i=1; $i <= 5; $i++) { 
                $map['district_'.$i] = $status;
            }

            switch (I('get.target')) {
              case 'pendingcvs':
                $map['status_text'] = self::TEXT_CV_NEW;
                break;
              case 'passedcvs':
              case 'interviewpass':
              case 'interviewfailed':
              case 'internshippass':
              case 'internshipfailed':
                // 以上，有1个满足即可
                $map['_logic'] = 'or';
                break;
              case 'failedcvs':
                // accept_deploy = 1 && 5区都不通过
                // 或者accept_deploy = 0 && (5区有1区不通过)
                $map = '(`accept_deploy`=1 AND `district_1`='.self::CV_FAIL
                    .' AND `district_2`='.self::CV_FAIL
                    .' AND `district_3`='.self::CV_FAIL
                    .' AND `district_5`='.self::CV_FAIL
                    .' AND `district_4`='.self::CV_FAIL.')'
                    .'OR (`accept_deploy`=0 AND (`district_1`='.self::CV_FAIL
                        .' OR `district_2`='.self::CV_FAIL
                        .' OR `district_3`='.self::CV_FAIL
                        .' OR `district_4`='.self::CV_FAIL
                        .' OR `district_5`='.self::CV_FAIL.'))';
                break;
              default:
                break;
            }

            $all_CVs = $model->join('district ON resume.willing_district = district.district_id')
                    ->join('resume_handle ON resume.cv_id = resume_handle.cv_id')
                    ->field('resume.cv_id,stu_id,name,photo,gender,birthday,birth_place,race,college,major,phone,short_phone,address,willing_district,accept_deploy,free_time,hobbies_n_expertise,reason,skill_level,joined_group,self_assessment,cTime,lastEditTime,cv_flag,district_name,last_rst_district,status_text,interview_comment,internship_comment')
                    ->where($map)
                    ->select();
        }else {

            $first['willing_district'] = $info['d_ID'];
	    	$first['district_'.$info['d_ID']] = $status;
        	$first_willing_CVs = $model->join('district ON resume.willing_district = district.district_id')
                    ->join('resume_handle ON resume.cv_id = resume_handle.cv_id')
                    ->where($first)
                    ->field('resume.cv_id,stu_id,name,photo,gender,birthday,birth_place,race,college,major,phone,short_phone,address,willing_district,accept_deploy,free_time,hobbies_n_expertise,reason,skill_level,joined_group,self_assessment,cTime,lastEditTime,cv_flag,district_name,'.'district_'.$info['d_ID'].',last_rst_district,status_text,interview_comment,internship_comment')
                    ->select();

            $other['willing_district']  = array('neq',$info['d_ID']);
            $other['accept_deploy']  = array('eq',1);
            $other['district_'.$info['d_ID']] = $status;
            $other_accecpt_deploy_CVs = $model->join('district ON resume.willing_district = district.district_id')
                    ->join('resume_handle ON resume.cv_id = resume_handle.cv_id')
                    ->where($other)
                    ->field('resume.cv_id,stu_id,name,photo,gender,birthday,birth_place,race,college,major,phone,short_phone,address,willing_district,accept_deploy,free_time,hobbies_n_expertise,reason,skill_level,joined_group,self_assessment,cTime,lastEditTime,cv_flag,district_name,'.'district_'.$info['d_ID'].',last_rst_district,status_text,interview_comment,internship_comment')
                    ->select();

            if (is_array($first_willing_CVs) && is_array($other_accecpt_deploy_CVs)) {
                $all_CVs = array_merge($first_willing_CVs, $other_accecpt_deploy_CVs);
            }elseif(is_array($first_willing_CVs)){
                $all_CVs = $first_willing_CVs;
            }else{
                $all_CVs = $other_accecpt_deploy_CVs;
            }
        }
        
        // p($model);
        // p($first_willing_CVs);
        // p($other_accecpt_deploy_CVs);
        // p($all_CVs);die;

        $this->assign('data', $all_CVs);
        $this->assign('navbar', self::$navbar);
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

        // var_dump(U('Manager/Resume/lists'));
        // var_dump($_SERVER['HTTP_REFERER']);
        // var_dump($this->success(self::TEXT_CV_PASS."，处理成功！", $_SERVER['HTTP_REFERER'], 5));
        // die;
        if (strrchr($old_handle_data['status_text'], self::TEXT_CV_PASS) == '') {// 防止重复添加状态
            $handle['status_text'] = $old_handle_data['status_text']." -> ".self::TEXT_CV_PASS;
        }
        $rst = $model->save($handle);

        // var_dump($rst);
        // die;
         

        if ($rst) {
            /*U('Manager/Resume/lists').(isset($_SERVER['HTTP_REFERER']) ? '?'.end(explode('?', $_SERVER['HTTP_REFERER'])) : '')*/
            $this->success(self::TEXT_CV_PASS."，处理成功！", $_SERVER['HTTP_REFERER']);
        }else{
            $this->error(self::TEXT_CV_PASS."，处理失败！");
        }
    }

    /**
     * 简历未通过筛选
     */
    public function cv_fail(){

        // p($_SERVER);
        // die;

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
            $this->success(self::TEXT_CV_FAIL."，处理成功！", $_SERVER['HTTP_REFERER']);
        }else{
            $this->error(self::TEXT_CV_FAIL."，处理失败！");
        }
    }

    /**
     * 面试结果
     * pass / fail
     */
    public function interview_rst(){
        if (self::$RECRUIT_STAGE != 2) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_2_INFO."\" 才能更新面试状态！");
            return;
        }

        if (I('get.id') == '') {
            $this->error("id不能为空！");
            return;
        }

        $get = I('get.');
        if (!isset($get['pass']) && !isset($get['fail'])) {
            $this->error("未指定结果！");
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

        // 通过与不通过，判断的条件不同
        if (isset($get['pass'])) {// pass

            if ($resume['accept_deploy'] == 0) {// 不接受调配
                if ($info['d_ID'] != $resume['willing_district']) {// 意愿与处理简历的管理员区域不符(避免管理员的非法操作)
                    $this->error("该应聘者不接受调配，还是不要强人所难了！");
                    return;
                }
            }
        }else {// fail
            
            if ($info['d_ID'] != $resume['willing_district']) {
                $this->error("只有对应的意向区才能刷面试者哦！");
                return;
            }
        }

        $model = M('resume_handle');
        $old_handle_data = $model->find($cv_id);// 获取旧的处理结果数据
        // 只要有1个区处理状态为5或6，则该人已有面试结果
        $hasInterviewed = false;
        for ($i=1; $i <= 5; $i++) { 
            if ($old_handle_data['district_'.$i] == self::INTERVIEW_PASS || $old_handle_data['district_'.$i] == self::INTERVIEW_FAIL) {
                $hasInterviewed = true;
                break;
            }
        }
        if ($hasInterviewed) {
            $this->error("该面试者已有面试结果，请勿重复操作！");
            return;
        }

        // 只能来自状态1和3，则五个区中只要有1或3就行
        $canInterview = false;
        for ($i=1; $i <= 5; $i++) { 
            if ($old_handle_data['district_'.$i] == self::CV_PASS || $old_handle_data['district_'.$i] == self::CV_FAIL) {
                $canInterview = true;
                break;
            }
        }
        if (!$canInterview) {
            $this->error("该面试者无权参与面试！");
            return;
        }

        $tip = '';
        if (isset($get['pass'])) {// pass
            
            $handle['cv_id'] = $cv_id;
            $handle['district_'.$info['d_ID']] = self::INTERVIEW_PASS;
            $handle['last_rst_district'] = $info['d_ID'];// 最终去向
            $handle['status_text'] = $old_handle_data['status_text']." -> ".self::TEXT_INTERVIEW_PASS.'['.$info['d_name'].']';
            $tip = self::TEXT_INTERVIEW_PASS;
        }else {// fail

            $handle['cv_id'] = $cv_id;
            $handle['district_'.$info['d_ID']] = self::INTERVIEW_FAIL;
            $handle['status_text'] = $old_handle_data['status_text']." -> ".self::TEXT_INTERVIEW_FAIL;
            $tip = self::TEXT_INTERVIEW_FAIL;
        }
        
        $rst = $model->save($handle);

        if ($rst) {
            $this->success($tip."，处理成功！", $_SERVER['HTTP_REFERER']);
        }else{
            $this->error($tip."，处理失败！");
        }
    }

    /**
     * 面试评价
     */
    public function interview_comment(){
        // 一次性评价
        // 只有第1个人能够提交评价，其后的人只能查看到评语不能提交
        // 得到该简历id
        // 该简历是否已有面试评价(即，是否已经面试过)
        // 此阶段只判断会不会重复面试，评价可能来自各区
        
        if (self::$RECRUIT_STAGE != 2) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_2_INFO."\" 才能给出面试评价！");
            return;
        }

        if (I('get.id') == '') {
            $this->error("id不能为空！");
            return;
        }

        if (I('post.interview_comment') == '') {
            $this->error("评价不能为空！");
            return;
        }

        $cv_id = I('get.id');

        $model = M('resume_handle');
        $old_handle_data = $model->find($cv_id);// 获取旧的处理结果数据

        if (!$old_handle_data) {
            $this->error("不存在的简历！");
            return;
        }

        if ($old_handle_data['interview_comment'] != '') {// 已有评价
            $this->error("已存在面试评价，不能重复操作！");
            return;
        }

        $info = session('MANAGER_INFO');

        $handle['cv_id'] = $cv_id;
        $handle['interview_comment'] = '['.$info['d_name'].']: '.I('post.interview_comment');

        $rst = $model->save($handle);

        if ($rst) {
            $this->success("面试评价，处理成功！", $_SERVER['HTTP_REFERER']);
        }else{
            $this->error("面试评价，处理失败！");
        }
    }

    /**
     * 实习结果
     * psss / fail
     */
    public function internship_rst(){
        if (self::$RECRUIT_STAGE != 3) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_3_INFO."\" 才能更新最终状态！");
            return;
        }

        if (I('get.id') == '') {
            $this->error("id不能为空！");
            return;
        }

        $get = I('get.');
        if (!isset($get['pass']) && !isset($get['fail'])) {
            $this->error("未指定结果！");
            return;
        }

        $info = session('MANAGER_INFO');
        if ($info['d_ID'] == 9) {
            $this->error("总负责人不能处理简历！");
            return;
        }

        $cv_id = $get['id'];// 简历编号
        $resume = M('resume')->find($cv_id);

        if (!$resume) {
            $this->error("不存在的简历！");
            return;
        }

        $model = M('resume_handle');
        $old_handle_data = $model->find($cv_id);// 获取旧的处理结果数据
        // last_rst_district为空 或者状态不是5，非实习生
        if ($old_handle_data['last_rst_district'] == '' || $old_handle_data['district_'.$old_handle_data['last_rst_district']] != self::INTERVIEW_PASS) {
            $this->error("非实习生！非法操作！");
            return;
        }

        // 防止跨实习区操作
        if ($info['d_ID'] != $old_handle_data['last_rst_district']) {
            $this->error("您与实习生所在实习区不符，不能给出实习结果！");
            return;
        }

        // 如果'district_'.$last_rst_district状态为8或9，则已处理
        if ($old_handle_data['district_'.$old_handle_data['last_rst_district']] == self::INTERNSHIP_PASS || $old_handle_data['district_'.$old_handle_data['last_rst_district']] == self::INTERNSHIP_FAIL) {
            $this->error("该实习生已有实习结果，请勿重复操作！");
            return;
        }

        $tip = '';// 提示信息
        if (isset($get['pass'])) {// pass
            $handle['cv_id'] = $cv_id;
            $handle['district_'.$info['d_ID']] = self::INTERNSHIP_PASS;
            $handle['status_text'] = $old_handle_data['status_text']." -> ".self::TEXT_INTERNSHIP_PASS;
            $tip = self::TEXT_INTERNSHIP_PASS;
        }else {// fail
            $handle['cv_id'] = $cv_id;
            $handle['district_'.$info['d_ID']] = self::INTERNSHIP_FAIL;
            $handle['status_text'] = $old_handle_data['status_text']." -> ".self::TEXT_INTERNSHIP_FAIL;
            $tip = self::TEXT_INTERNSHIP_FAIL;
        }

        $rst = $model->save($handle);

        if ($rst) {
            $this->success($tip."，处理成功！", $_SERVER['HTTP_REFERER']);
        }else{
            $this->error($tip."，处理失败！");
        }
    }

    /**
     * 实习评价
     */
    public function internship_comment(){
        // 得到该简历id
        // 只能由对应的实习区评价
        // 对应区可以重复评价，以最后一次为准
        
        if (self::$RECRUIT_STAGE != 3) {
            $this->error("当前: \"".self::$RECRUIT_STAGE_INFO."\"".
                "<br/ >只有 \"".self::RECRUIT_STAGE_3_INFO."\" 才能给出实习评价！");
            return;
        }

        if (I('get.id') == '') {
            $this->error("id不能为空！");
            return;
        }

        if (I('post.internship_comment') == '') {
            $this->error("评价不能为空！");
            return;
        }

        $cv_id = I('get.id');

        $model = M('resume_handle');
        $old_handle_data = $model->find($cv_id);// 获取旧的处理结果数据

        if (!$old_handle_data) {
            $this->error("不存在的简历！");
            return;
        }

        $info = session('MANAGER_INFO');
        if ($old_handle_data['last_rst_district'] != $info['d_ID']) {
            $this->error("只有对应的实习区才能给出评价！");
            return;
        }

        $handle['cv_id'] = $cv_id;
        $handle['internship_comment'] = I('post.internship_comment');

        $rst = $model->save($handle);

        if ($rst) {
            $this->success("实习评价，处理成功！", $_SERVER['HTTP_REFERER']);
        }else{
            $this->error("实习评价，处理失败！");
        }
    }
}