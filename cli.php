<?php

/**
 * cli入口
 *
 * @copyright copyright(2011) weibo.com all rights reserved
 * @author Chengxuan <chengxuan@staff.sina.com.cn> 
 */
define('ROOTPATH', dirname(__FILE__));
define('APPPATH', ROOTPATH . '/application');
//define('IS_DEBUG_XHPROF', FALSE);
//function main($argc, $argv) {
//}
$app = new Yaf_Application(APPPATH . '/config/application.ini');
$app->bootstrap()->getDispatcher()->dispatch(new Yaf_Request_Simple());
