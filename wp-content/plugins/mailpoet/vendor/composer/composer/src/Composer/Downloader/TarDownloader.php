<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
class TarDownloader extends ArchiveDownloader
{
 protected function extract(PackageInterface $package, $file, $path)
 {
 // Can throw an UnexpectedValueException
 $archive = new \PharData($file);
 $archive->extractTo($path, null, true);
 return \React\Promise\resolve();
 }
}
