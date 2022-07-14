<?php
namespace Symfony\Component\Console\Helper;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
abstract class Helper implements HelperInterface
{
 protected $helperSet = null;
 public function setHelperSet(HelperSet $helperSet = null)
 {
 $this->helperSet = $helperSet;
 }
 public function getHelperSet()
 {
 return $this->helperSet;
 }
 public static function strlen($string)
 {
 $string = (string) $string;
 if (false === $encoding = mb_detect_encoding($string, null, true)) {
 return \strlen($string);
 }
 return mb_strwidth($string, $encoding);
 }
 public static function substr($string, $from, $length = null)
 {
 $string = (string) $string;
 if (false === $encoding = mb_detect_encoding($string, null, true)) {
 return substr($string, $from, $length);
 }
 return mb_substr($string, $from, $length, $encoding);
 }
 public static function formatTime($secs)
 {
 static $timeFormats = [
 [0, '< 1 sec'],
 [1, '1 sec'],
 [2, 'secs', 1],
 [60, '1 min'],
 [120, 'mins', 60],
 [3600, '1 hr'],
 [7200, 'hrs', 3600],
 [86400, '1 day'],
 [172800, 'days', 86400],
 ];
 foreach ($timeFormats as $index => $format) {
 if ($secs >= $format[0]) {
 if ((isset($timeFormats[$index + 1]) && $secs < $timeFormats[$index + 1][0])
 || $index == \count($timeFormats) - 1
 ) {
 if (2 == \count($format)) {
 return $format[1];
 }
 return floor($secs / $format[2]).' '.$format[1];
 }
 }
 }
 }
 public static function formatMemory($memory)
 {
 if ($memory >= 1024 * 1024 * 1024) {
 return sprintf('%.1f GiB', $memory / 1024 / 1024 / 1024);
 }
 if ($memory >= 1024 * 1024) {
 return sprintf('%.1f MiB', $memory / 1024 / 1024);
 }
 if ($memory >= 1024) {
 return sprintf('%d KiB', $memory / 1024);
 }
 return sprintf('%d B', $memory);
 }
 public static function strlenWithoutDecoration(OutputFormatterInterface $formatter, $string)
 {
 return self::strlen(self::removeDecoration($formatter, $string));
 }
 public static function removeDecoration(OutputFormatterInterface $formatter, $string)
 {
 $isDecorated = $formatter->isDecorated();
 $formatter->setDecorated(false);
 // remove <...> formatting
 $string = $formatter->format($string);
 // remove already formatted characters
 $string = preg_replace("/\033\[[^m]*m/", '', $string);
 $formatter->setDecorated($isDecorated);
 return $string;
 }
}
