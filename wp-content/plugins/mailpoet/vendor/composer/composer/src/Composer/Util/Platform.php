<?php
namespace Composer\Util;
if (!defined('ABSPATH')) exit;
use Composer\Pcre\Preg;
class Platform
{
 private static $isVirtualBoxGuest = null;
 private static $isWindowsSubsystemForLinux = null;
 public static function getEnv($name)
 {
 if (array_key_exists($name, $_SERVER)) {
 return (string) $_SERVER[$name];
 }
 if (array_key_exists($name, $_ENV)) {
 return (string) $_ENV[$name];
 }
 return getenv($name);
 }
 public static function putEnv($name, $value)
 {
 $value = (string) $value;
 putenv($name . '=' . $value);
 $_SERVER[$name] = $_ENV[$name] = $value;
 }
 public static function clearEnv($name)
 {
 putenv($name);
 unset($_SERVER[$name], $_ENV[$name]);
 }
 public static function expandPath($path)
 {
 if (Preg::isMatch('#^~[\\/]#', $path)) {
 return self::getUserDirectory() . substr($path, 1);
 }
 return Preg::replaceCallback('#^(\$|(?P<percent>%))(?P<var>\w++)(?(percent)%)(?P<path>.*)#', function ($matches) {
 // Treat HOME as an alias for USERPROFILE on Windows for legacy reasons
 if (Platform::isWindows() && $matches['var'] == 'HOME') {
 return (Platform::getEnv('HOME') ?: Platform::getEnv('USERPROFILE')) . $matches['path'];
 }
 return Platform::getEnv($matches['var']) . $matches['path'];
 }, $path);
 }
 public static function getUserDirectory()
 {
 if (false !== ($home = self::getEnv('HOME'))) {
 return $home;
 }
 if (self::isWindows() && false !== ($home = self::getEnv('USERPROFILE'))) {
 return $home;
 }
 if (\function_exists('posix_getuid') && \function_exists('posix_getpwuid')) {
 $info = posix_getpwuid(posix_getuid());
 return $info['dir'];
 }
 throw new \RuntimeException('Could not determine user directory');
 }
 public static function isWindowsSubsystemForLinux()
 {
 if (null === self::$isWindowsSubsystemForLinux) {
 self::$isWindowsSubsystemForLinux = false;
 // while WSL will be hosted within windows, WSL itself cannot be windows based itself.
 if (self::isWindows()) {
 return self::$isWindowsSubsystemForLinux = false;
 }
 if (
 !ini_get('open_basedir')
 && is_readable('/proc/version')
 && false !== stripos(Silencer::call('file_get_contents', '/proc/version'), 'microsoft')
 && !file_exists('/.dockerenv') // docker running inside WSL should not be seen as WSL
 ) {
 return self::$isWindowsSubsystemForLinux = true;
 }
 }
 return self::$isWindowsSubsystemForLinux;
 }
 public static function isWindows()
 {
 return \defined('PHP_WINDOWS_VERSION_BUILD');
 }
 public static function strlen($str)
 {
 static $useMbString = null;
 if (null === $useMbString) {
 $useMbString = \function_exists('mb_strlen') && ini_get('mbstring.func_overload');
 }
 if ($useMbString) {
 return mb_strlen($str, '8bit');
 }
 return \strlen($str);
 }
 public static function isTty($fd = null)
 {
 if ($fd === null) {
 $fd = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
 }
 // detect msysgit/mingw and assume this is a tty because detection
 // does not work correctly, see https://github.com/composer/composer/issues/9690
 if (in_array(strtoupper(self::getEnv('MSYSTEM') ?: ''), array('MINGW32', 'MINGW64'), true)) {
 return true;
 }
 // modern cross-platform function, includes the fstat
 // fallback so if it is present we trust it
 if (function_exists('stream_isatty')) {
 return stream_isatty($fd);
 }
 // only trusting this if it is positive, otherwise prefer fstat fallback
 if (function_exists('posix_isatty') && posix_isatty($fd)) {
 return true;
 }
 $stat = @fstat($fd);
 // Check if formatted mode is S_IFCHR
 return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
 }
 public static function workaroundFilesystemIssues()
 {
 if (self::isVirtualBoxGuest()) {
 usleep(200000);
 }
 }
 private static function isVirtualBoxGuest()
 {
 if (null === self::$isVirtualBoxGuest) {
 self::$isVirtualBoxGuest = false;
 if (self::isWindows()) {
 return self::$isVirtualBoxGuest;
 }
 if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
 $processUser = posix_getpwuid(posix_geteuid());
 if ($processUser && $processUser['name'] === 'vagrant') {
 return self::$isVirtualBoxGuest = true;
 }
 }
 if (self::getEnv('COMPOSER_RUNTIME_ENV') === 'virtualbox') {
 return self::$isVirtualBoxGuest = true;
 }
 if (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Linux') {
 $process = new ProcessExecutor();
 try {
 if (0 === $process->execute('lsmod | grep vboxguest', $ignoredOutput)) {
 return self::$isVirtualBoxGuest = true;
 }
 } catch (\Exception $e) {
 // noop
 }
 }
 }
 return self::$isVirtualBoxGuest;
 }
 public static function getDevNull()
 {
 if (self::isWindows()) {
 return 'NUL';
 }
 return '/dev/null';
 }
}
