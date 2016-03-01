<?php
/**
 * 数据读取器(DataReader)抽象类
 *
 * @package    Dr
 * @copyright  copyright(2011) weibo.com all rights reserved
 * @author     weibo.com php team
 */
abstract class Dr_Abstract {
    
    /**
     * 静态数据
     * 
     * @var array
     */
    protected static $static_data = array();
    
    /**
     * 筛选出未命中的缓存对象
     * 
     * @param array $items 批量从缓存中获取的对象
     * @param array $keys 批量的缓存key
     * @param array $data 用于保存命中的对象
     * @return array
     */
    public static function filter_cached_items(Array $items, Array $keys, Array &$data) {
        $no_cache_items = array();
        foreach($keys as $id => $key) {
            $item = isset($items[$key]) ? $items[$key] : FALSE;
            if (FALSE === $item) {
                $no_cache_items[] = $id;
                continue;
            }
            $data[$id] = $item;
        }
        return $no_cache_items;
    }
}
?>