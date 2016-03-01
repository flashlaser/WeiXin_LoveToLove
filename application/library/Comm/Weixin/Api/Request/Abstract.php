<?php
/**
 * 微信接口规范
 */
abstract class Comm_Weixin_Api_Request_Abstract {
    protected $http_request;
    /**
     * @var string 默认为false，由curl根据参数确定
     */
    protected $method;
    
    /**
     * $rules[实名] = array(
     * 'data_type' => 'int/int64/string/filepath/float',
     * 'where' => 'PARAM_IN_*',
     * 'is_required' => 'true/false',
     * 'final_value' => ''
     * );
     * @var array 存放参数规则 
     */
    protected $rules = array();
    
    /**
     * $rule_method[param_name] = array(
     *     method,
     * );
     * @var array 存放参数名对应的请求方式
     */
    protected $rule_method = array();
    
    /**
     * $alias[别名] = 实名
     * @var array 存放参数别名
     */
    protected $alias = array();
    
    /**
     * @var array 参数的值，key为actual_name
     */
    protected $values = array();
    
    /**
     * @var array 设置各种的回调
     */
    protected $callback = array();
    
    /*
     * @var string 接口返回值格式
     */
    protected $return_format = "json";
    
    /**
     * 设置接口警报值，超过此值，将写超时日志、默不记录
     * @var unknown_type
     */
    protected $warning_timeout = 0;
    
    /**
     * @var int 参数位置由接口的http method决定（在url或http body中）
     */
    const PARAM_IN_BY_METHOD = 0;
    
    /**
     * @var int 强行将参数放在url中
     */
    const PARAM_IN_GET = 1;
    
    /**
     * @var int 强行将参数放在http body中
     */
    const PARAM_IN_POST = 2;
    
    /**
     * 供 ##接口开发者## 设置URL和HTTP REQUEST METHOD
     * 
     * @param string $url
     * @param string $method
     */
    public function __construct($url, $method = false) {
        $this->http_request = new Comm_HttpRequest($url);
        
        $this->method = strtoupper($method);
        $this->http_request->set_method($method);
    }
    
    /**
     * 发送请求
     * curl错误在这里被处理
     * 正确的返回值由get_rst处理
     * 
     */
    protected function send() {
        $this->apply_rules();
        $this->run_callback("before_send");
        $send_rst = $this->http_request->send();
        
        $this->run_callback("after_send");
        if (!$send_rst) {
            throw new Exception($this->get_exception_msg());
        }
            
        //请求时间超过超时阀值，记录日志
        $warning_timeout = $this->get_warning_timeout() / 1000;
        $response_time = $this->http_request->get_response_time();
        if ($warning_timeout != 0 && $response_time >= $warning_timeout) {
        }
    }
    
    /**
     * 获取接口需要抛出的异常信息
     */
    protected function get_exception_msg() {
        $response_info = $this->http_request->get_response_info();
        $exp_msg = array();
        $exp_msg['current_location'] = Comm_Context::get_current_url(FALSE);
        $exp_msg['request_time'] = $response_info['total_time'];
        $exp_msg['url'] = $response_info['url'];
        $exp_msg['msg'] = $this->http_request->get_error_msg();
        return join(' ', $exp_msg);
    }
    
    protected function debug() {
        // 指定context变量名和预期值
        //超过0.2秒，写日志
        $is_write_log = false;
        if ($this->http_request->get_response_info('total_time') > 0.3) {
            $is_write_log = true;
        }
        // 根据条件判断
        if (!$is_write_log && !isset($_COOKIE['ooh']) && !isset($_GET['ooh'])){
            return;
        }
        
        $viewer = Comm_Context::get('viewer', false);
        if ($viewer === false || preg_match('#/ooh.*#Di', Comm_Context::get_server('REQUEST_URI'))) {
            return;
        }
        
        static $request_flag, $sequence_no;
        if (empty($request_flag)) {
            $request_flag = true;
            $sequence_no = 1;
            $msg = "[" . date('Y-m-d H:i:s') . "] " . Comm_Context::get_server('REQUEST_URI');
            $msg .= " (at ".Comm_Context::get_client_ip() . ' use '. Comm_ClientProber::get_client_agent('browser') . ")\n";
        } else {
            $sequence_no ++;
            $msg = "";
        }
        $msg .= "api: " . $this->http_request->url . "\n";
        $msg .= "sn: " . $sequence_no . "\n";
        $msg .= "php stack: " . $this->get_backtrace_info() . "\n";
        $msg .= "http code: " . $this->http_request->get_response_info('http_code') . "\n";
        $msg .= "used time: " . $this->http_request->get_response_info('total_time') . " s\n";
        $msg .= "request size: " . $this->http_request->get_response_info('request_size') . " byte\n";
        $msg .= "response size: " . $this->http_request->get_response_info('download_content_length') . " byte\n";
        $this->http_request->set_debug(TRUE);
        $msg .= $this->http_request->get_curl_cli();
        $msg .= "\n------\n";
        
        // 使用log formatter
        // rotate_log, by file size? by date
        $dir = Comm_Context::get_server('SINASRV_APPLOGS_DIR') . '/ooh';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($dir . '/' . $viewer->id . '_' . $viewer->domain, $msg, FILE_APPEND);
        
        //请求时间超过0.2秒的接口写入log
        if ($is_write_log) {
            $msg = date('Y-m-d H:i:s')."\n".$msg;
            file_put_contents($dir . '/high_elapse', $msg, FILE_APPEND);
        }
    }
    
