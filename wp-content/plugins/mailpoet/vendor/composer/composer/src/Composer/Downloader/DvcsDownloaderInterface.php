<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
interface DvcsDownloaderInterface
{
 public function getUnpushedChanges(PackageInterface $package, $path);
}
