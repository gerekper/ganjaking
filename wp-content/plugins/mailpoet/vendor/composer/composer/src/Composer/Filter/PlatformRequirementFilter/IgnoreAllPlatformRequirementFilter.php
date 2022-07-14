<?php
namespace Composer\Filter\PlatformRequirementFilter;
if (!defined('ABSPATH')) exit;
use Composer\Repository\PlatformRepository;
final class IgnoreAllPlatformRequirementFilter implements PlatformRequirementFilterInterface
{
 public function isIgnored($req)
 {
 return PlatformRepository::isPlatformPackage($req);
 }
}
