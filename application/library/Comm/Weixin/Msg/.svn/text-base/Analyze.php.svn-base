<?php

include_once "wxBizMsgCrypt.php";
/**
 * 微信消息加密
 * @package comm
 */
class Comm_Weixin_Msg_Analyze {
    CONST EncodingAesKey = 'z8NdguC9IJ68o7JemFkbMgMzW6XAgOHlnIDCpogVMg7';

	/**
	 * 第三方向公众号平台发送消息，加密
	 */
	public function encode($text, $nonce){
		$pc = new WXBizMsgCrypt(TOKEN, self::EncodingAesKey, APPID);
		$encryptMsg = '';
		$errCode = $pc->encryptMsg($text, time(), $nonce, $encryptMsg);

		if ($errCode == 0) {
			return $encryptMsg;
		} else {
			return false;
		}

	}

	/**
	 * 第三方收到公众号平台发送的消息，解密
	 */
	public function decode($msgSignature, $timeStamp, $nonce, $from_xml){
		$msg = '';
		$pc = new WXBizMsgCrypt(TOKEN, self::EncodingAesKey, APPID);
		$errCode = $pc->decryptMsg($msgSignature, $timeStamp, $nonce, $from_xml, $msg);
		if ($errCode == 0) {
			return array('errCode' => $errCode, 'msg' => $msg);
		} else {
			return array('errCode' => $errCode);
		}
	}

	/**
	 * 将xml信息转换成数组
	 */
	public function xml_to_array($xml){
		$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		return json_decode(json_encode($xml, true),true);
	}

	///**
	// * 将加密后的信息转成xml
	// */
	//public function encryptmsg_to_xml($encryptMsg, $to_username){
	//	$xml_tree = new DOMDocument();
	//	$xml_tree->loadXML($encryptMsg);
	//	$array_e = $xml_tree->getElementsByTagName('Encrypt');
	//	$array_s = $xml_tree->getElementsByTagName('MsgSignature');
	//	$encrypt = $array_e->item(0)->nodeValue;
	//	$msg_sign = $array_s->item(0)->nodeValue;
	//
	//	$format = "<xml><ToUserName><![CDATA[%s]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
	//	$from_xml = sprintf($format, $to_username, $encrypt);
	//	return $from_xml;
	//}
}
