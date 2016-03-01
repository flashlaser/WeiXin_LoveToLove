<?php
/**
 * Enter description here ...
 */
class Bootstrap extends Yaf_Bootstrap_Abstract {
    /**
     * Enter description here ...
     * 
     * @param Yaf_Dispatcher $dispatcher dispatcher
     * 
     * @return void
     */
    public function _initLoader (Yaf_Dispatcher $dispatcher) {
        /* 注册本地类名前缀, 这部分类名将会在本地类库查找 */
        Yaf_Loader::getInstance()->registerLocalNameSpace(
            array(
				'Abstract',
				'Comm',
				'Tool',
				'Dr',
				'Dw',
				'Cache',
                'Data',
				'Exception',
			)
        );
    }
    /**
     * Enter description here ...
     * 
     * @param Yaf_Dispatcher $dispatcher dispatcher
     * 
     * @return void
     */
    public function _initConfig (Yaf_Dispatcher $dispatcher) {
        Yaf_Registry::set("config", Yaf_Application::app()->getConfig());
        Comm_Context::$keep_server_copy = true;
        Comm_Context::init();

        //Comm_Cache::auto_configure_pool();
        Comm_Db::auto_configure_pool();
    }
    
    /**
     * Enter description here ...
     * 
     * @param Yaf_Dispatcher $dispatcher dispatcher
     * 
     * @return void
     */
    public function _initPlugin (Yaf_Dispatcher $dispatcher) {
        //rpc请求，不加载插件
        if (strpos($_SERVER['HTTP_USER_AGENT'], "Yar")) {
            return;
        }

		$dispatcher->registerPlugin(new RewritePlugin());

    }
}
