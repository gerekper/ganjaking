<?php
namespace Composer\Util;
if (!defined('ABSPATH')) exit;
use Composer\Pcre\Preg;
use React\Promise\PromiseInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;
class Filesystem
{
 private $processExecutor;
 public function __construct(ProcessExecutor $executor = null)
 {
 $this->processExecutor = $executor;
 }
 public function remove($file)
 {
 if (is_dir($file)) {
 return $this->removeDirectory($file);
 }
 if (file_exists($file)) {
 return $this->unlink($file);
 }
 return false;
 }
 public function isDirEmpty($dir)
 {
 $finder = Finder::create()
 ->ignoreVCS(false)
 ->ignoreDotFiles(false)
 ->depth(0)
 ->in($dir);
 return \count($finder) === 0;
 }
 public function emptyDirectory($dir, $ensureDirectoryExists = true)
 {
 if (is_link($dir) && file_exists($dir)) {
 $this->unlink($dir);
 }
 if ($ensureDirectoryExists) {
 $this->ensureDirectoryExists($dir);
 }
 if (is_dir($dir)) {
 $finder = Finder::create()
 ->ignoreVCS(false)
 ->ignoreDotFiles(false)
 ->depth(0)
 ->in($dir);
 foreach ($finder as $path) {
 $this->remove((string) $path);
 }
 }
 }
 public function removeDirectory($directory)
 {
 $edgeCaseResult = $this->removeEdgeCases($directory);
 if ($edgeCaseResult !== null) {
 return $edgeCaseResult;
 }
 if (Platform::isWindows()) {
 $cmd = sprintf('rmdir /S /Q %s', ProcessExecutor::escape(realpath($directory)));
 } else {
 $cmd = sprintf('rm -rf %s', ProcessExecutor::escape($directory));
 }
 $result = $this->getProcess()->execute($cmd, $output) === 0;
 // clear stat cache because external processes aren't tracked by the php stat cache
 clearstatcache();
 if ($result && !is_dir($directory)) {
 return true;
 }
 return $this->removeDirectoryPhp($directory);
 }
 public function removeDirectoryAsync($directory)
 {
 $edgeCaseResult = $this->removeEdgeCases($directory);
 if ($edgeCaseResult !== null) {
 return \React\Promise\resolve($edgeCaseResult);
 }
 if (Platform::isWindows()) {
 $cmd = sprintf('rmdir /S /Q %s', ProcessExecutor::escape(realpath($directory)));
 } else {
 $cmd = sprintf('rm -rf %s', ProcessExecutor::escape($directory));
 }
 $promise = $this->getProcess()->executeAsync($cmd);
 $self = $this;
 return $promise->then(function ($process) use ($directory, $self) {
 // clear stat cache because external processes aren't tracked by the php stat cache
 clearstatcache();
 if ($process->isSuccessful()) {
 if (!is_dir($directory)) {
 return \React\Promise\resolve(true);
 }
 }
 return \React\Promise\resolve($self->removeDirectoryPhp($directory));
 });
 }
 private function removeEdgeCases($directory, $fallbackToPhp = true)
 {
 if ($this->isSymlinkedDirectory($directory)) {
 return $this->unlinkSymlinkedDirectory($directory);
 }
 if ($this->isJunction($directory)) {
 return $this->removeJunction($directory);
 }
 if (is_link($directory)) {
 return unlink($directory);
 }
 if (!is_dir($directory) || !file_exists($directory)) {
 return true;
 }
 if (Preg::isMatch('{^(?:[a-z]:)?[/\\\\]+$}i', $directory)) {
 throw new \RuntimeException('Aborting an attempted deletion of '.$directory.', this was probably not intended, if it is a real use case please report it.');
 }
 if (!\function_exists('proc_open') && $fallbackToPhp) {
 return $this->removeDirectoryPhp($directory);
 }
 return null;
 }
 public function removeDirectoryPhp($directory)
 {
 $edgeCaseResult = $this->removeEdgeCases($directory, false);
 if ($edgeCaseResult !== null) {
 return $edgeCaseResult;
 }
 try {
 $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
 } catch (\UnexpectedValueException $e) {
 // re-try once after clearing the stat cache if it failed as it
 // sometimes fails without apparent reason, see https://github.com/composer/composer/issues/4009
 clearstatcache();
 usleep(100000);
 if (!is_dir($directory)) {
 return true;
 }
 $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
 }
 $ri = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
 foreach ($ri as $file) {
 if ($file->isDir()) {
 $this->rmdir($file->getPathname());
 } else {
 $this->unlink($file->getPathname());
 }
 }
 // release locks on the directory, see https://github.com/composer/composer/issues/9945
 unset($ri, $it, $file);
 return $this->rmdir($directory);
 }
 public function ensureDirectoryExists($directory)
 {
 if (!is_dir($directory)) {
 if (file_exists($directory)) {
 throw new \RuntimeException(
 $directory.' exists and is not a directory.'
 );
 }
 if (!@mkdir($directory, 0777, true)) {
 throw new \RuntimeException(
 $directory.' does not exist and could not be created.'
 );
 }
 }
 }
 public function unlink($path)
 {
 $unlinked = @$this->unlinkImplementation($path);
 if (!$unlinked) {
 // retry after a bit on windows since it tends to be touchy with mass removals
 if (Platform::isWindows()) {
 usleep(350000);
 $unlinked = @$this->unlinkImplementation($path);
 }
 if (!$unlinked) {
 $error = error_get_last();
 $message = 'Could not delete '.$path.': ' . @$error['message'];
 if (Platform::isWindows()) {
 $message .= "\nThis can be due to an antivirus or the Windows Search Indexer locking the file while they are analyzed";
 }
 throw new \RuntimeException($message);
 }
 }
 return true;
 }
 public function rmdir($path)
 {
 $deleted = @rmdir($path);
 if (!$deleted) {
 // retry after a bit on windows since it tends to be touchy with mass removals
 if (Platform::isWindows()) {
 usleep(350000);
 $deleted = @rmdir($path);
 }
 if (!$deleted) {
 $error = error_get_last();
 $message = 'Could not delete '.$path.': ' . @$error['message'];
 if (Platform::isWindows()) {
 $message .= "\nThis can be due to an antivirus or the Windows Search Indexer locking the file while they are analyzed";
 }
 throw new \RuntimeException($message);
 }
 }
 return true;
 }
 public function copyThenRemove($source, $target)
 {
 $this->copy($source, $target);
 if (!is_dir($source)) {
 $this->unlink($source);
 return;
 }
 $this->removeDirectoryPhp($source);
 }
 public function copy($source, $target)
 {
 if (!is_dir($source)) {
 return copy($source, $target);
 }
 $it = new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS);
 $ri = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::SELF_FIRST);
 $this->ensureDirectoryExists($target);
 $result = true;
 foreach ($ri as $file) {
 $targetPath = $target . DIRECTORY_SEPARATOR . $ri->getSubPathname();
 if ($file->isDir()) {
 $this->ensureDirectoryExists($targetPath);
 } else {
 $result = $result && copy($file->getPathname(), $targetPath);
 }
 }
 return $result;
 }
 public function rename($source, $target)
 {
 if (true === @rename($source, $target)) {
 return;
 }
 if (!\function_exists('proc_open')) {
 $this->copyThenRemove($source, $target);
 return;
 }
 if (Platform::isWindows()) {
 // Try to copy & delete - this is a workaround for random "Access denied" errors.
 $command = sprintf('xcopy %s %s /E /I /Q /Y', ProcessExecutor::escape($source), ProcessExecutor::escape($target));
 $result = $this->getProcess()->execute($command, $output);
 // clear stat cache because external processes aren't tracked by the php stat cache
 clearstatcache();
 if (0 === $result) {
 $this->remove($source);
 return;
 }
 } else {
 // We do not use PHP's "rename" function here since it does not support
 // the case where $source, and $target are located on different partitions.
 $command = sprintf('mv %s %s', ProcessExecutor::escape($source), ProcessExecutor::escape($target));
 $result = $this->getProcess()->execute($command, $output);
 // clear stat cache because external processes aren't tracked by the php stat cache
 clearstatcache();
 if (0 === $result) {
 return;
 }
 }
 $this->copyThenRemove($source, $target);
 }
 public function findShortestPath($from, $to, $directories = false)
 {
 if (!$this->isAbsolutePath($from) || !$this->isAbsolutePath($to)) {
 throw new \InvalidArgumentException(sprintf('$from (%s) and $to (%s) must be absolute paths.', $from, $to));
 }
 $from = lcfirst($this->normalizePath($from));
 $to = lcfirst($this->normalizePath($to));
 if ($directories) {
 $from = rtrim($from, '/') . '/dummy_file';
 }
 if (\dirname($from) === \dirname($to)) {
 return './'.basename($to);
 }
 $commonPath = $to;
 while (strpos($from.'/', $commonPath.'/') !== 0 && '/' !== $commonPath && !Preg::isMatch('{^[a-z]:/?$}i', $commonPath)) {
 $commonPath = strtr(\dirname($commonPath), '\\', '/');
 }
 if (0 !== strpos($from, $commonPath) || '/' === $commonPath) {
 return $to;
 }
 $commonPath = rtrim($commonPath, '/') . '/';
 $sourcePathDepth = substr_count(substr($from, \strlen($commonPath)), '/');
 $commonPathCode = str_repeat('../', $sourcePathDepth);
 return ($commonPathCode . substr($to, \strlen($commonPath))) ?: './';
 }
 public function findShortestPathCode($from, $to, $directories = false, $staticCode = false)
 {
 if (!$this->isAbsolutePath($from) || !$this->isAbsolutePath($to)) {
 throw new \InvalidArgumentException(sprintf('$from (%s) and $to (%s) must be absolute paths.', $from, $to));
 }
 $from = lcfirst($this->normalizePath($from));
 $to = lcfirst($this->normalizePath($to));
 if ($from === $to) {
 return $directories ? '__DIR__' : '__FILE__';
 }
 $commonPath = $to;
 while (strpos($from.'/', $commonPath.'/') !== 0 && '/' !== $commonPath && !Preg::isMatch('{^[a-z]:/?$}i', $commonPath) && '.' !== $commonPath) {
 $commonPath = strtr(\dirname($commonPath), '\\', '/');
 }
 if (0 !== strpos($from, $commonPath) || '/' === $commonPath || '.' === $commonPath) {
 return var_export($to, true);
 }
 $commonPath = rtrim($commonPath, '/') . '/';
 if (strpos($to, $from.'/') === 0) {
 return '__DIR__ . '.var_export(substr($to, \strlen($from)), true);
 }
 $sourcePathDepth = substr_count(substr($from, \strlen($commonPath)), '/') + $directories;
 if ($staticCode) {
 $commonPathCode = "__DIR__ . '".str_repeat('/..', $sourcePathDepth)."'";
 } else {
 $commonPathCode = str_repeat('dirname(', $sourcePathDepth).'__DIR__'.str_repeat(')', $sourcePathDepth);
 }
 $relTarget = substr($to, \strlen($commonPath));
 return $commonPathCode . (\strlen($relTarget) ? '.' . var_export('/' . $relTarget, true) : '');
 }
 public function isAbsolutePath($path)
 {
 return strpos($path, '/') === 0 || substr($path, 1, 1) === ':' || strpos($path, '\\\\') === 0;
 }
 public function size($path)
 {
 if (!file_exists($path)) {
 throw new \RuntimeException("$path does not exist.");
 }
 if (is_dir($path)) {
 return $this->directorySize($path);
 }
 return filesize($path);
 }
 public function normalizePath($path)
 {
 $parts = array();
 $path = strtr($path, '\\', '/');
 $prefix = '';
 $absolute = '';
 // extract windows UNC paths e.g. \\foo\bar
 if (strpos($path, '//') === 0 && \strlen($path) > 2) {
 $absolute = '//';
 $path = substr($path, 2);
 }
 // extract a prefix being a protocol://, protocol:, protocol://drive: or simply drive:
 if (Preg::isMatch('{^( [0-9a-z]{2,}+: (?: // (?: [a-z]: )? )? | [a-z]: )}ix', $path, $match)) {
 $prefix = $match[1];
 $path = substr($path, \strlen($prefix));
 }
 if (strpos($path, '/') === 0) {
 $absolute = '/';
 $path = substr($path, 1);
 }
 $up = false;
 foreach (explode('/', $path) as $chunk) {
 if ('..' === $chunk && ($absolute !== '' || $up)) {
 array_pop($parts);
 $up = !(empty($parts) || '..' === end($parts));
 } elseif ('.' !== $chunk && '' !== $chunk) {
 $parts[] = $chunk;
 $up = '..' !== $chunk;
 }
 }
 return $prefix.((string) $absolute).implode('/', $parts);
 }
 public static function trimTrailingSlash($path)
 {
 if (!Preg::isMatch('{^[/\\\\]+$}', $path)) {
 $path = rtrim($path, '/\\');
 }
 return $path;
 }
 public static function isLocalPath($path)
 {
 return Preg::isMatch('{^(file://(?!//)|/(?!/)|/?[a-z]:[\\\\/]|\.\.[\\\\/]|[a-z0-9_.-]+[\\\\/])}i', $path);
 }
 public static function getPlatformPath($path)
 {
 if (Platform::isWindows()) {
 $path = Preg::replace('{^(?:file:///([a-z]):?/)}i', 'file://$1:/', $path);
 }
 return (string) Preg::replace('{^file://}i', '', $path);
 }
 public static function isReadable($path)
 {
 if (is_readable($path)) {
 return true;
 }
 if (is_file($path)) {
 return false !== Silencer::call('file_get_contents', $path, false, null, 0, 1);
 }
 if (is_dir($path)) {
 return false !== Silencer::call('opendir', $path);
 }
 // assume false otherwise
 return false;
 }
 protected function directorySize($directory)
 {
 $it = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
 $ri = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
 $size = 0;
 foreach ($ri as $file) {
 if ($file->isFile()) {
 $size += $file->getSize();
 }
 }
 return $size;
 }
 protected function getProcess()
 {
 if (!$this->processExecutor) {
 $this->processExecutor = new ProcessExecutor();
 }
 return $this->processExecutor;
 }
 private function unlinkImplementation($path)
 {
 if (Platform::isWindows() && is_dir($path) && is_link($path)) {
 return rmdir($path);
 }
 return unlink($path);
 }
 public function relativeSymlink($target, $link)
 {
 if (!function_exists('symlink')) {
 return false;
 }
 $cwd = getcwd();
 $relativePath = $this->findShortestPath($link, $target);
 chdir(\dirname($link));
 $result = @symlink($relativePath, $link);
 chdir($cwd);
 return $result;
 }
 public function isSymlinkedDirectory($directory)
 {
 if (!is_dir($directory)) {
 return false;
 }
 $resolved = $this->resolveSymlinkedDirectorySymlink($directory);
 return is_link($resolved);
 }
 private function unlinkSymlinkedDirectory($directory)
 {
 $resolved = $this->resolveSymlinkedDirectorySymlink($directory);
 return $this->unlink($resolved);
 }
 private function resolveSymlinkedDirectorySymlink($pathname)
 {
 if (!is_dir($pathname)) {
 return $pathname;
 }
 $resolved = rtrim($pathname, '/');
 if (!\strlen($resolved)) {
 return $pathname;
 }
 return $resolved;
 }
 public function junction($target, $junction)
 {
 if (!Platform::isWindows()) {
 throw new \LogicException(sprintf('Function %s is not available on non-Windows platform', __CLASS__));
 }
 if (!is_dir($target)) {
 throw new IOException(sprintf('Cannot junction to "%s" as it is not a directory.', $target), 0, null, $target);
 }
 $cmd = sprintf(
 'mklink /J %s %s',
 ProcessExecutor::escape(str_replace('/', DIRECTORY_SEPARATOR, $junction)),
 ProcessExecutor::escape(realpath($target))
 );
 if ($this->getProcess()->execute($cmd, $output) !== 0) {
 throw new IOException(sprintf('Failed to create junction to "%s" at "%s".', $target, $junction), 0, null, $target);
 }
 clearstatcache(true, $junction);
 }
 public function isJunction($junction)
 {
 if (!Platform::isWindows()) {
 return false;
 }
 // Important to clear all caches first
 clearstatcache(true, $junction);
 if (!is_dir($junction) || is_link($junction)) {
 return false;
 }
 $stat = lstat($junction);
 // S_ISDIR test (S_IFDIR is 0x4000, S_IFMT is 0xF000 bitmask)
 return $stat ? 0x4000 !== ($stat['mode'] & 0xF000) : false;
 }
 public function removeJunction($junction)
 {
 if (!Platform::isWindows()) {
 return false;
 }
 $junction = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $junction), DIRECTORY_SEPARATOR);
 if (!$this->isJunction($junction)) {
 throw new IOException(sprintf('%s is not a junction and thus cannot be removed as one', $junction));
 }
 return $this->rmdir($junction);
 }
 public function filePutContentsIfModified($path, $content)
 {
 $currentContent = @file_get_contents($path);
 if (!$currentContent || ($currentContent != $content)) {
 return file_put_contents($path, $content);
 }
 return 0;
 }
 public function safeCopy($source, $target)
 {
 if (!file_exists($target) || !file_exists($source) || !$this->filesAreEqual($source, $target)) {
 $source = fopen($source, 'r');
 $target = fopen($target, 'w+');
 stream_copy_to_stream($source, $target);
 fclose($source);
 fclose($target);
 }
 }
 private function filesAreEqual($a, $b)
 {
 // Check if filesize is different
 if (filesize($a) !== filesize($b)) {
 return false;
 }
 // Check if content is different
 $ah = fopen($a, 'rb');
 $bh = fopen($b, 'rb');
 $result = true;
 while (!feof($ah)) {
 if (fread($ah, 8192) != fread($bh, 8192)) {
 $result = false;
 break;
 }
 }
 fclose($ah);
 fclose($bh);
 return $result;
 }
}
