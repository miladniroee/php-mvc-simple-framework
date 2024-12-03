<?php

namespace Core\Commands;

class Command
{
    public static function red($string)
    {
        echo "\033[31m$string\033[0m";
    }

    public static function green($string)
    {
        echo "\033[32m$string\033[0m";
    }

    public static function blue($string)
    {
        echo "\033[34m$string\033[0m";
    }

    public static function yellow($string)
    {
        echo "\033[33m$string\033[0m";
    }

    public static function cyan($string)
    {
        echo "\033[36m$string\033[0m";
    }

    public static function magenta($string)
    {
        echo "\033[35m$string\033[0m";
    }

    public static function white($string)
    {
        echo "\033[37m$string\033[0m";
    }


}