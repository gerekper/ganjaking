<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
use Composer\Package\AliasPackage;
use Composer\Installer\InstallationManager;
class WritableArrayRepository extends ArrayRepository implements WritableRepositoryInterface
{
 protected $devPackageNames = array();
 private $devMode = null;
 public function getDevMode()
 {
 return $this->devMode;
 }
 public function setDevPackageNames(array $devPackageNames)
 {
 $this->devPackageNames = $devPackageNames;
 }
 public function getDevPackageNames()
 {
 return $this->devPackageNames;
 }
 public function write($devMode, InstallationManager $installationManager)
 {
 $this->devMode = $devMode;
 }
 public function reload()
 {
 $this->devMode = null;
 }
 public function getCanonicalPackages()
 {
 $packages = $this->getPackages();
 // get at most one package of each name, preferring non-aliased ones
 $packagesByName = array();
 foreach ($packages as $package) {
 if (!isset($packagesByName[$package->getName()]) || $packagesByName[$package->getName()] instanceof AliasPackage) {
 $packagesByName[$package->getName()] = $package;
 }
 }
 $canonicalPackages = array();
 // unfold aliased packages
 foreach ($packagesByName as $package) {
 while ($package instanceof AliasPackage) {
 $package = $package->getAliasOf();
 }
 $canonicalPackages[] = $package;
 }
 return $canonicalPackages;
 }
}
