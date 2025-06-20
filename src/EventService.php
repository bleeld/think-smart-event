<?php
// src/EventService.php
namespace think\SmartEvent;

use think\facade\Config;
use think\facade\Request;
use ReflectionClass;

class EventService
{
    protected static $mappings = [];

    // 统一触发方法 - 支持可选类别名参数
    public static function trigger($category = null, $data = [])
    {
        if (is_array($category)) {
            // 兼容旧版单参数调用方式
            $data = $category;
            $request = Request::instance();
            $controller = strtolower($request->controller());
            $action = strtolower($request->action());
            
            // 移除 'Controller' 后缀
            $controller = str_replace('controller', '', $controller);
            $category = $controller;
        }
        
        $subscribers = self::getSubscribers();
        $mappings = self::getMappings();
        
        if (isset($mappings[$category][$action]) && isset($subscribers[$category])) {
            $subscriberClass = $subscribers[$category];
            $methodName = 'on' . ucfirst($action);
            
            if (class_exists($subscriberClass)) {
                $subscriber = new $subscriberClass();
                
                if (method_exists($subscriber, $methodName)) {
                    $subscriber->$methodName($data);
                }
            }
        }
    }

    // 获取自动生成的事件映射
    protected static function getMappings()
    {
        if (empty(self::$mappings)) {
            $subscribers = self::getSubscribers();
            
            foreach ($subscribers as $category => $subscriberClass) {
                if (class_exists($subscriberClass)) {
                    $reflection = new ReflectionClass($subscriberClass);
                    $methods = $reflection->getMethods();
                    
                    $mappings[$category] = [];
                    
                    foreach ($methods as $method) {
                        $methodName = $method->getName();
                        
                        if (strpos($methodName, 'on') === 0 && $methodName !== '__construct') {
                            $action = strtolower(substr($methodName, 2));
                            $mappings[$category][$action] = $action;
                        }
                    }
                }
            }
            
            self::$mappings = $mappings;
        }
        
        return self::$mappings;
    }

    // 获取订阅者配置 - 兼容两种格式
    protected static function getSubscribers()
    {
        $config = Config::get('event', []);
        
        // 兼容两种配置格式
        if (isset($config['subscribers'])) {
            // 格式1: ['subscribers' => ['user' => User::class, ...]]
            return $config['subscribers'];
        } else {
            // 格式2: ['user' => User::class, ...]
            return $config;
        }
    }
}