<?php

namespace nathanabrewer\RelateIQ\Resource;

class RelateIQConfig{

    public static $key = "";
    public static $secret = "";

    public static function setKey($key, $secret){
        self::$key = $key;
        self::$secret = $secret;
    }
}
