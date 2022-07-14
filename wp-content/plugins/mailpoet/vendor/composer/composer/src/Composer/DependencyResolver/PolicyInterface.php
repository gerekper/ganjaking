<?php
namespace Composer\DependencyResolver;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
use Composer\Semver\Constraint\Constraint;
interface PolicyInterface
{
 public function versionCompare(PackageInterface $a, PackageInterface $b, $operator);
 public function selectPreferredPackages(Pool $pool, array $literals, $requiredPackage = null);
}
