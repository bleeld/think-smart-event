<?php
// src/facade/SmartEvent.php
namespace bleeld\Event\facade;

use think\Facade;

class SmartEvent extends Facade
{
    protected static function getFacadeClass()
    {
        return 'bleeld\\Event\\EventService';
    }
}