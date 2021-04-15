<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/17/19
 * Time: 4:58 PM
 */

namespace Pyrobyte\Anticaptcha;


use Pyrobyte\Config\PyrobyteConfig;

class Config extends PyrobyteConfig
{
    protected static $config = [
        'curl' => [
            'timeout' => 30,
        ],
        'time_limit' => 60,
    ];

    protected static $defferedConfig = [];
}