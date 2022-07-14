<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
use Composer\Util\Platform;
use Composer\Util\ProcessExecutor;
class GzipDownloader extends ArchiveDownloader
{
 protected function extract(PackageInterface $package, $file, $path)
 {
 $filename = pathinfo(parse_url($package->getDistUrl(), PHP_URL_PATH), PATHINFO_FILENAME);
 $targetFilepath = $path . DIRECTORY_SEPARATOR . $filename;
 // Try to use gunzip on *nix
 if (!Platform::isWindows()) {
 $command = 'gzip -cd -- ' . ProcessExecutor::escape($file) . ' > ' . ProcessExecutor::escape($targetFilepath);
 if (0 === $this->process->execute($command, $ignoredOutput)) {
 return \React\Promise\resolve();
 }
 if (extension_loaded('zlib')) {
 // Fallback to using the PHP extension.
 $this->extractUsingExt($file, $targetFilepath);
 return \React\Promise\resolve();
 }
 $processError = 'Failed to execute ' . $command . "\n\n" . $this->process->getErrorOutput();
 throw new \RuntimeException($processError);
 }
 // Windows version of PHP has built-in support of gzip functions
 $this->extractUsingExt($file, $targetFilepath);
 return \React\Promise\resolve();
 }
 private function extractUsingExt($file, $targetFilepath)
 {
 $archiveFile = gzopen($file, 'rb');
 $targetFile = fopen($targetFilepath, 'wb');
 while ($string = gzread($archiveFile, 4096)) {
 fwrite($targetFile, $string, Platform::strlen($string));
 }
 gzclose($archiveFile);
 fclose($targetFile);
 }
}
