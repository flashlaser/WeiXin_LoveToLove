<?php
/**
 * 对页面请求controller层抽象
 *
 * @package abstract
 * @author  Chengxuan <chengxuan@staff.sina.com.cn>
 */
class Abstract_Controller_Default extends Yaf_Controller_Abstract {
    
    //Action路径
    const ACTION_DIR = 'actions/';

    protected $allow_no_login = false;

    /**
     * X-Frame-Options 是否允许放到iframe下(慎用)
     */
    protected $x_frame_options = false;

	protected $x_frame_options_all = false;
    
    //当前用户信息
    public $user = array();

    /**
     * 公共入口
     *
     * @return void
     */
    public function init() {
        //header('Cache-Control: no-cache, must-revalidate');
        //header('Expires: ' . gmdate(DATE_RFC822, time() - 3600));
        //if($this->x_frame_options){
	     //   if(!$this->x_frame_options_all){
		 //       header('X-Frame-Options:weibo.com');
	     //   }
        //}else{
        //    header('X-Frame-Options:Deny');
        //}
        //
        ////Comm_Response::contentType('html');
		//
		//$user = UserModel::currentUser();
        ////获取用户的微博信息
        //if (!empty($user)) {
        //    Yaf_Registry::set('current_uid', $user['uid']);
        //    $this->user = $user;
			//Comm_Context::set('viewer', $user);
        //} else if (!$this->allow_no_login) {
			//throw new Exception('user no login');
        //}
    }


}
