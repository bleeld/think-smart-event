# ThinkPHP 智能事件订阅系统扩展包

ThinkPHP 智能事件订阅系统扩展包，提供基于控制器和方法自动推断的事件订阅机制。

## 安装

```bash
composer require yourname/think-smart-event


## 配置
在 config/event.php 中配置订阅者：

## 配置

安装扩展包后会自动在项目根目录的 `config` 文件夹下生成 `smart_event.php` 配置文件，请在 ./config/smart_event.php 中配置订阅者：


配置文件内容示例：

```php
return [
    'subscribers' => [
        'user' => \app\event\subscriber\User::class,
        'order' => \app\event\subscriber\Order::class,
    ],
];

// 或简写格式：

return [
    'user' => \app\event\subscriber\User::class,
    'order' => \app\event\subscriber\Order::class,
];



## 使用方式
### Facade 方式：
```php
use YourName\SmartEvent\facade\SmartEvent;

// 显式指定类别名
SmartEvent::trigger('user', ['user_id' => 1]);

// 自动推断类别名
SmartEvent::trigger(['user_id' => 1]);


### 助手函数方式

```php
// 显式指定类别名
smart_event('user', ['user_id' => 1]);

// 自动推断类别名
smart_event(['user_id' => 1]);

## 订阅者
### 订阅者实现示例
```php
namespace app\event\subscriber;

class User
{
    public function onLogin($eventData)
    {
        // 登录处理逻辑
    }

    public function onLogout($eventData)
    {
        // 登出处理逻辑
    }
}

## 测试
### 运行测试：
```bash
./vendor/bin/phpunit


## 注意事项
事件方法命名必须以 on 开头
方法名剩余部分将作为事件动作名
支持两种配置格式
支持 Facade 和助手函数两种调用方式



## 8. 安装说明

1. 创建上述文件结构
2. 运行 `composer install` 安装开发依赖
3. 运行测试确保一切正常
4. 打包发布到 Packagist

## 9. 使用说明

安装后，开发者可以像这样使用智能事件系统：

```php
// 在控制器中
class UserController
{
    public function login()
    {
        // 登录逻辑...
        SmartEvent::trigger('user', ['user_id' => 1]);
        // 或
        smart_event(['user_id' => 1]);
    }
}

## version 1.0.0
这个扩展包提供了完整的测试覆盖，确保核心功能稳定可靠。开发者可以根据需要扩展更多功能或自定义配置。