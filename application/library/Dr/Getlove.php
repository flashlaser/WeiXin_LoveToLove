<?php
/**
 *
 */
class Dr_Getlove extends Dr_Abstract {

	const db_pool = 'eye2eye';

	public static function get_love_info($openid){
		try{
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'select * from loverel where openid=?';
			$rst = $db->fetch_row($sql, array($openid));
			if(is_array($rst)){
				return $rst;
			}
			return array();
		}catch(Exception $e){
			return array();
		}

	}

	public static function check_love_to_love(array $love_info){
		try{
			$db = Comm_Db::pool(self::db_pool);
			$sql = 'select * from loverel where fuser=? and tuser=? and progress in (8,9)';
			$rst = $db->fetch_row($sql, array($love_info['tuser'], $love_info['fuser']));
			if(is_array($rst)){
				return array('love_each_other' => true, 'love_msg' => $rst['lovemsg']);
			}
			return false;
		}catch(Exception $e){
			return false;
		}

	}

}