    public function get_backtrace_info() {
        $trace = debug_backtrace();
        foreach ($trace as $item) {
            if (isset($item['file'])) {
                if (preg_match('#/www/classes/(.*)$#Di', $item['file'], $match)) {
                    $info[] = $match[1] . '@' . $item['line'];
                }
            }else{
                $info[] = $item['class'] . '@' . $item['function'];
            }
        }
        
        if (!empty($info)) {
            $info = array_reverse($info);
            return implode(', ', $info);
        } else {
            return '';
        }
    }
    
    /**
     * 获取正确的值
     * 
     */
    abstract public function get_rst();
    
    /**
     * 供 ##接口开发者## 设置接口规则
     * 
     * @param string $param_name
     * @param string $data_type
     * @param bool $is_required
     * @param int $where
     */
    public function add_rule($actual_name, $data_type, $is_required = FALSE, $where = 0) {
        $this->rules[$actual_name]['data_type'] = $data_type;
        $this->rules[$actual_name]['is_required'] = $is_required;
        $this->rules[$actual_name]['where'] = $where;
    }
    
    /**
     * 为参数添加特殊的请求
     * @param string $actual_name
     * @param string $method
     * @throws Comm_Exception_Program
     */
    public function add_rule_method($actual_name, $method) {
        $allow_methods = array('GET' => 0, 'POST' => 1, 'DELETE' => 2);
        if(!isset($allow_methods[$method])) {
            throw new Comm_Exception_Program("method for the param {$actual_name} error:  $method");
        }
        if($this->method != 'POST' && $method == 'POST') {
            $this->http_request->set_method('POST');
        }
        $this->rule_method[$actual_name] = $method;
    }
    
    /**
     * 供 ##接口开发者## 设置参数别名
     * 
     * @param unknown_type $param
     * @param unknown_type $alias
     */
    public function add_alias($actual_name, $alias) {
        $this->alias[$alias] = $actual_name;
    }
    
    /**
     * 供 ##接口开发者## 增加 ##设置单个参数时## 的callback
     * 回调方法示意：（第一个参数为按引用传递的$value）
     * public function func($value, $p1, $p2..., $pn)
     * 
     * @param string $name
     * @param array $callback
     */
    public function add_set_callback($actual_name, $obj, $method, $param = array()) {
        $this->callback['set'][$actual_name][] = array($obj, $method);
        $this->callback['set'][$actual_name][] = $param;
    }
    
    /**
     * 供 ##接口开发者## 增加 ##发送请求前## 的callback
     * 回调方法示意：（最后一个参数为当前$request）
     * public function func($p1, $p2..., $pn, $request)
     * 
     * @param object $name
     * @param string $callback
     * @param array $param
     */
    public function add_before_send_callback($obj, $method, $param = array()) {
        Comm_Assert::as_exception();
        Comm_Assert::false(isset($this->callback['before_send']), "don not add before send callback repeatly");
        $this->callback['before_send'][] = array($obj, $method);
        $this->callback['before_send'][] = $param;
    }
    
    /**
     * 供 ##接口开发者## 增加 ##发送请求后## 的callback
     * 回调方法示意：（最后一个参数为当前$request）
     * public function func($p1, $p2..., $pn, $request)
     * 
     * @param string $name
     * @param array $callback
     * @param array $param
     */
    public function add_after_send_callback($obj, $method, $param = array()) {
        Comm_Assert::as_exception();
        Comm_Assert::false(isset($this->callback['after_send']), "don not add after send callback repeatly");
        $this->callback['after_send'][] = array($obj, $method);
        $this->callback['after_send'][] = $param;
    }
    
