<?php

	define('APP_DEBUG', true);// 开启调试，上线前须关闭

	// // 绑定Recruit模块到当前入口文件
	// define('BIND_MODULE','Recruit');
	// define('BUILD_CONTROLLER_LIST','Index,Resume');
	// define('BUILD_MODEL_LIST','Resume');

    define('APP_PATH', './Application/');
    define('THINK_PATH', './ThinkPHP/');
    
    define('DOMAIN_URL', "http://127.0.0.1");//服务器域名

    // define('PUBLIC_URL', '/hmtNMG/Application/Public');//Public公共文件夹路径
    
    define('RECRUIT_SRC', '/hmtNMG/Application/Recruit/Source');// Recruit资源文件夹路径

    define('RECRUIT_TITLE', '招新系统');
    define('hmtNMG', '红满堂网络维护组(HMT Network Maintenance Group)');

    require THINK_PATH.'ThinkPHP.php'

?>