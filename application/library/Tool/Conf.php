<?php
/**
  * 配置文件读取器(封装Yaf_Config_Ini)
  */
class Tool_Conf {
	
	private static $inst = array();
	
	private function __construct(){

	}
	
	public static function get($key){
        if (strpos($key, '.') !== false) {
            list($file, $path) = explode('.', $key, 2);
        }else{
            $file = $key;
        }
        $config_file = APPPATH . '/config/' . $file . '.ini';
        if (!file_exists($config_file)) {
            return false;
        }
        if (!isset(self::$inst[$file])) {
            self::$inst[$file] = new Yaf_Config_Ini($config_file);
        }

        if (!isset($path)) {
            return self::$inst[$file]->toArray();
        }
        
        /**
         * 注意：Yaf_Config_Ini::get()方法不能获取key为数字的对象，此处用path方法替换
         */
        return self::path(self::$inst[$file]->toArray(), $path);
	}
	
    public static function path($array, $path, $default = NULL) {
        if (array_key_exists($path, $array)) {
            return $array[$path];
        }
        
        $delimiter = ".";
        //$path = trim($path, "{$delimiter}* ");
        $keys = explode($delimiter, $path);
        do {
            $key = array_shift($keys);
            
            if (isset($array[$key])) {
                if ($keys) {
                    if (is_array($array[$key])) {
                        $array = $array[$key];
                    } else {
                        break;
                    }
                } else {
                    return $array[$key];
                }
            } elseif ($key === '*') {
                $values = array();
                $inner_path = implode($delimiter, $keys);
                foreach ($array as $arr) {
                    $value = is_array($arr) ? self::path($arr, $inner_path) : $arr;
                    if ($value) {
                        $values[] = $value;
                    }
                }
                
                if ($values) {
                    return $values;
                } else {
                    break;
                }
            } else {
                break;
            }
        } while ($keys);
        
        return $default;
    }
	
}
	
