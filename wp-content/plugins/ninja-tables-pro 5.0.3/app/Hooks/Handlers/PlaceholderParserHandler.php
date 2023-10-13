<?php

namespace NinjaTablesPro\App\Hooks\Handlers;

class PlaceholderParserHandler
{
    public static function parse($string)
    {
        $placeholders = self::parseShortcode($string);
        if ( ! $placeholders) {
            return $string;
        }

        wp_enqueue_script('ninja-tables-pro');

        $replaceData = [];
        foreach ($placeholders as $placeholderName => $placeholder) {
            if (strpos($placeholder, ':')) {
                $data = explode(':', $placeholder);
                // check if today or not
                $firstPart = $data[0];
                $firstArg  = false;
                if (count($data) > 1) {
                    $firstArg = $data[1];
                }
                if (stripos($firstPart, 'date') !== false) {
                    $replaceData[$placeholderName] = self::parseDate($firstPart, $firstArg);
                } elseif (stripos($firstPart, 'user') !== false) {
                    $replaceData[$placeholderName] = self::parseUser($firstArg);
                } elseif (stripos($firstPart, 'usermeta') !== false) {
                    $replaceData[$placeholderName] = get_user_meta(get_current_user_id(), $firstArg, true);
                }
            } elseif ($placeholder == 'gt' || $placeholder == 'greater') {
                $replaceData[$placeholderName] = ">";
            } elseif ($placeholder == 'lt' || $placeholder == 'lesser') {
                $replaceData[$placeholderName] = "<";
            } elseif ($placeholder == 'current_post_id') {
                $replaceData[$placeholderName] = get_the_ID();
            } elseif ($placeholder == 'current_post_title') {
                $replaceData[$placeholderName] = get_the_title();
            } elseif ($placeholder == 'current_user_role') {
                $user = get_user_by('ID', get_current_user_id());
                if ( ! $user) {
                    return '';
                }
                $replaceData[$placeholderName] = implode(',', $user->roles);
            }
        }

        return str_replace(array_keys($replaceData), array_values($replaceData), $string);
    }

    public static function parseDate($dateString, $dateFormat = false)
    {
        $timeZone = static::getTimeZone();

        if ($timeZone) {
            date_default_timezone_set($timeZone);
        }

        if ($dateFormat == 'timestamp') {
            return time();
        }

        $dateFormat = self::convertMomentFormatToPhp($dateFormat);

        if (stripos($dateString, '+')) {
            $args = explode('+', $dateString);

            return date($dateFormat, time() + intval($args[1]) * 86400);
        } elseif (stripos($dateString, '-')) {
            $args = explode('-', $dateString);

            return date($dateFormat, time() - intval($args[1]) * 86400);
        }

        return date($dateFormat);
    }

    public static function parseUser($key)
    {
        $user = get_user_by('ID', get_current_user_id());
        if ( ! $user) {
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
        if ( ! $format) {
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

    public static function getTimezone()
    {
        // if site timezone string exists, return it
        $timezone = get_option('timezone_string');

        if ($timezone) {
            return ($timezone);
        }
        // get UTC offset, if it isn't set then return UTC
        $utcOffset = get_option('gmt_offset', 0);
        if ($utcOffset === 0) {
            return ('UTC');
        }
        // Adjust UTC offset from hours to seconds
        $utcOffset *= 3600;

        // Attempt to guess the timezone string from the UTC offset
        $timezone = timezone_name_from_abbr('', $utcOffset, 0);

        if ($timezone) {
            return ($timezone);
        }
        // Guess timezone string manually
        $isDst = date('I');
        foreach (timezone_abbreviations_list() as $abbr) {
            foreach ($abbr as $city) {
                if ($city['dst'] == $isDst && $city['offset'] == $utcOffset) {
                    $timezoneId = $city['timezone_id'];
                    if ($timezoneId) {
                        $timezone = timezone_name_from_abbr('', $timezoneId, 0);
                    }
                    if ($timezone) return ($timezone);
                }
            }
        }
        // Fallback
        return ('UTC');
    }
}
