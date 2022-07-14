<?php
namespace Symfony\Component\Filesystem;
if (!defined('ABSPATH')) exit;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\IOException;
class Filesystem
{
 private static $lastError;
 public function copy($originFile, $targetFile, $overwriteNewerFiles = false)
 {
 $originIsLocal = stream_is_local($originFile) || 0 === stripos($originFile, 'file://');
 if ($originIsLocal && !is_file($originFile)) {
 throw new FileNotFoundException(sprintf('Failed to copy "%s" because file does not exist.', $originFile), 0, null, $originFile);
 }
 $this->mkdir(\dirname($targetFile));
 $doCopy = true;
 if (!$overwriteNewerFiles && null === parse_url($originFile, \PHP_URL_HOST) && is_file($targetFile)) {
 $doCopy = filemtime($originFile) > filemtime($targetFile);
 }
 if ($doCopy) {
 // https://bugs.php.net/64634
 if (false === $source = @fopen($originFile, 'r')) {
 throw new IOException(sprintf('Failed to copy "%s" to "%s" because source file could not be opened for reading.', $originFile, $targetFile), 0, null, $originFile);
 }
 // Stream context created to allow files overwrite when using FTP stream wrapper - disabled by default
 if (false === $target = @fopen($targetFile, 'w', false, stream_context_create(['ftp' => ['overwrite' => true]]))) {
 throw new IOException(sprintf('Failed to copy "%s" to "%s" because target file could not be opened for writing.', $originFile, $targetFile), 0, null, $originFile);
 }
 $bytesCopied = stream_copy_to_stream($source, $target);
 fclose($source);
 fclose($target);
 unset($source, $target);
 if (!is_file($targetFile)) {
 throw new IOException(sprintf('Failed to copy "%s" to "%s".', $originFile, $targetFile), 0, null, $originFile);
 }
 if ($originIsLocal) {
 // Like `cp`, preserve executable permission bits
 @chmod($targetFile, fileperms($targetFile) | (fileperms($originFile) & 0111));
 if ($bytesCopied !== $bytesOrigin = filesize($originFile)) {
 throw new IOException(sprintf('Failed to copy the whole content of "%s" to "%s" (%g of %g bytes copied).', $originFile, $targetFile, $bytesCopied, $bytesOrigin), 0, null, $originFile);
 }
 }
 }
 }
 public function mkdir($dirs, $mode = 0777)
 {
 foreach ($this->toIterable($dirs) as $dir) {
 if (is_dir($dir)) {
 continue;
 }
 if (!self::box('mkdir', $dir, $mode, true)) {
 if (!is_dir($dir)) {
 // The directory was not created by a concurrent process. Let's throw an exception with a developer friendly error message if we have one
 if (self::$lastError) {
 throw new IOException(sprintf('Failed to create "%s": ', $dir).self::$lastError, 0, null, $dir);
 }
 throw new IOException(sprintf('Failed to create "%s".', $dir), 0, null, $dir);
 }
 }
 }
 }
 public function exists($files)
 {
 $maxPathLength = \PHP_MAXPATHLEN - 2;
 foreach ($this->toIterable($files) as $file) {
 if (\strlen($file) > $maxPathLength) {
 throw new IOException(sprintf('Could not check if file exist because path length exceeds %d characters.', $maxPathLength), 0, null, $file);
 }
 if (!file_exists($file)) {
 return false;
 }
 }
 return true;
 }
 public function touch($files, $time = null, $atime = null)
 {
 foreach ($this->toIterable($files) as $file) {
 $touch = $time ? @touch($file, $time, $atime) : @touch($file);
 if (true !== $touch) {
 throw new IOException(sprintf('Failed to touch "%s".', $file), 0, null, $file);
 }
 }
 }
 public function remove($files)
 {
 if ($files instanceof \Traversable) {
 $files = iterator_to_array($files, false);
 } elseif (!\is_array($files)) {
 $files = [$files];
 }
 $files = array_reverse($files);
 foreach ($files as $file) {
 if (is_link($file)) {
 // See https://bugs.php.net/52176
 if (!(self::box('unlink', $file) || '\\' !== \DIRECTORY_SEPARATOR || self::box('rmdir', $file)) && file_exists($file)) {
 throw new IOException(sprintf('Failed to remove symlink "%s": ', $file).self::$lastError);
 }
 } elseif (is_dir($file)) {
 $this->remove(new \FilesystemIterator($file, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS));
 if (!self::box('rmdir', $file) && file_exists($file)) {
 throw new IOException(sprintf('Failed to remove directory "%s": ', $file).self::$lastError);
 }
 } elseif (!self::box('unlink', $file) && (str_contains(self::$lastError, 'Permission denied') || file_exists($file))) {
 throw new IOException(sprintf('Failed to remove file "%s": ', $file).self::$lastError);
 }
 }
 }
 public function chmod($files, $mode, $umask = 0000, $recursive = false)
 {
 foreach ($this->toIterable($files) as $file) {
 if ((\PHP_VERSION_ID < 80000 || \is_int($mode)) && true !== @chmod($file, $mode & ~$umask)) {
 throw new IOException(sprintf('Failed to chmod file "%s".', $file), 0, null, $file);
 }
 if ($recursive && is_dir($file) && !is_link($file)) {
 $this->chmod(new \FilesystemIterator($file), $mode, $umask, true);
 }
 }
 }
 public function chown($files, $user, $recursive = false)
 {
 foreach ($this->toIterable($files) as $file) {
 if ($recursive && is_dir($file) && !is_link($file)) {
 $this->chown(new \FilesystemIterator($file), $user, true);
 }
 if (is_link($file) && \function_exists('lchown')) {
 if (true !== @lchown($file, $user)) {
 throw new IOException(sprintf('Failed to chown file "%s".', $file), 0, null, $file);
 }
 } else {
 if (true !== @chown($file, $user)) {
 throw new IOException(sprintf('Failed to chown file "%s".', $file), 0, null, $file);
 }
 }
 }
 }
 public function chgrp($files, $group, $recursive = false)
 {
 foreach ($this->toIterable($files) as $file) {
 if ($recursive && is_dir($file) && !is_link($file)) {
 $this->chgrp(new \FilesystemIterator($file), $group, true);
 }
 if (is_link($file) && \function_exists('lchgrp')) {
 if (true !== @lchgrp($file, $group)) {
 throw new IOException(sprintf('Failed to chgrp file "%s".', $file), 0, null, $file);
 }
 } else {
 if (true !== @chgrp($file, $group)) {
 throw new IOException(sprintf('Failed to chgrp file "%s".', $file), 0, null, $file);
 }
 }
 }
 }
 public function rename($origin, $target, $overwrite = false)
 {
 // we check that target does not exist
 if (!$overwrite && $this->isReadable($target)) {
 throw new IOException(sprintf('Cannot rename because the target "%s" already exists.', $target), 0, null, $target);
 }
 if (true !== @rename($origin, $target)) {
 if (is_dir($origin)) {
 // See https://bugs.php.net/54097 & https://php.net/rename#113943
 $this->mirror($origin, $target, null, ['override' => $overwrite, 'delete' => $overwrite]);
 $this->remove($origin);
 return;
 }
 throw new IOException(sprintf('Cannot rename "%s" to "%s".', $origin, $target), 0, null, $target);
 }
 }
 private function isReadable(string $filename): bool
 {
 $maxPathLength = \PHP_MAXPATHLEN - 2;
 if (\strlen($filename) > $maxPathLength) {
 throw new IOException(sprintf('Could not check if file is readable because path length exceeds %d characters.', $maxPathLength), 0, null, $filename);
 }
 return is_readable($filename);
 }
 public function symlink($originDir, $targetDir, $copyOnWindows = false)
 {
 self::assertFunctionExists('symlink');
 if ('\\' === \DIRECTORY_SEPARATOR) {
 $originDir = strtr($originDir, '/', '\\');
 $targetDir = strtr($targetDir, '/', '\\');
 if ($copyOnWindows) {
 $this->mirror($originDir, $targetDir);
 return;
 }
 }
 $this->mkdir(\dirname($targetDir));
 if (is_link($targetDir)) {
 if (readlink($targetDir) === $originDir) {
 return;
 }
 $this->remove($targetDir);
 }
 if (!self::box('symlink', $originDir, $targetDir)) {
 $this->linkException($originDir, $targetDir, 'symbolic');
 }
 }
 public function hardlink($originFile, $targetFiles)
 {
 self::assertFunctionExists('link');
 if (!$this->exists($originFile)) {
 throw new FileNotFoundException(null, 0, null, $originFile);
 }
 if (!is_file($originFile)) {
 throw new FileNotFoundException(sprintf('Origin file "%s" is not a file.', $originFile));
 }
 foreach ($this->toIterable($targetFiles) as $targetFile) {
 if (is_file($targetFile)) {
 if (fileinode($originFile) === fileinode($targetFile)) {
 continue;
 }
 $this->remove($targetFile);
 }
 if (!self::box('link', $originFile, $targetFile)) {
 $this->linkException($originFile, $targetFile, 'hard');
 }
 }
 }
 private function linkException(string $origin, string $target, string $linkType)
 {
 if (self::$lastError) {
 if ('\\' === \DIRECTORY_SEPARATOR && str_contains(self::$lastError, 'error code(1314)')) {
 throw new IOException(sprintf('Unable to create "%s" link due to error code 1314: \'A required privilege is not held by the client\'. Do you have the required Administrator-rights?', $linkType), 0, null, $target);
 }
 }
 throw new IOException(sprintf('Failed to create "%s" link from "%s" to "%s".', $linkType, $origin, $target), 0, null, $target);
 }
 public function readlink($path, $canonicalize = false)
 {
 if (!$canonicalize && !is_link($path)) {
 return null;
 }
 if ($canonicalize) {
 if (!$this->exists($path)) {
 return null;
 }
 if ('\\' === \DIRECTORY_SEPARATOR && \PHP_VERSION_ID < 70410) {
 $path = readlink($path);
 }
 return realpath($path);
 }
 if ('\\' === \DIRECTORY_SEPARATOR && \PHP_VERSION_ID < 70400) {
 return realpath($path);
 }
 return readlink($path);
 }
 public function makePathRelative($endPath, $startPath)
 {
 if (!$this->isAbsolutePath($startPath)) {
 throw new InvalidArgumentException(sprintf('The start path "%s" is not absolute.', $startPath));
 }
 if (!$this->isAbsolutePath($endPath)) {
 throw new InvalidArgumentException(sprintf('The end path "%s" is not absolute.', $endPath));
 }
 // Normalize separators on Windows
 if ('\\' === \DIRECTORY_SEPARATOR) {
 $endPath = str_replace('\\', '/', $endPath);
 $startPath = str_replace('\\', '/', $startPath);
 }
 $splitDriveLetter = function ($path) {
 return (\strlen($path) > 2 && ':' === $path[1] && '/' === $path[2] && ctype_alpha($path[0]))
 ? [substr($path, 2), strtoupper($path[0])]
 : [$path, null];
 };
 $splitPath = function ($path) {
 $result = [];
 foreach (explode('/', trim($path, '/')) as $segment) {
 if ('..' === $segment) {
 array_pop($result);
 } elseif ('.' !== $segment && '' !== $segment) {
 $result[] = $segment;
 }
 }
 return $result;
 };
 [$endPath, $endDriveLetter] = $splitDriveLetter($endPath);
 [$startPath, $startDriveLetter] = $splitDriveLetter($startPath);
 $startPathArr = $splitPath($startPath);
 $endPathArr = $splitPath($endPath);
 if ($endDriveLetter && $startDriveLetter && $endDriveLetter != $startDriveLetter) {
 // End path is on another drive, so no relative path exists
 return $endDriveLetter.':/'.($endPathArr ? implode('/', $endPathArr).'/' : '');
 }
 // Find for which directory the common path stops
 $index = 0;
 while (isset($startPathArr[$index]) && isset($endPathArr[$index]) && $startPathArr[$index] === $endPathArr[$index]) {
 ++$index;
 }
 // Determine how deep the start path is relative to the common path (ie, "web/bundles" = 2 levels)
 if (1 === \count($startPathArr) && '' === $startPathArr[0]) {
 $depth = 0;
 } else {
 $depth = \count($startPathArr) - $index;
 }
 // Repeated "../" for each level need to reach the common path
 $traverser = str_repeat('../', $depth);
 $endPathRemainder = implode('/', \array_slice($endPathArr, $index));
 // Construct $endPath from traversing to the common path, then to the remaining $endPath
 $relativePath = $traverser.('' !== $endPathRemainder ? $endPathRemainder.'/' : '');
 return '' === $relativePath ? './' : $relativePath;
 }
 public function mirror($originDir, $targetDir, \Traversable $iterator = null, $options = [])
 {
 $targetDir = rtrim($targetDir, '/\\');
 $originDir = rtrim($originDir, '/\\');
 $originDirLen = \strlen($originDir);
 if (!$this->exists($originDir)) {
 throw new IOException(sprintf('The origin directory specified "%s" was not found.', $originDir), 0, null, $originDir);
 }
 // Iterate in destination folder to remove obsolete entries
 if ($this->exists($targetDir) && isset($options['delete']) && $options['delete']) {
 $deleteIterator = $iterator;
 if (null === $deleteIterator) {
 $flags = \FilesystemIterator::SKIP_DOTS;
 $deleteIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($targetDir, $flags), \RecursiveIteratorIterator::CHILD_FIRST);
 }
 $targetDirLen = \strlen($targetDir);
 foreach ($deleteIterator as $file) {
 $origin = $originDir.substr($file->getPathname(), $targetDirLen);
 if (!$this->exists($origin)) {
 $this->remove($file);
 }
 }
 }
 $copyOnWindows = $options['copy_on_windows'] ?? false;
 if (null === $iterator) {
 $flags = $copyOnWindows ? \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS : \FilesystemIterator::SKIP_DOTS;
 $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($originDir, $flags), \RecursiveIteratorIterator::SELF_FIRST);
 }
 $this->mkdir($targetDir);
 $filesCreatedWhileMirroring = [];
 foreach ($iterator as $file) {
 if ($file->getPathname() === $targetDir || $file->getRealPath() === $targetDir || isset($filesCreatedWhileMirroring[$file->getRealPath()])) {
 continue;
 }
 $target = $targetDir.substr($file->getPathname(), $originDirLen);
 $filesCreatedWhileMirroring[$target] = true;
 if (!$copyOnWindows && is_link($file)) {
 $this->symlink($file->getLinkTarget(), $target);
 } elseif (is_dir($file)) {
 $this->mkdir($target);
 } elseif (is_file($file)) {
 $this->copy($file, $target, $options['override'] ?? false);
 } else {
 throw new IOException(sprintf('Unable to guess "%s" file type.', $file), 0, null, $file);
 }
 }
 }
 public function isAbsolutePath($file)
 {
 if (null === $file) {
 @trigger_error(sprintf('Calling "%s()" with a null in the $file argument is deprecated since Symfony 4.4.', __METHOD__), \E_USER_DEPRECATED);
 }
 return '' !== (string) $file && (strspn($file, '/\\', 0, 1)
 || (\strlen($file) > 3 && ctype_alpha($file[0])
 && ':' === $file[1]
 && strspn($file, '/\\', 2, 1)
 )
 || null !== parse_url($file, \PHP_URL_SCHEME)
 );
 }
 public function tempnam($dir, $prefix)
 {
 [$scheme, $hierarchy] = $this->getSchemeAndHierarchy($dir);
 // If no scheme or scheme is "file" or "gs" (Google Cloud) create temp file in local filesystem
 if (null === $scheme || 'file' === $scheme || 'gs' === $scheme) {
 $tmpFile = @tempnam($hierarchy, $prefix);
 // If tempnam failed or no scheme return the filename otherwise prepend the scheme
 if (false !== $tmpFile) {
 if (null !== $scheme && 'gs' !== $scheme) {
 return $scheme.'://'.$tmpFile;
 }
 return $tmpFile;
 }
 throw new IOException('A temporary file could not be created.');
 }
 // Loop until we create a valid temp file or have reached 10 attempts
 for ($i = 0; $i < 10; ++$i) {
 // Create a unique filename
 $tmpFile = $dir.'/'.$prefix.uniqid(mt_rand(), true);
 // Use fopen instead of file_exists as some streams do not support stat
 // Use mode 'x+' to atomically check existence and create to avoid a TOCTOU vulnerability
 $handle = @fopen($tmpFile, 'x+');
 // If unsuccessful restart the loop
 if (false === $handle) {
 continue;
 }
 // Close the file if it was successfully opened
 @fclose($handle);
 return $tmpFile;
 }
 throw new IOException('A temporary file could not be created.');
 }
 public function dumpFile($filename, $content)
 {
 if (\is_array($content)) {
 @trigger_error(sprintf('Calling "%s()" with an array in the $content argument is deprecated since Symfony 4.3.', __METHOD__), \E_USER_DEPRECATED);
 }
 $dir = \dirname($filename);
 if (!is_dir($dir)) {
 $this->mkdir($dir);
 }
 // Will create a temp file with 0600 access rights
 // when the filesystem supports chmod.
 $tmpFile = $this->tempnam($dir, basename($filename));
 try {
 if (false === @file_put_contents($tmpFile, $content)) {
 throw new IOException(sprintf('Failed to write file "%s".', $filename), 0, null, $filename);
 }
 @chmod($tmpFile, file_exists($filename) ? fileperms($filename) : 0666 & ~umask());
 $this->rename($tmpFile, $filename, true);
 } finally {
 if (file_exists($tmpFile)) {
 @unlink($tmpFile);
 }
 }
 }
 public function appendToFile($filename, $content)
 {
 if (\is_array($content)) {
 @trigger_error(sprintf('Calling "%s()" with an array in the $content argument is deprecated since Symfony 4.3.', __METHOD__), \E_USER_DEPRECATED);
 }
 $dir = \dirname($filename);
 if (!is_dir($dir)) {
 $this->mkdir($dir);
 }
 if (false === @file_put_contents($filename, $content, \FILE_APPEND)) {
 throw new IOException(sprintf('Failed to write file "%s".', $filename), 0, null, $filename);
 }
 }
 private function toIterable($files): iterable
 {
 return is_iterable($files) ? $files : [$files];
 }
 private function getSchemeAndHierarchy(string $filename): array
 {
 $components = explode('://', $filename, 2);
 return 2 === \count($components) ? [$components[0], $components[1]] : [null, $components[0]];
 }
 private static function assertFunctionExists(string $func): void
 {
 if (!\function_exists($func)) {
 throw new IOException(sprintf('Unable to perform filesystem operation because the "%s()" function has been disabled.', $func));
 }
 }
 private static function box(string $func, ...$args)
 {
 self::assertFunctionExists($func);
 self::$lastError = null;
 set_error_handler(__CLASS__.'::handleError');
 try {
 $result = $func(...$args);
 restore_error_handler();
 return $result;
 } catch (\Throwable $e) {
 }
 restore_error_handler();
 throw $e;
 }
 public static function handleError(int $type, string $msg)
 {
 self::$lastError = $msg;
 }
}
