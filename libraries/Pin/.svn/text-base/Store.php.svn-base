<?php
class Pin_Store {
    const MC_LIVETIME = 300;
    const CACHE_NAME = 'TIPS';
    private static $MC_KEY_PINCODE = '%s_1_%s';
    private static $MC_KEY_PINCODEKEY = '%s_2_%s';
    
    function __construct() {
        $this->CACHE_KEY_PINCODE = Comm_Util::conf('cache_key.pincode');
    }
    
    /**
     * 
     * 验证码生成的随机数写入mc
     * @param string $key 当前登录用户的uid
     * @param string $value 当成生成随机数
     */
    public function add_pincodekey_to_mc($key,$value) {
    	$mc_key = sprintf ( self::$MC_KEY_PINCODEKEY, $this->CACHE_KEY_PINCODE, $key);
        $re = Comm_Cache::pool (self::CACHE_NAME)->set($mc_key, $value, self::MC_LIVETIME);
        return $re;
    }
    
    /**
     * 
     * 通过key从mc中获取验证码的随机数
     * @param string $key
     */
    public function get_pincodekey_from_mc($key) {
    	$mc_key = sprintf ( self::$MC_KEY_PINCODEKEY, $this->CACHE_KEY_PINCODE, $key);
        $value = Comm_Cache::pool(self::CACHE_NAME)->get($mc_key, self::MC_LIVETIME);
        return $value;
    }
    
    /**
     * 添加验证码到缓存
     * @param string $key
     * @param string $code
     */
    public function add_pin($key, $code) {
        $mc_key = sprintf ( self::$MC_KEY_PINCODE, $this->CACHE_KEY_PINCODE, $key);
        $re = Comm_Cache::pool (self::CACHE_NAME)->set($mc_key, $code, self::MC_LIVETIME);
        return $re;
    }
    
    /**
     * 从缓存中获取验证码
     * @param string $key
     */
    public function get_pin($key) {
        $mc_key = sprintf ( self::$MC_KEY_PINCODE, $this->CACHE_KEY_PINCODE, $key);
        $code = Comm_Cache::pool(self::CACHE_NAME)->get($mc_key, self::MC_LIVETIME);
        return $code;
    }
    
    /**
     * 从缓存中删除验证码
     * @param string $key
     */
    public function del_pin($key) {
        $mc_key = sprintf ( self::$MC_KEY_PINCODE, $this->CACHE_KEY_PINCODE, $key);
        $re = Comm_Cache::pool(self::CACHE_NAME)->del($mc_key);
        return $re;
    }
    
}
?>
