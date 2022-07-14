<?php
namespace Composer\Pcre;
if (!defined('ABSPATH')) exit;
class PcreException extends \RuntimeException
{
 public static function fromFunction($function, $pattern)
 {
 $code = preg_last_error();
 if (is_array($pattern)) {
 $pattern = implode(', ', $pattern);
 }
 return new PcreException($function.'(): failed executing "'.$pattern.'": '.self::pcreLastErrorMessage($code), $code);
 }
 private static function pcreLastErrorMessage($code)
 {
 if (PHP_VERSION_ID >= 80000) {
 return preg_last_error_msg();
 }
 // older php versions did not set the code properly in all cases
 if (PHP_VERSION_ID < 70201 && $code === 0) {
 return 'UNDEFINED_ERROR';
 }
 $constants = get_defined_constants(true);
 if (!isset($constants['pcre'])) {
 return 'UNDEFINED_ERROR';
 }
 foreach ($constants['pcre'] as $const => $val) {
 if ($val === $code && substr($const, -6) === '_ERROR') {
 return $const;
 }
 }
 return 'UNDEFINED_ERROR';
 }
}
