<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
interface ChangeReportInterface
{
 public function getLocalChanges(PackageInterface $package, $path);
}
