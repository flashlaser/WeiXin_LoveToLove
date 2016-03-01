<?php
/**
 * 接收用户消息
 */
set_time_limit(5);
error_reporting(0);
class GetmsgController extends Abstract_Controller_Internal {

	const REPLY_XML_FORMAT = '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%u</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content><FuncFlag>0</FuncFlag></xml>';
	const LOVE_INFO_VALID_TIME = 2592000; //love信息有效时间

	public function indexAction(){
		//接收传送的数据
		$msg_signature = Comm_Context::param('msg_signature', '');
		$timestamp = Comm_Context::param('timestamp', '');
		$nonce = Comm_Context::param('nonce', '');
		$xml_rst = file_get_contents("php://input");
		//解密
		$msg_obj = new Comm_Weixin_Msg_Analyze();
		$decode_rst = $msg_obj->decode($msg_signature, $timestamp, $nonce, $xml_rst);
		if($decode_rst['errCode'] != 0){
			echo '解密错误';exit;
		}
		//解析解密后的xml
		$msg_array = $msg_obj->xml_to_array($decode_rst['msg']);
		//判断消息内容,进行相应回复
		$text = trim($msg_array['Content']);
		$openid = $msg_array['FromUserName'];

		try{
			$love_info = Dr_Getlove::get_love_info($openid);
			$reply_text = self::love_progress($openid, $love_info, $text);
		}catch(Exception $e){
			$reply_text = '(⊙o⊙)...系统出错了...(⊙o⊙)';
		}

		$reply_xml = sprintf(self::REPLY_XML_FORMAT, $msg_array['FromUserName'], APPNAME, time(), $reply_text);
		echo $msg_obj->encode($reply_xml, $nonce);

	}


