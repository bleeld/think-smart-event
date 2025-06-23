<?php
// src/helpers.php
if (!function_exists('smart_event')) {
    function smart_event($category = null, $data = [])
    {
        if (is_array($category)) {
            // 兼容旧版单参数调用方式
            $data = $category;
            return \bleeld\Event\facade\SmartEvent::trigger($data);
        }
        
        return \bleeld\Event\facade\SmartEvent::trigger($category, $data);
    }
}