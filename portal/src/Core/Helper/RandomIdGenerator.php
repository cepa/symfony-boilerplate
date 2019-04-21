<?php

namespace Core\Helper;

class RandomIdGenerator
{
    private static $dict1 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    private static $dict2 = '0123456789';

    public static function generate()
    {
        return RandomStringGenerator::randomize(self::$dict1, 1) . RandomStringGenerator::randomize(self::$dict1 . self::$dict2, 9);
    }
}
