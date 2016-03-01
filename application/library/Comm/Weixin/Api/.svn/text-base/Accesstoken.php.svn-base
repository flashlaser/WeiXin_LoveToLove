<?php
/**
 * 微信接口，access_token
 */
class Comm_Weixin_Api_Accesstoken {
    const RESOURCE = "trends";
    /**
     * 获取access_token
     * 
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function get_access_token () {
        $url      = Comm_Weixin_Api_Request_Platform::assemble_url(self::RESOURCE, null);
        $platform = new Comm_Weixin_Api_Request_Platform($url, "GET");
        $platform->add_rule("uid", "int64", true);
        $platform->add_rule("has_num", "int64", false);
        $platform->support_pagination();
        return $platform;
    }

	/**
	 * 例子
	 */
	public static function destroy_batch(){
		$url = Comm_Weibo_Api_Request_Platform::assemble_url(self::RESOURCE, null);
		$platform = new Comm_Weibo_Api_Request_Platform($url,"POST");
		$platform->add_rule("ids", "string",true);
		$platform->set_request_timeout(5000, 5000);
		return $platform;
	}
}