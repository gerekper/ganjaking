<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
interface ConfigurableRepositoryInterface
{
 public function getRepoConfig();
}