	private static function love_progress($openid, array $love_info, $text){
		$text = trim($text);
		$progress = $love_info['progress'];

		if($text == ''){ //新用户或重新关注
			if($progress <= 2){ //未确认过自己微信号
				if (Dw_Savelove::start_new_user($openid)) {
					$reply_text = '感谢您关注该账号, 请仔细查看如下声明:'."\n".'1.这里可以记录下您喜欢的人的微信号,如果您喜欢的人恰好也喜欢你,并也在这里做了记录,我们可以通知您'."\n".'2.爱情应该是真诚的,因此我们采用文字这一普通而又庄重的形式来进行记录'."\n".'3.记录内容不会被任何人看到,记录期间您可以随时输入"取消"来终止记录,但是记录一旦完成,一个月内将不得修改'."\n".'4.我们不会收取任何费用'."\n".'如果您同意该声明, 请输入"我同意"。';
				}
			}else{//已经确认过自己微信号了
				if (Dw_Savelove::start_old_user($openid)) {
					$reply_text = '感谢您重新关注该账号, 请仔细查看如下声明:'."\n".'1.这里可以记录下您喜欢的人的微信号,如果您喜欢的人恰好也喜欢你,并也在这里做了记录,我们可以通知您'."\n".'2.爱情应该是真诚的,因此我们采用文字这一普通而又庄重的形式来进行记录'."\n".'3.记录内容不会被任何人看到,记录期间您可以随时输入"取消"来终止记录,但是记录一旦完成,一个月内将不得修改'."\n".'4.我们不会收取任何费用'."\n".'您已同意过该声明,并确认了自己的微信号,现在您可以输入"开始"来启动记录程序。';
				}
			}
		}else{
			if($text === '取消' && $progress>3 && $progress<8){
				if(Dw_Savelove::cancel_love_record($openid)){
					$reply_text = '您已取消记录行为, 如果您想重新开始记录, 请输入"开始"。';
				}
			}else{
				switch($progress){
					case '0':
						if($text === '我同意'){
							if(Dw_Savelove::new_user_convince($openid)){
								$reply_text = '感谢您对本公众号的信任,您的微信号将是您的唯一凭证,请本着对自己和他人负责的态度输入您正确的微信号。'."\n".'请问您的微信号是什么?';
							}
						}else{
							$reply_text = '如果您对该公众号有什么建议, 可以留言, 感谢您的使用。如果您同意以上声明, 请输入"我同意"。';
						}
						break;
					case '1':
						if(Dw_Savelove::new_user_name($openid, $text)){
							$reply_text = '您确认"'.$text. '"是您的微信号吗? 一经确认将无法修改。如果输错了, 可以再重新输入。'."\n".'如果确认请输入"我确认"。';
						}
						break;
					case '2':
						if($text === '我确认'){
							if(Dw_Savelove::new_user_name_convince($openid)){
								$reply_text = '微信号已成功确认。'."\n".'现在您可以输入"开始"来启动记录程序。';
							}
						}else{
							if(Dw_Savelove::new_user_name($openid, $text)){
								$reply_text = '您确认"'.$text. '"是您的微信号吗? 一经确认将无法修改。如果输错了, 可以再重新输入。'."\n".'如果确认请输入"我确认"。';
							}
						}
						break;
					case '3':
						if($text === '开始'){
							if(Dw_Savelove::start_love_record($openid)){
								$reply_text = '您已成功开始记录程序. '."\n".'您的暗恋人的微信号是什么?';
							}
						}else{
							$reply_text = '你可以输入"开始"来启动记录程序。';
						}
						break;
					case '4':
						if(Dw_Savelove::new_love_name($openid, $text)){
							$reply_text = '您确认"'.$text.'"是您暗恋人的微信号吗? 一经确认一个月内将无法修改。如果输错了, 可以再重新输入。'."\n".'如果确认请输入"我确认"。';
						}
						break;
					case '5':
						if($text === '我确认'){
							if(Dw_Savelove::new_love_name_convince($openid)){
								$reply_text = '对方微信号已成功确认。 如果成功匹配, 你想对他/她说什么?';
							}
						}else{
							if(Dw_Savelove::new_love_name($openid, $text)){
								$reply_text = '您确认"'.$text.'"是您暗恋人的微信号吗? 一经确认一个月内将无法修改。如果输错了, 可以再重新输入。'."\n".'如果确认请输入"我确认"。';
							}
						}
						break;
					case '6':
						if(Dw_Savelove::new_love_msg($openid, $text)){
							$reply_text = '您确认想对他/她说下面的话吗? 一经确认将无法修改。如果输错了, 可以再重新输入。如果确认请输入"我确认":'."\n".$text;
						}
						break;
					case '7':
						if($text === '我确认'){
							if(Dw_Savelove::new_love_msg_convince($openid)){
								$reply_text = 'OK. 记录完成了, 您可以随时输入"检查匹配"来查看匹配结果。'."\n".'感谢您的信任与支持. 愿有情人终成眷属。';
							}
						}else{
							if(Dw_Savelove::new_love_msg($openid, $text)){
								$reply_text = '您确认想对他/她说下面的话吗? 一经确认将无法修改。如果输错了, 可以再重新输入。如果确认请输入"我确认":'."\n".$text;
							}
						}
						break;
					case '8':
						if($text === '开始'){
							if((time() - $love_info['uptime']) < self::LOVE_INFO_VALID_TIME){
								$reply_text = '您已完成过记录, 一个月内不可以再次记录。';
							}else{
								if(Dw_Savelove::start_love_record($openid)){
									$reply_text = '您已成功开始新的记录程序。您的暗恋人的微信号是什么?';
								}
							}
						}elseif($text === '检查匹配'){
							$check_rst = Dr_Getlove::check_love_to_love($love_info);
							if($check_rst['love_each_other'] == true){
								Dw_Savelove::love_to_love_convince(array($love_info['openid']));
								$reply_text = 'oh my god! 匹配成功! 你暗恋的人也暗恋着你! 这是他/她想对您说的话:'."\n".$check_rst['love_msg'];
							}else{
								$reply_text = '对不起, 还没有您的匹配消息, 再等等吧。。。';
							}
						}else{
							$reply_text = '您已完成记录, 您可以随时输入"检查匹配"来查看匹配结果。'."\n".'感谢您的信任与支持。 愿有情人终成眷属。';
						}

						break;
					case '9':
						if($text === '开始'){
							if((time() - $love_info['uptime']) < self::LOVE_INFO_VALID_TIME){
								$reply_text = '您已完成过记录, 一个月内不可以再次记录.';
							}else{
								if(Dw_Savelove::start_love_record($openid)){
									$reply_text = '您已成功开始新的记录程序. 您的暗恋人的微信号是什么?';
								}
							}
						}else{
							$reply_text = '您已成功匹配了恋人, 恭喜您。 如果确认要重新匹配, 请输入"开始", 虽然我们不想看到您输入这两个字。';
						}
						break;
				}
			}

		}

		if(!isset($reply_text)){
			if (Dw_Savelove::start_new_user($openid)) {
				$reply_text = '请仔细查看如下声明:'."\n".'1.这里可以记录下您喜欢的人的微信号,如果您喜欢的人恰好也喜欢你,并也在这里做了记录,我们可以通知您'."\n".'2.爱情应该是真诚的,因此我们采用文字这一普通而又庄重的形式来进行记录'."\n".'3.记录内容不会被任何人看到,记录期间您可以随时输入"取消"来终止记录,但是记录一旦完成,一个月内将不得修改'."\n".'4.我们不会收取任何费用'."\n".'如果您同意该声明, 请输入"我同意"';
			}else{
				$reply_text = '(⊙o⊙)...系统出错了...(⊙o⊙)';
			}
		}

		return $reply_text;
	}

}