<?php
namespace Composer\Util;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
class PackageSorter
{
 public static function sortPackages(array $packages, array $weights = array())
 {
 $usageList = array();
 foreach ($packages as $package) {
 $links = $package->getRequires();
 if ($package instanceof RootPackageInterface) {
 $links = array_merge($links, $package->getDevRequires());
 }
 foreach ($links as $link) {
 $target = $link->getTarget();
 $usageList[$target][] = $package->getName();
 }
 }
 $computing = array();
 $computed = array();
 $computeImportance = function ($name) use (&$computeImportance, &$computing, &$computed, $usageList, $weights) {
 // reusing computed importance
 if (isset($computed[$name])) {
 return $computed[$name];
 }
 // canceling circular dependency
 if (isset($computing[$name])) {
 return 0;
 }
 $computing[$name] = true;
 $weight = isset($weights[$name]) ? $weights[$name] : 0;
 if (isset($usageList[$name])) {
 foreach ($usageList[$name] as $user) {
 $weight -= 1 - $computeImportance($user);
 }
 }
 unset($computing[$name]);
 $computed[$name] = $weight;
 return $weight;
 };
 $weightedPackages = array();
 foreach ($packages as $index => $package) {
 $name = $package->getName();
 $weight = $computeImportance($name);
 $weightedPackages[] = array('name' => $name, 'weight' => $weight, 'index' => $index);
 }
 usort($weightedPackages, function ($a, $b) {
 if ($a['weight'] !== $b['weight']) {
 return $a['weight'] - $b['weight'];
 }
 return strnatcasecmp($a['name'], $b['name']);
 });
 $sortedPackages = array();
 foreach ($weightedPackages as $pkg) {
 $sortedPackages[] = $packages[$pkg['index']];
 }
 return $sortedPackages;
 }
}
