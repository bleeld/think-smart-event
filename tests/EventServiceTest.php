<?php
// tests/EventServiceTest.php
namespace think\SmartEvent\Tests;

use PHPUnit\Framework\TestCase;
use think\SmartEvent\facade\SmartEvent;
use think\facade\Config;

class EventServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // 模拟配置
        Config::set([
            'event' => [
                'subscribers' => [
                    'user' => \think\SmartEvent\Tests\stubs\UserSubscriber::class,
                    'order' => \think\SmartEvent\Tests\stubs\OrderSubscriber::class,
                ],
            ]
        ]);
    }

    public function testTriggerWithCategory()
    {
        $result = SmartEvent::trigger('user', ['user_id' => 1]);
        $this->assertNull($result); // 测试无返回值
    }

    public function testTriggerWithoutCategory()
    {
        // 模拟请求信息
        $_SERVER['PHP_SELF'] = '/user/login';
        
        $result = SmartEvent::trigger(['user_id' => 1]);
        $this->assertNull($result);
    }
}