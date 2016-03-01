<?php
//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
//function xhprof_shutdown(){
//	require_once('xhprof_lib/utils/xhprof_lib.php');
//	require_once('xhprof_lib/utils/xhprof_runs.php');
//	$xhprof_data = xhprof_disable();
//	$xhprof_runs = new XHProfRuns_Default();
//	$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");
//}
//register_shutdown_function('xhprof_shutdown');
//
error_reporting(E_ALL);

define('ROOTPATH', dirname(__FILE__));
define('APPPATH', ROOTPATH . '/application');
define('APPNAME', 'lovetolove_app'); //微信接入token
define('TOKEN', 'eye2eye917713'); //微信接入token
define('APPID', 'wx48dab544b687e389'); //微信公众号appid

$app = new Yaf_Application(APPPATH . '/config/application.ini');
$app->bootstrap()->run();
