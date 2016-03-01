<?php
/**
 * Internal Controller抽象
 *
 * @package abstract
 */
class Abstract_Controller_Internal extends Yaf_Controller_Abstract {
    //Action路径
    const ACTION_DIR = 'modules/Internal/actions/';
    //是否检查ip
    public $no_ip_check = false;
    //当前用户UID
    public $uid;
    
    /**
     * 可以不用传cip
     * @var bool
     */
    protected $allow_no_cip = false;

    //“构造方法” 检查权限
    public function init() {
        //禁止自动渲染模板
        $dispatcher = Yaf_Dispatcher::getInstance();
        $dispatcher->autoRender(false);
        $dispatcher->disableView();
    }
}
