<?php
// src/EventService.php
namespace bleeld\Event;

use think\facade\Config;
use think\facade\Request;
use think\Service;
use ReflectionClass;

class EventService extends Service
{
    protected static $mappings = []; // 缓存: [category][action] => methodName
    protected static $validSubscribers = []; // 缓存有效订阅者

    public static function trigger($category = null, $data = [])
    {
        $request = Request::instance();
        $action = strtolower($request->action());
        $categories = [];
        $isSingleParam = false;

        // 参数处理逻辑优化：支持任意数据类型
        if (func_num_args() === 1) {
            // 单参数模式：任意类型都视为数据，遍历所有订阅者
            $data = $category;
            $categories = array_keys(self::getSubscribers());
            $isSingleParam = true;
        } elseif ($category === null && func_num_args() === 0) {
            // 无参数模式：空数据遍历所有订阅者
            $categories = array_keys(self::getSubscribers());
        } else {
            // 双参数模式：指定类别和数据
            $categories = [$category];
        }

        $subscribers = self::getSubscribers();
        $mappings = self::getMappings();

        foreach ($categories as $currentCategory) {
            // 支持新格式：类别.方法名（无on前缀）
            if (strpos($currentCategory, '.') !== false) {
                list($categoryPart, $methodPart) = explode('.', $currentCategory, 2);
                $methodName = 'on' . ucfirst($methodPart);
                
                if (isset($subscribers[$categoryPart])) {
                    $subscriberClass = $subscribers[$categoryPart];
                    
                    // 检查方法是否存在
                    if (method_exists($subscriberClass, $methodName)) {
                        $reflection = new \ReflectionMethod($subscriberClass, $methodName);
                        $params = $reflection->getParameters();
                        $callParams = [];

                        if (count($params) > 0) {
                            $callParams[] = $data;
                        }

                        (new $subscriberClass())->$methodName(...$callParams);
                    }
                }
                continue; // 跳过原有映射逻辑
            }

            if (isset($mappings[$currentCategory][$action])) {
                $methodName = $mappings[$currentCategory][$action];
                $subscriberClass = $subscribers[$currentCategory];

                // 获取方法参数信息，支持任意类型参数传递
                $reflection = new \ReflectionMethod($subscriberClass, $methodName);
                $params = $reflection->getParameters();
                $callParams = [];

                // 根据方法参数数量决定如何传递数据
                if (count($params) > 0) {
                    // 支持单参数方法（如onUserDelete($user_id)）
                    $callParams[] = $data;
                }

                (new $subscriberClass())->$methodName(...$callParams);
            }
        }
    }

    protected static function getMappings()
    {
        if (empty(self::$mappings)) {
            $subscribers = self::getSubscribers();
            $mappings = [];

            foreach ($subscribers as $category => $subscriberClass) {
                $reflection = new ReflectionClass($subscriberClass);
                $methods = $reflection->getMethods();

                foreach ($methods as $method) {
                    $methodName = $method->getName();
                    // 仅处理事件方法（以on开头且非构造函数）
                    if (strpos($methodName, 'on') === 0 && $methodName !== '__construct') {
                        $action = strtolower(substr($methodName, 2));
                        $mappings[$category][$action] = $methodName; // 存储完整方法名
                    }
                }
            }
            self::$mappings = $mappings;
        }
        return self::$mappings;
    }

    protected static function getSubscribers()
    {
        if (empty(self::$validSubscribers)) {
            $config = Config::get('smart_event', []);
            $rawSubscribers = isset($config['subscribers']) ? $config['subscribers'] : $config;
            $validSubscribers = [];

            // 预过滤无效订阅者（仅保留存在的类）
            foreach ($rawSubscribers as $category => $class) {
                if (class_exists($class)) {
                    $validSubscribers[$category] = $class;
                }
            }
            self::$validSubscribers = $validSubscribers;
        }
        return self::$validSubscribers;
    }
}