<?php
/**
 * 无线gsid登陆
 * @author @qie copy from club.weibo.cn
 * 2013/11/27
 *
 */
class WapSso {
	// 根据gsid获取uid的 平台接口地址
	//private $_gsid_url = "http://i.api.weibo.cn/interface/f/login/getUidFromGsid.php?gsid=%s";
	//无线朱陶提供的 根据gsid获取uid的方法
	private $_gsid_url = "http://i.api.weibo.cn/interface/i/ttt/login/cookieToUid.php?gsid=%s";
	// 登陆后,cookie保存2天时间
	private $_cookie_time = 172800;
	// 私钥
	private $_priv_key = "X3*&$#cl_ub&wap322102";
	// 10分钟内有动作算活动
	private $_live_time = 600;
	
	private $_uid = 0;
	
	public function __construct() {
		//如果是客户端过来的请求，将对应的GET参数种cookie
		//永久cookie
		//参数gsid=>_gsid_CTandWM   用户登陆信息缓存
		//参数ua=>HTTP_X_USER_AGENT  客户端
		//参数from=>vip.weibo.cn/member/([a-z]*)/*  => ([a-z])*_from
		//参数skin=?vip.weibo.cn/member/([a-z]*)/*  => ([a-z])*_skin
		//参数from=>种from
		//??  => 种_TTT_USER_CONFIG_H5
		
		//参数wm=>种session: WEIBOCN_WM
		//print_r($_SERVER);
		if( isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])){
			if( strpos( $_SERVER['HTTP_HOST'],"weibo.cn")){
				//种30天的永久缓存
				$cookie_expires = time()+2592000;
				//无线的域名访问，开始处理，否则不处理
				//种gsid永久cookie
				//Andrior客户端跳转到我们页面的时候  已经在cookie中种上了   $_COOKIE['gsid_CTandwm'] 
				if( false != Comm_Context::param ( 'gsid', false ,true) ){
                    if( Comm_Context::param ( 'gsid', false ,true) != $_COOKIE['gsid_CTandWM'] ){
                    	//首先删除weibo.cn根域的cookie
                    	$expires = time () - 3600;
                    	setcookie ( 'gsid_CTandWM', "", $expires, "/", "weibo.cn" );
                    	//然后将gsid种到weibo.cn根域
						self::_setDomainSidToken('gsid_CTandWM', Comm_Context::param ( 'gsid', false ,true), $cookie_expires);
					}
					//如果get参数用带有gsid，则将gsid参数去掉gsid
					//HTTP_HOST:vip.weibo.cn
					//SCRIPT_URL:/members/cover/wantuse
					//QUERY_STRING:type=shop&coordinates=crop.0.0.640.640.640.640&pid=6ce2240djw1e9ob6msd5mj20hs0hs79h&gsid=4uLT08543s6OjbgyTVufVdBEw2n
					if( true == self::check_gsid_get_cookie()){
						$query_string = (isset( $_SERVER['QUERY_STRING'] ) && ($_SERVER["REQUEST_METHOD"] == "GET"))? $_SERVER['QUERY_STRING'] : "";
						if($query_string != "" ){
							if( preg_match("/gsid=[a-zA-Z0-9]*/", $query_string, $matches)){
								if( isset( $matches[0]) ){
									$tmp = explode("=", $matches[0]);
									if( ($tmp[1] == $_COOKIE['gsid_CTandWM'] ) || ( $tmp[1] == Comm_Context::param ( 'gsid', false ,true )) ){
										//匹配到gsid,且gsid已经种入到cookie,则替换该gsid参数
										$query_string = preg_replace("/gsid=[a-zA-Z0-9]*/", "", $query_string);
										header("Location:http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_URL']."?".$query_string);
										exit;
									}
								}
							}
						}
					}
				}
				//种ua永久cookie
				//$_SERVER['HTTP_X_USER_AGENT'] 是是否来源于客户端的标识
				//andrior 的访问首先访问weibo.cn/sinaurl 会种cookie:$_COOKIE['HTTP_X_USER_AGENT']=samsung-SM-G7108__weibo__4.2.0_build2__android__android4.3
				//andrior:sinaurl:get参数ua:samsung-SM-G7108__weibo__4.2.0_build2__android__android4.3,后种HTTP_X_USER_AGENTcookie
				
				//ios 
				if( (false != Comm_Context::param ( 'ua', false ,true)) || isset($_SERVER['HTTP_X_USER_AGENT']) ){
					//优先选择get中的ua,因为之前旧版本浏览器header中是没有HTTP_X_USER_AGENT的
					$ua = (false != Comm_Context::param ( 'ua', false ,true)) ? Comm_Context::param ( 'ua', false ,true) :$_SERVER['HTTP_X_USER_AGENT'];
					//参数
					if( $ua != $_COOKIE['HTTP_X_USER_AGENT'] ){
						//种30天
						self::_setDomainSidToken('HTTP_X_USER_AGENT', $ua, $cookie_expires);
					}
					$ua = NULL;
				}
				//种ua永久cookie
				if( false != Comm_Context::param ( 'from', false ,true)){
					if( Comm_Context::param ( 'from', false ,true) != $_COOKIE['from']){
						//种30天
						self::_setDomainSidToken('from', Comm_Context::param ( 'from', false ,true), $cookie_expires);
					}
				}
				
				//种含有访问文件的cookie
				$script_url = $_SERVER['PHP_SELF'];
				$tmp = explode("/", $script_url);
				if( $tmp[2] == "members"){
					if( false != Comm_Context::param ( 'from', false ,true)){
						$fromname = "_".$tmp[3]."_from";
						self::_setDomainSidToken($fromname, Comm_Context::param ( 'from', false ,true), $cookie_expires);
					}
					if( false != Comm_Context::param ( 'skin', false ,true)){
						$skinname = "_".$tmp[3]."_skin";
						self::_setDomainSidToken($skinname, Comm_Context::param ( 'skin', false ,true), $cookie_expires);
					}
				}
				$tmp = NULL;
				$fromname = NULL;
				$skinname = NULL;
				$script_url = NULL;

				//分析无线原服务中有种wm session【临时】，但是大门未找到触发种的地方，
                //页面从sinaurl跳过来时已经种好了WEIBOCN_WM 的cookie,因此我们暂不处理
                /*
				if( false != Comm_Context::param ( 'wm', false ,true)){
                    // session start
                    session_start(); // 开始一个会话，如果要使用session程序最前面一定要加上这句
                    $_SESSION['WEIBOCN_WM'] = Comm_Context::param ( 'wm', false ,true);//给一个session 变量赋值，如果该变量不存在即创建
				}
                */
			}
		}else{
            //不是我们域名下的访问，跳转到登陆页
            Tool_Redirect::toWeiboHome();
        }
	}
	
	/**
	 * 判定当前用户是否登陆
	 * 
	 * @param bool $is_jump
	 * @return boolean
	 */
	public function isLogin($is_jump = true) {
		if (! $this->_isLogin ()) {
			if ($is_jump) {
				//$is_jump=true表示，如果未登陆，则需要跳转到登陆页,执行跳转
				$this->_goToLogin ();
			}
			return false;
		} else {
			// 登陆状态
			return true;
		}
	}
	
	/**
	 * 获取用户uid
	 * @return bingint 
	 */
	public function getCookieUid() {
		if ($this->_uid) {
			return $this->_uid;
		}else{
			if(self::isLogin()){
				return $this->_uid;
			}
			return false;
		}
	}
	
	/**
	 * 强制跳转
	 */
	private function _goToLogin() {
		/*
		 * backURL 登录成功返回地址，需要urlencode backTitle 登录页面返回链接标题，GBK编码，需要urlencode
		 * ns 是否需要接受手机新浪网SESSION参数，1为不接受，默认为0(接受)
		 * revalid 是否强制手动登录，1为不使用gsid自动登录，2为不使用gsid和网关信息自动登录，默认为0(支持自动登录)
		 */
		$request_uri = isset ( $_SERVER ["REQUEST_URI"] ) ? $_SERVER ["REQUEST_URI"] : "";
		$call_back = "http://page.vote.weibo.cn" . $request_uri;
        //$url = "http://3g.sina.com.cn/prog/wapsite/sso/login.php?backURL=%s&backTitle=%s&ns=0&revalid=0";
        //未登陆的跳转地址换成weibo.cn的，不用sina.cn的
        if ((Comm_Context::param('ftpl',"") == "m" ) || ("h5" == Comm_Wap_Getuainfo::getuatype ())) {
            //h5的用http://m.weibo.cn/login?ns=1&backURL=http%3A%2F%2Fvip.weibo.cn%2F&backTitle=%D0%C2%C0%CB%CE%A2%B2%A9&vt=4&
            $url = "http://m.weibo.cn/login?ns=1&backURL=%s&backTitle=%s&vt=4";
        }else{
            //wap的用login.weibo.cn
            $url = "http://login.weibo.cn/login/?ns=1&revalid=2&backURL=%s&backTitle=%s";
        }
		$url = sprintf ( $url, urlencode ( $call_back ), urlencode ( iconv ( "UTF-8", "GBK", "微博会员" ) ) );
		header ( "Location:" . $url );
		exit ();
	}
	
	/**
	 * 从weibo.cn域 cookie中获取用户验证用户是否登陆的gsid
	 * @return boolean|unknown
	 */
	private function getgsid() {
		if (! isset ( $_COOKIE ['gsid_CTandWM'] ) && (false == Comm_Context::param ( 'gsid', false ,true))) {
			//未登陆
			return false;
		}else{
			//客户端现在的逻辑是： 只要get中有gsid就优先按照她算，不管这个gsid是否有效；只有当get参数中没有的时候才取本地cookie
			$cookie_gsid = (false != Comm_Context::param ( 'gsid', false ,true))?Comm_Context::param ( 'gsid', false ,true):$_COOKIE ['gsid_CTandWM'];
			return $cookie_gsid;
		}
		return false;
	}
	
	/**
	 * 对比从get参数中传入的gsid与cookie中的gsid是否相等
	 * @return boolean
	 */
	public function check_gsid_get_cookie(){
		$cookiegsid  = isset($_COOKIE['gsid_CTandWM'])?$_COOKIE['gsid_CTandWM']:false;
		$getgsid = Comm_Context::param ( 'gsid', false ,true);
		if( ($getgsid == false) || ($cookiegsid == false)){
			return false;
		}elseif( $getgsid == $cookiegsid){
			return true;
		}else{
			//get
			$getkey = Dr_Mem::get_key_new ( 'GSID_COOKIEUID', array ($getgsid ) );
			$mc_val = Dr_Mem::get ( $getkey );
			if( false != $mc_val){
				$getuid = unserialize($mc_val);
			}else{
				$getuid = self::_getUidFromGsid($getgsid);
				//从接口获取的值种缓存
				$time = Dr_Mem::get_key_time ( 'GSID_COOKIEUID', 'DEFAULT_EXPIRE' );
				$re = Dr_Mem::set ( $getkey, serialize ($getuid), $time );
			}
			//cookie
			$cookiekey = Dr_Mem::get_key_new ( 'GSID_COOKIEUID', array ($cookiegsid ) );
			$mc_val_cookie = Dr_Mem::get ( $cookiekey );
			if( false != $mc_val_cookie){
				$cookieuid = unserialize($mc_val_cookie);
			}else{
				$cookieuid = self::_getUidFromGsid($cookiegsid);
				//从接口获取的值种缓存
				$time = Dr_Mem::get_key_time ( 'GSID_COOKIEUID', 'DEFAULT_EXPIRE' );
				$re = Dr_Mem::set ( $cookiekey, serialize ($cookieuid), $time );
			}
			
			if(($getuid == $cookieuid) && ($getuid!=false)){
				//两者在缓存中有，且不为0，且一致，则代表两者一致
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 检查用户是否为登陆状态
	 * @return boolean
	 */
	private function _isLogin() {
		$cookie_gsid = self::getgsid();
		if (! $cookie_gsid) {
			//未登陆
			return false;
		}else{
			//增加会员本地缓存，避免频繁调用无线接口
			$key = Dr_Mem::get_key_new ( 'GSID_COOKIEUID', array ($cookie_gsid ) );
			$mc_val = Dr_Mem::get ( $key );
			if( false != $mc_val){
				$uid = unserialize($mc_val);
			}else{
				$uid = 0;
			}
			$uid = 0;
			if( !$uid || !is_numeric($uid) || $uid <=0 ){
				$uid = $this->_getUidFromGsid ( $cookie_gsid );
				if (! $uid) {
					// 验证不通过，未登陆状态
					return false;
				}else{
					//从接口获取的值种缓存
					$time = Dr_Mem::get_key_time ( 'GSID_COOKIEUID', 'DEFAULT_EXPIRE' );
					$re = Dr_Mem::set ( $key, serialize ($uid), $time );
				}
			}
			// 首次登陆，拿到uid
			$this->_uid = $uid;
			return true;
		}
		return false;
	}
	
	/**
	 * 生成key
	 * @param bingint $uid
	 * @param int $timestamp
	 * @param unknown_type $priv_key
	 * @param unknown_type $gsid
	 * @return string
	 */
	private function _createKey($uid, $timestamp, $priv_key, $gsid) {
		return md5 ( $uid . "_" . $timestamp . "_" . $priv_key . "_" . $gsid );
	}
	
	/**
	 * 通过gsid值获取用户uid
	 *
	 * @param unknown_type $gsid        	
	 * @return boolean string
	 */
	private function _getUidFromGsid($gsid) {
		$url = sprintf ( $this->_gsid_url, $gsid );
		$re = "";
		if (! $this->_CurlByGet ( $url, $re, 3 )) {
			return false;
		}
		// 检测返回值是否是合法的uid
		if (! is_numeric ( $re ) || strlen ( $re ) < 5) {
			return false;
		}
		// 返回用户信息
		return $re;
	}
	
	/**
	 * 通过get方式调用接口【通过gsid值获取用户uid 接口专用】
	 *
	 * @param unknown_type $url        	
	 * @param unknown_type $re        	
	 * @param unknown_type $timeout        	
	 * @return boolean
	 */
	private function _CurlByGet($url, &$re, $timeout = 1) {
		$re = false;
		$retry = 1;
		try {
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
			for($i = 1; $i <= $retry; $i ++) {
				if ($re !== false) {
					break;
				}
				$re = curl_exec ( $ch );
                //PageTrace  -Begin
                $pagetrace = Yaf_Registry::get("pagetrace");
                if($pagetrace && $pagetrace->Is_Running()){
                    $config = $pagetrace->getConfig();
                    if(!in_array($this->url,$config['BLOCK_URLS'])){ //过滤黑名单
                        $response_content = substr($re,strpos($re,"\r\n\r\n") +4,strlen($re) - strpos($re,"\r\n\r\n")-4);
                        $row_limit = 120;
                        if(strlen($response_content) > $row_limit){
                            $response_content = substr($response_content,0,$row_limit) . "...";
                        }

                        $curl = $this->curl_cli;
                        if(strlen($curl) > $row_limit){
                            $curl = substr($curl,0,$row_limit). "...";
                        }
                        $pagetrace->trace("API",vsprintf("<BR/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[blue]Request:&nbsp;[/blue]%s,<BR/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[blue]response:[/blue]%s,<BR/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[blue]CLI:[/blue]%s",array($this->url,$response_content,$curl)));
                    }
                }



                //PageTrace  - End
				if (is_string ( $re ) && strlen ( $re )) {
					curl_close ( $ch );
					$return = 'info';
				} else {
					if ($i == $retry) {
						$curl_error = curl_error ( $ch );
						curl_close ( $ch );
					}
				}
				
				if ($return == 'info') {
					return true;
				}
			}
		} catch ( Exception $e ) {
			return false;
		}
		return false;
	}
	
	/**
	 * 创建登陆状态cookie[子项目域下]
	 *
	 * @param unknown_type $uid        	
	 * @param unknown_type $gsid        	
	 * @return boolean
	 */
	private function _createLoginCookie($uid, $gsid) {
		if (! $uid) {
			return false;
		}
		$timestamp = time ();
		$key = $this->_createKey ( $uid, $timestamp, $this->_priv_key, $gsid );
		$expires = $timestamp + $this->_cookie_time;
		$this->_setCookieFunc ( 'cb_uid', $uid, $expires );
		$this->_setCookieFunc ( 'cb_t', $timestamp, $expires );
		$this->_setCookieFunc ( 'cb_k', $key, $expires );
		$this->_setCookieFunc ( 'gsid_CTandWM', $gsid, $expires );
		$this->_uid = $uid; // 首次登陆，拿到uid
		return true;
	}
	
	/**
	 * 退出，销毁子项目域下 cookie
	 *
	 * @return boolean
	 */
	private function _destroyCookie() {
		$expires = time () - 3600;
		$this->_setCookieFunc ( 'cb_uid', '', $expires );
		$this->_setCookieFunc ( 'cb_t', '', $expires );
		$this->_setCookieFunc ( 'cb_k', '', $expires );
		$this->_setCookieFunc ( 'gsid_CTandWM', '', $expires );
		return true;
	}
	
	/**
	 * 销毁指定cookie
	 * @param unknown_type $name
	 * @return boolean
	 */
	private function destroyCookie($name) {
		$expires = time () - 3600;
		$this->_setCookieFunc ( $name, '', $expires );
		return true;
	}
	/**
	 * 在子项目域下种cookie
	 *
	 * @param unknown_type $name        	
	 * @param unknown_type $value        	
	 * @param unknown_type $endtime        	
	 * @return boolean
	 */
	private function _setCookieFunc($name, $value, $endtime) {
		// return true;
		return setcookie ( $name, $value, $endtime, "/", "vip.weibo.cn", false );
	}
	
	/**
	 * 在weibo.cn域名下种cookie
	 *
	 * @param unknown_type $name        	
	 * @param unknown_type $value        	
	 * @param unknown_type $endtime        	
	 * @return boolean
	 */
	public function _setDomainSidToken($name, $value, $endtime) {
		return setcookie ( $name, $value, $endtime, "/", "weibo.cn", false );
	}
	
	public function _setDomainSessioncookie($name, $value) {
		return setcookie ( $name, $value, "", "/", "weibo.cn", false );
	}
}
?>