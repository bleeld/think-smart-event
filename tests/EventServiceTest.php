<?php
// tests/EventServiceTest.php
namespace bleeld\Event\Tests;

use PHPUnit\Framework\TestCase;
use bleeld\Event\facade\SmartEvent;
use think\facade\Config;
use think\facade\Request;
use app\subscribe\User;
use app\subscribe\Order;

class EventServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // 修复配置键名，与实际应用保持一致
        Config::set([
            'smart_event' => [  // 原为 'event'，现修正为 'smart_event'
                'subscribers' => [
                    'user' => User::class,  // 使用实际应用中的订阅者类
                    'order' => Order::class,
                ],
            ]
        ]);
    }

    /**
     * 测试双参数调用（指定类别和数据）
     */
    public function testTriggerWithCategoryAndData()
    {
        Request::setAction('Login'); // 模拟请求action
        $result = SmartEvent::trigger('user', ['user_id' => 1]);
        $this->assertNull($result);
    }

    /**
     * 测试单参数调用（仅数据，遍历所有类别）
     */
    public function testTriggerWithOnlyData()
    {
        Request::setAction('Login');
        $result = SmartEvent::trigger(['user_id' => 1]);
        $this->assertNull($result);
    }

    /**
     * 新增：测试无参数调用
     */
    public function testTriggerWithoutParameters()
    {
        Request::setAction('Update');
        $result = SmartEvent::trigger();
        $this->assertNull($result);
    }

    /**
     * 新增：测试非数组数据类型
     */
    public function testTriggerWithNonArrayData()
    {
        Request::setAction('Delete');
        // 测试整数类型数据
        $result = SmartEvent::trigger('user', 123);
        $this->assertNull($result);
        
        // 测试字符串类型数据
        $result = SmartEvent::trigger('user', 'test@example.com');
        $this->assertNull($result);
    }

    /**
     * 新增：测试缓存机制
     */
    public function testTriggerWithCaching()
    {
        Request::setAction('Login');
        
        // 第一次调用（应该构建缓存）
        SmartEvent::trigger('user', ['user_id' => 1]);
        
        // 第二次调用（应该使用缓存）
        $result = SmartEvent::trigger('user', ['user_id' => 1]);
        $this->assertNull($result);
    }
}