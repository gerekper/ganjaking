<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
interface InstalledRepositoryInterface extends WritableRepositoryInterface
{
 public function getDevMode();
 public function isFresh();
}
