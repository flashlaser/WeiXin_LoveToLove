<?php
/**
 * Cli接口Controller抽象
 *
 * @package abstract
 * @author  Chengxuan <chengxuan@staff.sina.com.cn>
 */
class Abstract_Controller_Cli extends Yaf_Controller_Abstract {
    //Action路径
    const ACTION_DIR = 'modules/Cli/actions/';

    const CACHE_CONF = 'SINASRV_MEMCACHED_VOTE_SERVERS';

	static protected $service_db = NUlL;

    /**
     * 初始化
     *
     * @throws Exception_System
     */
    public function init() {
        $q = $this->getRequest();

        //必需要Cli中执行
        if (!$q->isCli()) {
            if (DEVELOP_MBLOG != 1 && DEVELOP_MBLOG != 2 && !isset($_GET['cli'])) {
                throw new Exception_System(200302, '非法入口访问cli程序', array('ip'  => Comm_Util::getClientIp(), 'uri' => $_SERVER['REQUEST_URI']));
            }
        }

        //action 处理
        $action_name = $q->getActionName();
        if (!method_exists($this, $action_name.'Action')) {
            $ctrl_name = $q->getControllerName();
            $this->actions = array(
                $action_name  => self::ACTION_DIR . ucfirst($ctrl_name).'/'.  ucfirst($action_name).'.php'
            );
        }

        //禁止自动渲染模板
        $dispatcher = Yaf_Dispatcher::getInstance();
        $dispatcher->autoRender(false);
        $dispatcher->disableView();
    }

    /**
     * 获取当前服务器IP
     * @return string
     */
    public function server_ip() {
        $str = "/sbin/ifconfig | grep 'inet addr' | awk '{ print $2 }' | awk -F ':' '{ print $2}' | head -1";
        $ip  = exec($str);
        return $ip;
    }
    
    /**
     * 获取服务器名称
     * 
     * @return string
     *
     * @author chengxuan
     */
    public function hostname() {
        if(isset($_SERVER['HOSTNAME'])) {
            $hostname = $_SERVER['HOSTNAME'];
        } else {
            $hostname = 'IP:' . $this->server_ip();
        }
        
        return $hostname;
    }
    
    /**
     * 检查是否是否该停了
     * 
     * @param string $type   类别crontab/mcq
     * @param string $action 方法名称
     * @param int    $idx    线程号
     * 
     * @return mixed false|string
     *
     */
    public function checkStop($type, $action, $idx,$serial = null) {
		$cache_conf= 'cron_delay';
		$mc = Comm_Mc::init();
		empty(self::$service_db) && self::$service_db= new Data_Poll_Service();
        $hostname = $this->hostname();
		$key = 'ss'.'_'.$type.'_'.$action.'_'.$idx.'_'.$hostname;
		if(!empty($serial)){
			$key = $key.'_'.$serial;
		}
		$result = $mc->getData($cache_conf, array($key));
		if(empty($result)){
			$result = self::$service_db->getValue($key);
			if(empty($result) || $result == 'reboot'){
				$mc->setData($cache_conf, array($key), 'yes');
				if($result == 'reboot'){
					self::$service_db->unsetKey($key);
					return 'stop';
				}
			}else{
				$mc->setData($cache_conf, array($key), 'no');
				return 'stop';
			}
		}else{
			if($result == 'no'){
				return 'stop';
			}
		}
		$key = 'ss'.'_'.$type.'_'.$action;
		$result = $mc->getData($cache_conf, array($key));
		if(empty($result)){
			$result = self::$service_db->getValue($key);
			if(empty($result)){
				$mc->setData($cache_conf, array($key), 'yes');
			}else{
				$mc->setData($cache_conf, array($key), 'no');
				return 'stop';
			}
		}else{
			if($result == 'no'){
				return 'stop';
			}
		}
		return false;
    }
    
    /**
     * 输出内容并记录日志(日志只记录500行，每执行500次检测一次文件大小，自动换行)
     * 
     * @param string $text
     * 
     * @return void
     *
     * @author chengxuan
     */
    public function output($text) {
        static $i = 0;
        if($i % 500 === 0) {
            //检查日志是否超过大小，如果超过，删除之前的内容
            $i = 0;
        }
        ++$i;
        
        //写入日志并输出
        $text .= "\n";
        echo $text;
        
        $r = $this->getRequest();
        $proc_num = Comm_Argchecker::int($r->getParam('proc_num'), 'min,1', 2, 2, 'x');
        $filename = sprintf('%s/%s/%s/%s_%s.log', $_SERVER['SINASRV_APPLOGS_DIR'], $r->getModuleName(), $r->getControllerName(), $r->getActionName(), $proc_num);
        $filename_dir = dirname($filename);
        if(!is_dir($filename_dir)) {
            mkdir($filename_dir, 0775, true);
        }
        $this->checkLogMaxLine($filename, 500);
        file_put_contents($filename, $text, FILE_APPEND);
    }
    
    /**
     * 类型于printf方式输出并记录日志
     * 
     * @return void
     *
     * @author chengxuan
     */
    public function printf() {
        $args_array = func_get_args();
        $text = call_user_func_array('sprintf', $args_array);
        $this->output($text);
    }
    
    /**
     * 检查日志文件的最大行数，如果超过，削减（可能不会完全按照max_line来削）
     * 
     * @param string $filename 文件名称
     * @param int    $max_line 最大行数
     * 
     * @return void
     *
     * @author chengxuan
     */
    public function checkLogMaxLine($filename, $max_line) {
        if(!is_file($filename)) {
            return false;
        }
        
        $filesize = filesize($filename);
        $length = 1024;
        $fp = fopen($filename, 'r');
        
        $position = $filesize;    //指针位置
        $lf_num = 0;              //换行数
        
        $content = '';
        do {
            $position = max(0, $position - $length);
            fseek($fp, $position);
            $current_content = fread($fp, $length);
            $content = $current_content .  $content;
            $lf_num += substr_count($current_content, "\n");
        } while($position > 0 && $lf_num <= $max_line);
        fclose($fp);
        
        //超过行数，截取完整数据
        if($lf_num > $max_line) {
            $content = ltrim(strstr($content, "\n"), "\n");
            file_put_contents($filename, $content);
            return true;
        } else {
            return false;
        }
    }
}
