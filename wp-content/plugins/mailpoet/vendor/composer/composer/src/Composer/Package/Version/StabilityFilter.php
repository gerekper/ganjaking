<?php
namespace Composer\Package\Version;
if (!defined('ABSPATH')) exit;
use Composer\Package\BasePackage;
class StabilityFilter
{
 public static function isPackageAcceptable(array $acceptableStabilities, array $stabilityFlags, array $names, $stability)
 {
 foreach ($names as $name) {
 // allow if package matches the package-specific stability flag
 if (isset($stabilityFlags[$name])) {
 if (BasePackage::$stabilities[$stability] <= $stabilityFlags[$name]) {
 return true;
 }
 } elseif (isset($acceptableStabilities[$stability])) {
 // allow if package matches the global stability requirement and has no exception
 return true;
 }
 }
 return false;
 }
}
