<?php

namespace Core;

use database\Database;

class Validator
{
    private static $data = [];
    public static function set_data($data): void
    {
        static::$data = $data;
    }
    public static function min($value, $min): bool|string
    {
        if (strlen($value) >= $min) {
            return false;
        }
        return "Must be at least {$min} characters long.";
    }
    public static function max($value, $max): bool|string
    {
        if (strlen($value) <= $max) {
            return false;
        }
        return "Must not exceed {$max} characters.";
    }
    public static function email($value): bool|string
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return "Must be a valid email address.";
    }
    public static function required($value): bool|string|array
    {
        if (!empty($value)) {
            return false;
        }
        return "Is required.";
    }
    public static function unique($value, $table_data): bool|string
    {
        if (empty($value)) {
            return false;
        }

        $table = "\App\Models\\".$table_data[0];
        $column = $table_data[1];

        try {
            $result = $table::where($column, '=', $value)->get();
            if (!$result) {
                return false;
            }
            return "Already exists.";
        } catch (\Exception $e) {
            return false;
        }
    }
    public static function date($value): bool|string
    {
        $date = date_parse($value);
        if ($date['error_count'] === 0 && checkdate($date['month'], $date['day'], $date['year'])) {
            return false;
        }
        return "Must be a valid date.";
    }
    public static function numeric($value): bool|string
    {
        if (is_numeric($value)) {
            return false;
        }
        return "Must be a number.";
    }
    public static function boolean($value): bool|string
    {
        if (is_bool($value)) {
            return false;
        }
        return "Must be a boolean.";
    }
    public static function string($value): bool|string
    {
        if (is_string($value)) {
            return false;
        }
        return "Must be a string.";
    }
    public static function integer($value): bool|string
    {
        if (is_int($value)) {
            return false;
        }
        return "Must be an integer.";
    }
    public static function float($value): bool|string
    {
        if (is_float($value)) {
            return false;
        }
        return "Must be a float.";
    }
    public static function array($value): bool|string
    {
        if (is_array($value)) {
            return false;
        }
        return "Must be an array.";
    }
    public static function nullable($value): bool|string
    {
        if ($value === null) {
            return false;
        }
        return "Must be nullable.";
    }

    public static function confirmed($value): bool|string
    {
        if ($value === static::$data['confirm_password']) {
            return false;
        }
        return "Must be confirmed.";
    }

    public static function alpha($value): bool|string
    {
        if (ctype_alpha($value)) {
            return false;
        }
        return "Must be alphabetic.";
    }

    public static function alpha_num($value): bool|string
    {
        if (ctype_alnum($value)) {
            return false;
        }
        return "Must be alphanumeric.";
    }

    public static function alpha_dash($value): bool|string
    {
        if (preg_match('/^[a-zA-Z0-9_-]*$/', $value)) {
            return false;
        }
        return "Must be alphabetic, numeric, dash, or underscore.";
    }
    public static function not_today($value): bool|string
    {
        if ($value > date('Y-m-d')) {
            return false;
        }
        return "Must not be today.";
    }

    public static function image($value): bool|string|array
    {
        try {
            $type = explode('/', $value['type'])[0];
            if ($type == 'image') {
                return false;
            }
            return "Must be a valid Image.";
        } catch (\Exception $e) {
            return "Must be a valid Image.";
        }
    }

    public static function url($value): bool|string|array
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return false;
        }
        return "Must be a valid URL.";
    }

}