<?php
/**
 * Ajax Controller抽象
 *
 * @package abstract
 * @author  Chengxuan <chengxuan@staff.sina.com.cn>
 */
class Abstract_Controller_Aj extends Abstract_Controller_Default {
    //Action路径

    const ACTION_DIR = 'actions/Aj/';

    //禁用的Referer
    protected $deny_referer = array(
        /* 由于会影响搜索接口，先屏蔽掉
          '<',
          '>',
          'document\. ',
          '(.)?([a-zA-Z]+)?(Element)+(.*)?(\()+(.)*(\))+',
          '(<script)+[\s]?(.)*(>)+',
          'src[\s]?(=)+(.)*(>)+',
          '[\s]+on[a-zA-Z]+[\s]?(=)+(.)*',
          'new[\s]+XMLHttp[a-zA-Z]+',
          '\@import[\s]+(\")?(\')?(http\:\/\/)?(url)?(\()?(javascript:)?',
         */
    );
    //是否必需是AJAX
    protected $check_ajax = true;

    //初始化
    public function init() {
        try{
            parent::init();
        }catch (Exception_Nologin $e){
            $this->result('D00001', '您尚未登录，请先登录');
            $this->getResponse()->response();
            exit();
        }
        //禁止自动渲染模板
        Yaf_Dispatcher::getInstance()->autoRender(false)->disableView();

        //检查AJAX
        $request = $this->getRequest();
//         if ($this->check_ajax && !Helper_Debug::isDebug()) {
//             if (!$request->isXmlHttpRequest()) {
//                 throw new Exception_Msg(303403);
//             }
//         }
        
        //检查Referer
        $referer = $request->getServer('HTTP_REFERER');
        $referer = urldecode($referer);
        if ($referer) {
            //检查Referer是否是本站的
            $urlInfo      = parse_url($referer);
            $allowReferer = array($_SERVER['HTTP_HOST'], 'js.t.sinajs.cn', 'tjs.sjs.sinajs.cn', 'js.wcdn.cn', 'login.sina.com.cn', $_SERVER['SERVER_NAME'], 'huati.weibo.com','weibo.com');
	        if (!in_array($urlInfo['host'], $allowReferer) && !in_array($urlInfo['host'].':'.$_SERVER['SERVER_PORT'], $allowReferer)) {
                throw new Exception_Msg(303403);
            }

	        //检查Referer合法性
            foreach ($this->deny_referer as $reg) {
                $ref = urldecode($referer);
                if (preg_match('/' . $reg . '/', $ref)) {
                    throw new Exception_Msg(303403);
                }
            }
        }else{
            throw new Exception_Msg(303403);
        }
    }

    /**
     * 输出结果
     * @param int $code
     * @param string $msg
     * @param mixed $data
     */
    public function result($code, $msg='', $data = null) {
        Comm_Response::contentType(Comm_Response::TYPE_JSON);
        $this->getResponse()->setBody(Comm_Response::json($code, $msg, $data));
    }

    /*by zongwen
     * */
    public function result_ex($code,$msg,$data,$ex)
    {
        $ex['is_success'] = 0;
        if ($code == '100000') {
            $ex['is_success'] = 1;
        }
        Comm_Log_Scribe_Poll::write($ex);
        $this->result($code,$msg,$data);
    }
    
    /**
     * 输出JSONP结果
	 * @param int $code
	 * @param string $msg
	 * @param mixed $data
     */
    public function jsonp($code, $msg, $data = null) {
        Comm_Response::contentType(Comm_Response::TYPE_JSON);    //避免gzip压缩造成IE6解析出错
        $this->getResponse()->setBody(Comm_Response::jsonp($code, $msg, $data));
    }
    
    public function json_v3($code, $msg='', $data = null) {
        Comm_Response::contentType(Comm_Response::TYPE_JSON);
        $this->getResponse()->setBody(Comm_Response::json_v3($code, $msg, $data));
    }
    
    public function pageResult($code, $msg='', $data = null) {
        Comm_Response::contentType('html');
        $ret = Comm_Response::json($code, $msg, $data);
        $ret = '<html><head><script type="text/javascript">if (window.parent) {try {window.parent.uploadFileComplete('.$ret.');}catch(e){}}</script></head><body></body></html>';
        $this->getResponse()->setBody($ret);
    }

}
