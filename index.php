<?php

	define('APP_DEBUG', false);// 开启调试，上线前须关闭
    //---------------------
    //SQL信息需要修改多次。分别位于Application下各文件夹下的Conf目录中。
    //---------------------
	// // 绑定Recruit模块到当前入口文件
	// define('BIND_MODULE','Recruit');
	// define('BUILD_CONTROLLER_LIST','Index,Resume');
	// define('BUILD_MODEL_LIST','Resume');

    define('APP_PATH', './Application/');
    define('THINK_PATH', './ThinkPHP/');
    
    define('DOMAIN_URL', "http://127.0.0.1");//服务器域名

    // define('PUBLIC_URL', '/hmtNMG/Application/Public');//Public公共文件夹路径
    
    define('APP_ROOT', '/hmtNMG'); //配置访问根目录至应用路径。
    define('RECRUIT_SRC', APP_ROOT.'/Application/Recruit/Source');// Recruit资源文件夹路径
    define('MANAGER_SRC', APP_ROOT.'/Application/Manager/Source');// Manager资源文件夹路径
    define('SADMIN_SRC', APP_ROOT.'/Application/SAdmin/Source');// SAdmin资源文件夹路径

    define('RECRUIT_TITLE', '招新系统');
    define('hmtNMG', '红满堂网络维护组(HMT Network Maintenance Group)');

    require THINK_PATH.'ThinkPHP.php'

?>