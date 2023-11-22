<?php

namespace DynamicContentForElementor;

trait Date
{
    /**
     * Convert date/time format between `date()` and `strftime()`
     *
     * Timezone conversion is done for Unix. Windows users must exchange %z and %Z.
     *
     * Unsupported date formats : S, n, t, L, B, G, u, e, I, P, Z, c, r
     * Unsupported strftime formats : %U, %W, %C, %g, %r, %R, %T, %X, %c, %D, %F, %x
     *
     * @example Convert `%A, %B %e, %Y, %l:%M %P` to `l, F j, Y, g:i a`, and vice versa for "Saturday, March 10, 2001, 5:16 pm"
     * @link http://php.net/manual/en/function.strftime.php#96424
     *
     * @param string $format The format to parse.
     * @param string $syntax The format's syntax. Either 'strf' for `strtime()` or 'date' for `date()`.
     * @return bool|string Returns a string formatted according $syntax using the given $format or `false`.
     * @copyright Chauncey McAskill, Baptiste Placé
     * @link https://github.com/mcaskill
     */
    public static function date_format_to($format, $syntax)
    {
        // http://php.net/manual/en/function.strftime.php
        $strf_syntax = [
            // Day - no strf eq : S (created one called %O)
            '%O',
            '%d',
            '%a',
            '%e',
            '%A',
            '%u',
            '%w',
            '%j',
            // Week - no date eq : %U, %W
            '%V',
            // Month - no strf eq : n, t
            '%B',
            '%m',
            '%b',
            '%-m',
            // Year - no strf eq : L; no date eq : %C, %g
            '%G',
            '%Y',
            '%y',
            // Time - no strf eq : B, G, u; no date eq : %r, %R, %T, %X
            '%P',
            '%p',
            '%l',
            '%I',
            '%H',
            '%M',
            '%S',
            // Timezone - no strf eq : e, I, P, Z
            '%z',
            '%Z',
            // Full Date / Time - no strf eq : c, r; no date eq : %c, %D, %F, %x
            '%s',
        ];
        // http://php.net/manual/en/function.date.php
        $date_syntax = ['S', 'd', 'D', 'j', 'l', 'N', 'w', 'z', 'W', 'F', 'm', 'M', 'n', 'o', 'Y', 'y', 'a', 'A', 'g', 'h', 'H', 'i', 's', 'O', 'T', 'U'];
        switch ($syntax) {
            case 'date':
                $from = $strf_syntax;
                $to = $date_syntax;
                break;
            case 'strf':
                $from = $date_syntax;
                $to = $strf_syntax;
                break;
            default:
                return \false;
        }
        $pattern = \array_map(function ($s) {
            return '/(?<!\\\\|\\%)' . $s . '/';
        }, $from);
        return \preg_replace($pattern, $to, $format);
    }
    /**
     * Equivalent to `date_format_to( $format, 'date' )`
     *
     * @param string $strf_format A `strftime()` date/time format
     * @return string
     * @copyright Chauncey McAskill, Baptiste Placé
     * @link https://github.com/mcaskill
     */
    public static function strftime_format_to_date_format($strf_format)
    {
        return self::date_format_to($strf_format, 'date');
    }
    /**
     * Equivalent to `convert_datetime_format_to( $format, 'strf' )`
     *
     * @param string $date_format A `date()` date/time format
     * @return string
     * @copyright Chauncey McAskill, Baptiste Placé
     * @link https://github.com/mcaskill
     */
    public static function date_format_to_strftime_format($date_format)
    {
        return self::date_format_to($date_format, 'strf');
    }
}
