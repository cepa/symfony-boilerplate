<?php

namespace Core\Helper;

class RandomStringGenerator
{
    const DICT = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    public static function generate($n)
    {
        return self::randomize(self::DICT, $n);
    }

    public static function randomize($dict, $n)
    {
        $m = strlen($dict);
        $s = '';
        $n++;
        while (--$n) {
            $s .= $dict[rand(0, $m - 1)];
        }
        return $s;
    }
}
