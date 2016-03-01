<?php
/**
 * openapi接口请求类
 */
class Comm_Weixin_Api_Request_Platform extends Comm_Weixin_Api_Request_Abstract {
    public static $platform_api_server_name = "http://i.api.weibo.com";

    public function __construct($url, $method = false) {
        parent::__construct($url, $method);
    }

    /**
     * 接口请求方法
     * @see Comm_Weibo_Api_Request_Abstract::get_rst()
     * @return 接口无异常时的正常返回值
     */
    public function get_rst($throw_exp = TRUE, $defaut = array()) {
        parent::send();
        $content = $this->http_request->get_response_content();

        $result = Comm_Util::json_decode($content, true);

        $exp_msg = $exp_code = FALSE;
        if ($this->http_request->get_response_info('http_code') != '200') {
            if (isset($result['error'])) {
                $exp_msg = $result['error'];
                $exp_code = $result['error_code'];
            } else {
                $exp_msg = "http error:" . $this->http_request->get_response_info('http_code');
                $exp_code = $this->http_request->get_response_info('http_code');
            }
        } elseif (!is_array($result)) {
            $exp_msg = "api return data can not be json_decode";
            $exp_code = -1;
        } elseif ((isset($result['error_code']) || isset($result['error'])) && !strpos($this->http_request->url, 'proxy/badges/badge')) {
            $exp_code = isset($result['error_code']) ? $result['error_code'] : -1;
            $exp_msg = isset($result['error']) ? $result['error'] : "api data is invalid";
        }
        if (FALSE !== $exp_code && FALSE !== $exp_msg) {
            if ($throw_exp == TRUE) {
                throw new Exception($exp_msg, $exp_code);
            } else {
                return $defaut;
            }
        }
        return $result;
    }


    /**
     * 生成接口url
     * @param string $resource
     * @param string $interface
     * @param string $format
     * @param string $version
     * @param bool $is_v4
     * @param $pre_query
     * @throws Comm_Exception_Program
     */
    public static function assemble_url($resource, $interface, $format = '', $pre_query = '') {
        if (empty($format)) {
            $format = self::$platform_api_default_format;
        }
		$url = self::$platform_api_server_name . '/' . $resource;
        if (!empty($interface)) {
            $url .= '/' . $interface;
        }
		if (!empty($format)) {
			$url .= '.' . $format;
		}
        $url .= '.' . $format;
        if (!empty($pre_query)) {
            $url .= "?$pre_query";
        }
        return $url;
    }

    /**
     * 用户名、密码方式访问时，设定相关信息
     * @param string $user
     * @param string $psw
     */
    public function add_userpsw() {
        $user = Tool_WeiboConf::get("env.unlogin_reg_user");
        $psw = Tool_WeiboConf::get("env.unlogin_reg_psw");
        $this->http_request->add_userpsw($user, $psw);
    }

    public function send_message_userpsw($username, $password) {
        $this->http_request->add_userpsw($username, $password);
    }
}
