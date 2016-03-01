<?php

/**
 * url rewrite规则
 */

class RewritePlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		//确定module
		if ($request->isCli()) {
			$this->module = 'Cli';
		} else {
			$this->host = $request->getServer('HTTP_HOST');
			if (substr($this->host, 0, 1) == 'i') {
				$this->module = 'Internal';
			} else {
				$this->module = 'Index';
			}
		}
		$routes_config = new Yaf_Config_ini(APPPATH . '/config/Routers/' . strtolower($this->module) . '.ini', 'routers');
		//加载相应模块的路由器
		$router = Yaf_Dispatcher::getInstance()->getRouter();
		$router->addConfig($routes_config->routes);
    }

	//分发循环前
	public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		//判断解析的路由器模块是否和预计的一样
		$module = $request->getModuleName();
		//非法模块
		if ($module != 'Index') {
			if ($this->module == 'Index') {
				$request->setModuleName('Index');
				throw new Yaf_Exception_LoadFailed_Module('Illegal Modules');
			}
		}

		//将模块设置为理想模块
		$request->setModuleName($this->module);
		//模版路径定义
		if ($this->module == 'Index') {
			define('TPLPATH', APPPATH . 'application/views/');
		} else {
			define('TPLPATH', APPPATH . 'application/modules/' . $this->module . '/views/');
		}
	}
}