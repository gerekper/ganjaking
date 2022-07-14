<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
use Composer\Util\ProcessExecutor;
class XzDownloader extends ArchiveDownloader
{
 protected function extract(PackageInterface $package, $file, $path)
 {
 $command = 'tar -xJf ' . ProcessExecutor::escape($file) . ' -C ' . ProcessExecutor::escape($path);
 if (0 === $this->process->execute($command, $ignoredOutput)) {
 return \React\Promise\resolve();
 }
 $processError = 'Failed to execute ' . $command . "\n\n" . $this->process->getErrorOutput();
 throw new \RuntimeException($processError);
 }
}
