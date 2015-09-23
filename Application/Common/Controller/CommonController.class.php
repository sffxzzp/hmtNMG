<?php
namespace Common\Controller;
use Think\Controller;
class CommonController extends Controller {
/**
 * 总控制器，直接继承Controller
 */
	const COMMON 					= "CommonController";

	const RECRUIT_STAGE_0_INFO		= "简历投递阶段";
	const RECRUIT_STAGE_1_INFO		= "简历筛选阶段";
	const RECRUIT_STAGE_2_INFO		= "面试阶段";
	const RECRUIT_STAGE_3_INFO		= "实习阶段";
	const RECRUIT_STAGE_4_INFO		= "转正阶段";
	// 0简历投递阶段、1简历筛选阶段、2面试阶段、3实习阶段
	public static $RECRUIT_STAGE;
	public static $RECRUIT_STAGE_INFO;
	protected static $RECRUIT_STAGE_CHANGED;// 阶段改变标志

	protected function _initialize(){

		$conf = M('schedule')->find(1);// 得到id为1的配置数据
		if (!$conf) {
			$this->error("未找到-招聘进度-配置参数");
			return;
		}

		self::$RECRUIT_STAGE = $conf['stage'];
		switch (self::$RECRUIT_STAGE) {
			case 0:
				self::$RECRUIT_STAGE_INFO = self::RECRUIT_STAGE_0_INFO;
				break;
			case 1:
				self::$RECRUIT_STAGE_INFO = self::RECRUIT_STAGE_1_INFO;
				break;
			case 2:
				self::$RECRUIT_STAGE_INFO = self::RECRUIT_STAGE_2_INFO;
				break;
			case 3:
				self::$RECRUIT_STAGE_INFO = self::RECRUIT_STAGE_3_INFO;
				break;
			case 4:
				self::$RECRUIT_STAGE_INFO = self::RECRUIT_STAGE_4_INFO;
				break;
			default:
				self::$RECRUIT_STAGE_INFO = "不存在的招聘阶段";
				$this->error(self::$RECRUIT_STAGE_INFO);
				return;
				break;
		}


		// p($conf);
		// echo self::$RECRUIT_STAGE;
		// die;
	}
}

?>