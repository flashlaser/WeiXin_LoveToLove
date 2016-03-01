<?php
/**
 *
 */
class Dw_Savelove extends Dw_Abstract {

	const db_pool = 'eye2eye';

	/**
	 * progress=0, 加入新用户
	 */
	public static function start_new_user($openid){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'insert into loverel (`openid`, `fuser`,`uptime`) values (?,?,?) on duplicate key update uptime=?,fuser=?,tuser=?,lovemsg=?,progress=?';
			$rst = $db->exec($sql, array($openid, $openid, time(), time(), $openid, '', '', 0));
			if (is_numeric($rst) && $rst > 0) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * 已经确认过微信号的老用户又重新关注,为防止它使用其他人的微信号
	 */
	public static function start_old_user($openid){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set uptime=?,tuser=?,lovemsg=?,progress=? where openid=?';
			$rst = $db->exec($sql, array(time(),'','',3,$openid));
			if (is_numeric($rst) && $rst > 0) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * progress=1, 新用户确认声明或玩法
	 */
	public static function new_user_convince($openid){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set progress=?,uptime=? where openid=? and progress=0';
			$rst = $db->exec($sql, array(1, time(), $openid));
			if (is_numeric($rst) && $rst > 0) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * progress=2, 新用户输入自己微信号名称
	 */
	public static function new_user_name($openid, $fuser){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set fuser=?,progress=?,uptime=? where openid=? and progress in (1,2)';
			$rst = $db->exec($sql, array($fuser, 2, time(), $openid));
			if (is_numeric($rst)) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * progress=3, 新用户确认自己的微信号名称
	 */
	public static function new_user_name_convince($openid){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set progress=?,uptime=? where openid=? and progress=2';
			$rst = $db->exec($sql, array(3, time(), $openid));
			if (is_numeric($rst) && $rst > 0) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * progress=4, 用户开始love record进程
	 */
	public static function start_love_record($openid){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set progress=?,uptime=? where openid=? and progress in (3,8,9)';
			$rst = $db->exec($sql, array(4, time(), $openid));
			if (is_numeric($rst) && $rst > 0) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * 用户取消love record进程,progress重置为3
	 */
	public static function cancel_love_record($openid){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set progress=?,uptime=?,tuser=?,lovemsg=? where openid=? and progress>3 and progress<8';
			$rst = $db->exec($sql, array(3, time(), '', '', $openid));
			if (is_numeric($rst) && $rst > 0) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * progress=5, 用户输入了暗恋对象的微信号
	 */
	public static function new_love_name($openid, $tuser){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set tuser=?,progress=?,uptime=? where openid=? and progress in (4,5)';
			$rst = $db->exec($sql, array($tuser, 5, time(), $openid));
			if (is_numeric($rst)) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * progress=6, 用户确认输入的暗恋对象的微信号
	 */
	public static function new_love_name_convince($openid){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set progress=?,uptime=? where openid=? and progress=5';
			$rst = $db->exec($sql, array(6, time(), $openid));
			if (is_numeric($rst) && $rst > 0) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * progress=7, 用户输入对暗恋对象想说的话
	 */
	public static function new_love_msg($openid, $lovemsg){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set lovemsg=?,progress=?,uptime=? where openid=? and progress in (6,7)';
			$rst = $db->exec($sql, array($lovemsg, 7, time(), $openid));
			if (is_numeric($rst)) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * progress=8, 用户确认输入对暗恋对象想说的话, 记录完成, 等待匹配
	 */
	public static function new_love_msg_convince($openid){
		try {
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'update loverel set progress=?,uptime=? where openid=? and progress=7';
			$rst = $db->exec($sql, array(8, time(), $openid));
			if (is_numeric($rst) && $rst > 0) {
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * progress=9, 爱情成功匹配!
	 */
	public static function love_to_love_convince(array $openids){
		try {
			if(empty($openids)){
				return false;
			}

			$db = Comm_Db::pool(self::db_pool);
			foreach($openids as $openid){
				$sql = 'update loverel set progress=?,uptime=? where openid=? and progress=8';
				$rst = $db->exec($sql, array(9, time(), $openid));
				if(!is_numeric($rst)){
					return false;
				}
			}

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

}
