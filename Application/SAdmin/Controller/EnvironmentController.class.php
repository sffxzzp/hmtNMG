<?php
namespace Sadmin\Controller;
use Think\Controller;
class EnvironmentController extends BaseController {

    public function index() {
    	echo "EnvironmentController";
    }

    /**
     * 一键(从0)开始招聘
     */
    public function onekey_init() {
        if (IS_POST) {
            if (session('MANAGER_INFO')['d_ID'] == '9') {
                $model = new \Think\Model();
                $model->execute('SET FOREIGN_KEY_CHECKS = 0;');
                $model->execute('truncate resume');
                $model->execute('truncate resume_handle');
                $model->execute('truncate resume_login_log');
                $model->execute('SET FOREIGN_KEY_CHECKS = 1;');
                $model = M('schedule');
                $data['id'] = 1;
                $data['stage'] = 0;
                $model->save($data);
                $this->handle = '重置成功！<br>';
                $this->disabled = 'disabled';
            }
        }
        else {
            $this->handle = '此功能将不可逆转，请慎重使用。';
        }
        $this->display();
    }

    /**
     * 备份数据
     */
    public function bk_DB() {
        $this->display();
    }

    /**
     * 改变招聘阶段
     */
    public function change_stage() {
        $model = M('schedule');
        if (IS_POST) {
            $zpjd = I('post.zpjd');
            $data['id'] = 1;
            $data['stage'] = (int)($zpjd);
            $model->save($data);
        }
        $stage = $model->where('id=1')->find();
        if ($stage['stage'] == '0') {
            $zpjd = '简历投递阶段';
            $this->stage0 = 'checked';
        }
        elseif ($stage['stage'] == '1') {
            $zpjd = '简历筛选阶段';
            $this->stage1 = 'checked';
        }
        elseif ($stage['stage'] == '2') {
            $zpjd = '面试阶段';
            $this->stage2 = 'checked';
        }
        elseif ($stage['stage'] == '3') {
            $zpjd = '实习阶段';
            $this->stage3 = 'checked';
        }
        elseif ($stage['stage'] == '4') {
            $zpjd = '转正阶段';
            $this->stage4 = 'checked';
        }
        $this->zpjd = $zpjd;
        $this->display();
    }
}