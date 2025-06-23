<?php
// tests/stubs/UserSubscriber.php
namespace think\SmartEvent\Tests\stubs;

class UserSubscriber
{
    public function onLogin($eventData)
    {
        // 测试用空实现
    }

    public function onLogout($eventData)
    {
        // 测试用空实现
    }
}

// tests/stubs/OrderSubscriber.php
namespace think\SmartEvent\Tests\stubs;

class OrderSubscriber
{
    public function onPaid($eventData)
    {
        // 测试用空实现
    }
}