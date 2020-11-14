<?php
namespace App\Helpers;

class HelperMethods
{
    public static function toCamelCase($string)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $string));

        $value = str_replace(' ', '', $value);

        $method = lcfirst($value);

        return $method;
    }

    public static function config($key)
    {
        $config = include 'config/metric_reports.php';

        return $config[$key];
    }
}
