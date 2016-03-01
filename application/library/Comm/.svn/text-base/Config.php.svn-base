<?php

/**
 * 配置信息读取类
 *
 * @package Comm
 * @author  xiaowu <xiaowu@staff.sina.com.cn>
 */
class Comm_Config {
    
    /**
     * @var array 指定find_file需要遍历的文件夹路径
     */
    private static $paths = array(APPPATH);
    
    /**
     * 读取配置信息
     *
     * @param string $path 节点路径，第一个是文件名，使用点号分隔。如:"app","app.product.routes"
     *
     * @return array/string    成功返回数组或string
     */
    static public function get($path) {
        $arr = explode('.', $path, 2);
        try {
            $conf = new Yaf_Config_ini(APP_PATH . 'conf/' . $arr[0] . '.ini');
        } catch (Exception $e) {}
        !empty($arr[1]) && !empty($conf) && $conf = $conf->get($arr[1]);
        
        if (!isset($conf) || is_null($conf)) {
            throw new Exception_System(200401, "读取的配置信息不存在", array('path' => $path));
        }
        
        return is_object($conf) ? $conf->toArray() : $conf;
    }
    
    /**
     * 读取配置信息，找不到配置时，使用默认值
     *
     * @param  string $path 节点路径，第一个是文件名，使用点号分隔。如:"app","app.product.routes"
     *  @param object $default $path 不存在时使用                 
     * @return array/string    返回配置value或者$default
     */
    static public function getWithDefault($path, $default = null) {
        $conf = $default;
        try {
            $conf = self::get($path);
        } catch (Exception_System $e) {
            //ignore
        }
        return $conf;
    }
    
    /**
     * 读取配置信息（使用静态数据缓存）
     *
     * @param string $path 节点路径，第一个是文件名，使用点号分隔。如:"app","app.product.routes"
     *
     * @return array/string    成功返回数组或string
     */
    static public function getUseStatic($path) {
        $static_key = 'get_' . $path;
        
        $result = Comm_Sdata::get(__CLASS__, $static_key);
        if ($result === false) {
            $result = self::get($path);
            Comm_Sdata::set(__CLASS__, $static_key, $result);
        }
        return $result;
    }
    
    /**
     * 加载指定的配置文件
     *
     * @param string 映射configuration文件名
     * @return array
     */
    public static function load($config_file) {
        $files = self::find_file('/config', $config_file, true);
        if (empty($files)) {
            throw new Exception("config file not exists");
        }
        
        $config = array();
        
        foreach ($files as $file) {
            $config = Comm_Array::merge($config, self::incl($file));
        }
        
        return $config;
    }
    
    /**
     * 在多个path中查找指定文件
     *
     * @param string 目录名称 (views, i18n, classes, extensions, etc.)
     * @param string 带有子目录的文件名
     * @param boolean 返回路径数组或者单个路径
     * @param bool $find_multi 是否查找多个路径
     * @return array|string|bool 查找到的路径名数组或者单个路径名。如果未查找到，返回false
     */
    public static function find_file($dir, $file, $find_multi = false) {
        $path = $dir . DIRECTORY_SEPARATOR . $file . ".php";
        if ($find_multi) {
            $paths = self::$paths;
            $paths = array_reverse($paths);
            $found = array();
            foreach ($paths as $dir) {
                if (is_file($dir  . $path)) {
                    $found[] = $dir  . $path;
                }
            }
        } else {
            $found = false;
            $paths = self::$paths;
            foreach ($paths as $dir) {
                if (is_file($dir  . $path)) {
                    $found = $dir  . $path;
                    break;
                }
            }
        }
        
        return $found;
    }
    
    /**
     * 包含一个文件
     *
     * @param string
     * @return mixed
     */
    public static function incl($file) {
        return include $file;
    }
    
    /**
     * 获取指定的配置项，如果$key不存在将报错
     * 进程内缓存，避免重复加载
     *
     * @param string $key 支持dot path方式获取
     */
    public static function php($key) {
        static $config = array();
        
        if (strpos($key, '.') !== false) {
            list($file, $path) = explode('.', $key, 2);
        } else {
            $file = $key;
        }
        
        if (!isset($config[$file])) {
            $config[$file] = self::load($file);
        }
        
        if (isset($path)) {
            $val = Comm_Array::path($config[$file], $path, "#not_found#");
            if ($val === "#not_found#") {
                throw new Exception("config key not exists:" . $key);
            }
            
            return $val;
        } else {
            // 获取整个配置
            return $config[$file];
        }
    }

}
