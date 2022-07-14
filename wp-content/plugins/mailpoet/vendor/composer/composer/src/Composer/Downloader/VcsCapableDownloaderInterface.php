<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
interface VcsCapableDownloaderInterface
{
 public function getVcsReference(PackageInterface $package, $path);
}
