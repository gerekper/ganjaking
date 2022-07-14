<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
class PharDownloader extends ArchiveDownloader
{
 protected function extract(PackageInterface $package, $file, $path)
 {
 // Can throw an UnexpectedValueException
 $archive = new \Phar($file);
 $archive->extractTo($path, null, true);
 return \React\Promise\resolve();
 }
}
