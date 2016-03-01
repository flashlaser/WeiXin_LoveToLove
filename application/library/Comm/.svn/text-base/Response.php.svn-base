<?php
/**
 * 输出结构
 * @package comm
 */
abstract class Comm_Response {
    //响应体类型（JSON）

    const TYPE_JSON = 'json';
    
    //响应体类型（JS）
    const TYPE_JS = 'js';

    /**
     * 输出响应类型
     * @param type $type
     */
    public static function contentType($type) {
        if (headers_sent()) {
            return false;
        }
        switch ($type) {
            case 'json' :
                header('Content-type: application/json; charset=utf-8');
                break;
            case 'html' :
                header('Content-type: text/html; charset=utf-8');
                break;
            case 'js' :
                header('text/javascript; charset=utf-8');
                break;
            case 'jpg' :
                header('Content-Type: image/jpeg');
                break;
        }
        return true;
    }

    /**
     * 输出一段JSON
     * @param type $code
     * @param type $msg
     * @param type $data
     * @param type $return
     * @return boolean
     */
    public static function json($code, $msg, $data = null) {
		if(self::contentType(self::TYPE_JSON)){
			$result = json_encode(array('code' => $code, 'msg' => $msg, 'data' => $data));
			echo $result;
			exit();
		}
    }
}
