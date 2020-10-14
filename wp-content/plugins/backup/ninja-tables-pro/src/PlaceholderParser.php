<?php namespace NinjaTablesPro;

class PlaceholderParser
{
    public static function parse($string)
    {
        $placeholders = self::parseShortcode($string);
        if(!$placeholders) {
            return $string;
        }

        wp_enqueue_script('ninja-tables-pro');

        $replaceData = [];
        foreach ($placeholders as $placeholderName => $placeholder) {
            if ( strpos($placeholder, ':') ) {
                $data = explode(':', $placeholder);
                // check if today or not
                $firstPart = $data[0];
                $firstArg = false;
                if(count($data) > 1) {
                    $firstArg = $data[1];
                }
                if (stripos($firstPart, 'date') !== false) {
                    $replaceData[$placeholderName] = self::parseDate($firstPart ,$firstArg);
                } else if (stripos($firstPart, 'user') !== false) {
                    $replaceData[$placeholderName] = self::parseUser($firstArg);
                } else if (stripos($firstPart, 'usermeta') !== false) {
                    $replaceData[$placeholderName] = get_user_meta(get_current_user_id(), $firstArg, true);
                }
            } else if($placeholder == 'gt' || $placeholder == 'greater') {
                $replaceData[$placeholderName] = ">";
            } else if($placeholder == 'lt' || $placeholder == 'lesser' ) {
                $replaceData[$placeholderName] = "<";
            } else if($placeholder == 'current_post_id') {
                $replaceData[$placeholderName] = get_the_ID();
            } else if($placeholder == 'current_post_title') {
                $replaceData[$placeholderName] = get_the_title();
            } else if($placeholder == 'current_user_role') {
                $user = get_user_by('ID', get_current_user_id());
                if(!$user) {
                    return '';
                }
                $replaceData[$placeholderName] = implode(',', $user->roles);
            }
        }
        return str_replace(array_keys($replaceData), array_values($replaceData), $string);
    }

    public static function parseDate($dateString, $dateFormat = false)
    {
        if($dateFormat == 'timestamp') {
            return time();
        }

        $dateFormat = self::convertMomentFormatToPhp($dateFormat);
        if(stripos($dateString, '+')) {
            $args = explode('+', $dateString);
            return date($dateFormat, time() + intval($args[1]) * 86400 );
        } else if(stripos($dateString, '-')) {
            $args = explode('-', $dateString);
            return date($dateFormat, time() - intval($args[1]) * 86400 );
        }
        return date($dateFormat);
    }

    public static function parseUser($key)
    {
        $user = get_user_by('ID', get_current_user_id());
        if(!$user) {
            return '';
        }
        return $user->{$key};
    }

    public static function parseShortcode($string)
    {
        $parsables = [];
        preg_replace_callback('/{+(.*?)}/', function ($matches) use (&$parsables) {
            $parsables[$matches[0]] = $matches[1];
        }, $string);
        return $parsables;
    }

    public static function convertMomentFormatToPhp($format)
    {
        if(!$format) {
            return 'Y-m-d';
        }
        $replacements = [
            'DD'   => 'd',
            'ddd'  => 'D',
            'D'    => 'j',
            'dddd' => 'l',
            'E'    => 'N',
            'o'    => 'S',
            'e'    => 'w',
            'DDD'  => 'z',
            'W'    => 'W',
            'MMMM' => 'F',
            'MM'   => 'm',
            'MMM'  => 'M',
            'M'    => 'n',
            'YYYY' => 'Y',
            'YY'   => 'y',
            'a'    => 'a',
            'A'    => 'A',
            'h'    => 'g',
            'H'    => 'G',
            'hh'   => 'h',
            'HH'   => 'H',
            'mm'   => 'i',
            'ss'   => 's',
            'SSS'  => 'u',
            'zz'   => 'e',
            'X'    => 'U',
        ];

        $phpFormat = strtr($format, $replacements);

        return $phpFormat;
    }
}
