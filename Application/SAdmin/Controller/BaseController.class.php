<?php
namespace SAdmin\Controller;
use Common\Controller\CommonController;
class BaseController extends CommonController {
/**
 * 项目总控制器，直接继承CommonController
 */
    public function index(){
    	echo "BaseController";
    }
}