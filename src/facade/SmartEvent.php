<?php
// src/facade/SmartEvent.php
namespace think\SmartEvent\facade;

use think\Facade;

class SmartEvent extends Facade
{
    protected static function getFacadeClass()
    {
        return 'think\\SmartEvent\\EventService';
    }
}