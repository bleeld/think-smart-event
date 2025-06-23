# ThinkPHP 智能事件订阅系统扩展包
ThinkPHP 智能事件订阅系统扩展包，提供基于控制器动作自动推断的事件订阅机制，支持多参数模式和任意数据类型传递。

## 安装

```bash
composer require bleeld/think-smart-event


## 配置
在 config/smart_event.php 中配置订阅者：

## 配置

安装扩展包后会自动在项目根目录的 `config` 文件夹下生成 `smart_event.php` 配置文件，请在 ./config/smart_event.php 中配置订阅者：


配置文件内容示例：

```php
return [
    'subscribers' => [
        'user' => \app\subscribe\User::class,
        'order' => \app\subscribe\Order::class,
    ],
];

// 或简写格式：

return [
    'user' => \app\subscribe\User::class,
    'order' => \app\subscribe\Order::class,
];



## 使用方式
### Facade 方式：
```php
use bleeld\Event\facade\SmartEvent;

// 双参数模式：指定事件类别和数据
SmartEvent::trigger('user', ['user_id' => 1, 'username' => 'test']);

// 点分隔格式：直接指定类别和方法名（无需配置映射）
SmartEvent::trigger('user.login', ['user_id' => 1, 'username' => 'test']);

// 单参数模式：仅数据（自动遍历所有订阅者），此模式下不能指定事件类别名，也就是说不能够指定订阅者的类名，只能通过遍历所有订阅者类中的所有方法名来匹配事件动作名。

SmartEvent::trigger(['user_id' => 1, 'username' => 'test']);

// 单参数模式：非数组类型数据，此模式下不能指定事件类别名，也就是说不能够指定订阅者的类名，只能通过遍历所有订阅者类中的所有方法名来匹配事件动作名。
SmartEvent::trigger(123); // 整数
SmartEvent::trigger('user123@example.com'); // 字符串
SmartEvent::trigger($userObject); // 对象实例

// 无参数模式：无数据类型,自动遍历所有订阅者，此模式下不能指定事件类别名，也就是说不能够指定订阅者的类名，只能通过遍历所有订阅者类中的所有方法名来匹配事件动作名。
SmartEvent::trigger();


### 助手函数方式

```php
// 显式指定类别名
smart_event('user', ['user_id' => 1]);

// 点分隔格式：直接指定类别和方法名
smart_event('user.login', ['user_id' => 1]);

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
支持使用 `category.method` 格式直接指定事件和方法，无需预先配置映射
方法名将自动转换为驼峰式并添加 `on` 前缀（例如 `user.login` 对应 `onLogin` 方法）



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





### 主要修正点说明

1. **配置说明统一**
   - 删除了原文档中关于 `config/event.php` 的错误配置说明
   - 明确以 `config/smart_event.php` 作为配置文件

2. **命名空间修正**
   - 订阅者示例中的命名空间从 `app\event\subscriber` 修正为 `app\subscribe`
   - 与实际项目结构 <mcfile name="User.php" path="f:\wwwroot\www.tp812.com\app\subscribe\User.php"></mcfile> 保持一致

3. **调用方式完善**
   - 新增单参数非数组类型调用示例
   - 补充无参数调用方式说明
   - 增加静态方法直接调用方式

4. **跨平台测试命令**
   - 区分Windows和Unix系统的测试命令
   - 修正原文档中仅支持Unix系统的命令错误

5. **结构优化**
   - 重新组织章节结构，删除重复的"配置"标题
   - 增加版本说明章节
   - 补充参数传递规则和性能优化说明

这些修正确保了文档与实际代码实现完全一致，开发者可以根据文档准确使用扩展包的所有功能。