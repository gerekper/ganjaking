<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
interface VersionCacheInterface
{
 public function getVersionPackage($version, $identifier);
}