    /**
     * 供 ##接口调用者## 设置参数
     * 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        Comm_Assert::as_exception();
        Comm_Assert::true($actual_name = $this->get_actual_name($name), "{$name} is not allowed");
        $this->values[$actual_name] = $this->run_callback('set', $actual_name, $value);
    }
    
    /**
     * 返回values数据
     * @param string $actual_name
     */
    public function __get($actual_name) {
        if (isset($this->values[$actual_name])) {
            return $this->values[$actual_name];
        }
        return NULL;
    }
    
    /**
     * 返回values数据
     */
    public function get_values() {
    	return $this->values;
    }
    
    /**
     * 返回http请求对象
     */
    public function get_http_request(){
    	return $this->http_request;
    }
    
    /**
     * 设定请求的超时时间
     * @param int $connect_timeout
     * @param int $time
     */
    public function set_request_timeout($connect_timeout, $time) {
        $this->http_request->connect_timeout = $connect_timeout;
        $this->http_request->timeout = $time;
    }  

    /**
     * 设置超时阀值
     * @param $time
     */
    public function set_warning_timeout($time) {
        $this->warning_timeout = $time;
    }
    
    /**
     * 获取超时阀值
     */
    public function get_warning_timeout() {
        return $this->warning_timeout;
    }
    
    /**
     * 发送正式请求前验证接口规则
     * 规则来自接口开发者的设定
     * 
     * @throws Comm_Exception_Program
     */
    protected function apply_rules() {
        if (empty($this->rules)) {
            return;
        }
        foreach ($this->rules as $actual_name => $rule) {
            if ($rule['is_required'] && !isset($this->values[$actual_name])) {
                throw new Comm_Exception_Program("param {$actual_name} is required");
            } elseif (!isset($this->values[$actual_name])) {
                continue;
            }
            
            $value = $this->values[$actual_name];
            switch ($rule['data_type']) {
                case "boolean" :
                    $value = ((boolean)$value) ? 'true' : 'false';
                    
                    break;
                case "int" :
                    $value = (int)$value;
                    break;
                case "string" :
                case "filepath" :
                case "date" :
                    $value = (string)$value;
                    break;
                case "float" :
                    $value = (float)$value;
                    break;
                case "int64" :
                    if (!Comm_Util::is_64bit()) {
                        //if (!is_string($value) && !is_float($value)) {/*throw?*/}
                        $value = (string)$value;
                    } else {
                        $value = (int)$value;
                    }
                    break;
                default :
                    throw new Comm_Exception_Program("invalid data type");
            }
            
            if(isset($this->rule_method[$actual_name])) {
                $method = $this->rule_method[$actual_name];
            } else {
                $method = $this->method;
            }
            if (($rule['where'] == self::PARAM_IN_BY_METHOD && $method === "GET") || $method === "DELETE" || $rule['where'] == self::PARAM_IN_GET) {
                $this->http_request->add_query_field($actual_name, $value);
            } else {
                if ($rule['data_type'] === 'filepath') {
                    $this->http_request->add_post_file($actual_name, $value);
                } else {
                    $this->http_request->add_post_field($actual_name, $value);
                }
            }
        }
    }
    
    /**
     * 检查参数是否在允许范围内
     * 
     * @param string $name
     */
    private function get_actual_name($name) {
        if (isset($this->rules[$name])) {
            return $name;
        }
        
        if (array_key_exists($name, $this->alias)) {
            return $this->alias[$name];
        }
        
        return false;
    }
    
    /**
     * 运行回调函数
     * 
     * @param string $phase
     * @param string $actual_name
     * @param mixed $value
     */
    private function run_callback($phase, $actual_name = '', $value = '') {
        //TODO callback
        if (!isset($this->callback[$phase])) {
            return $value;
        }
        $param = array();
        if ($phase == "set") {
            Comm_Assert::true($actual_name != '');
            if (isset($this->callback['set'][$actual_name])) {
                $callback = $this->callback['set'][$actual_name][0];
                $param = $this->callback['set'][$actual_name][1];
                $param = is_array($param) ? $param : array();
                array_unshift($param, $value);
                $value = call_user_func_array($callback, $param);
                return $value;
            } else {
                return $value;
            }
        } else {
            if (isset($this->callback[$phase])) {
                $callback = $this->callback[$phase][0];
                $param = $this->callback[$phase][1];
                $param[] = $this;
                call_user_func_array($callback, $param);
            }
        }
    }
}
