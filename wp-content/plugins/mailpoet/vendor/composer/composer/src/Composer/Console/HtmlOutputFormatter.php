<?php
namespace Composer\Console;
if (!defined('ABSPATH')) exit;
use Composer\Pcre\Preg;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
class HtmlOutputFormatter extends OutputFormatter
{
 private static $availableForegroundColors = array(
 30 => 'black',
 31 => 'red',
 32 => 'green',
 33 => 'yellow',
 34 => 'blue',
 35 => 'magenta',
 36 => 'cyan',
 37 => 'white',
 );
 private static $availableBackgroundColors = array(
 40 => 'black',
 41 => 'red',
 42 => 'green',
 43 => 'yellow',
 44 => 'blue',
 45 => 'magenta',
 46 => 'cyan',
 47 => 'white',
 );
 private static $availableOptions = array(
 1 => 'bold',
 4 => 'underscore',
 //5 => 'blink',
 //7 => 'reverse',
 //8 => 'conceal'
 );
 public function __construct(array $styles = array())
 {
 parent::__construct(true, $styles);
 }
 public function format($message)
 {
 $formatted = parent::format($message);
 $clearEscapeCodes = '(?:39|49|0|22|24|25|27|28)';
 // TODO in 2.3 replace with Closure::fromCallable and then use Preg::replaceCallback
 return preg_replace_callback("{\033\[([0-9;]+)m(.*?)\033\[(?:".$clearEscapeCodes.";)*?".$clearEscapeCodes."m}s", array($this, 'formatHtml'), $formatted);
 }
 private function formatHtml($matches)
 {
 $out = '<span style="';
 foreach (explode(';', $matches[1]) as $code) {
 if (isset(self::$availableForegroundColors[(int) $code])) {
 $out .= 'color:'.self::$availableForegroundColors[(int) $code].';';
 } elseif (isset(self::$availableBackgroundColors[(int) $code])) {
 $out .= 'background-color:'.self::$availableBackgroundColors[(int) $code].';';
 } elseif (isset(self::$availableOptions[(int) $code])) {
 switch (self::$availableOptions[(int) $code]) {
 case 'bold':
 $out .= 'font-weight:bold;';
 break;
 case 'underscore':
 $out .= 'text-decoration:underline;';
 break;
 }
 }
 }
 return $out.'">'.$matches[2].'</span>';
 }
}
