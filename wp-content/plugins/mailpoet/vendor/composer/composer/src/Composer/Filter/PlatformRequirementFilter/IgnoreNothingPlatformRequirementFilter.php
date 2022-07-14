<?php
namespace Composer\Filter\PlatformRequirementFilter;
if (!defined('ABSPATH')) exit;
final class IgnoreNothingPlatformRequirementFilter implements PlatformRequirementFilterInterface
{
 public function isIgnored($req)
 {
 return false;
 }
}
