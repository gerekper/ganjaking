<?php
namespace Composer\Filter\PlatformRequirementFilter;
if (!defined('ABSPATH')) exit;
interface PlatformRequirementFilterInterface
{
 public function isIgnored($req);
}
