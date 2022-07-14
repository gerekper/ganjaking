<?php
namespace Composer\DependencyResolver;
if (!defined('ABSPATH')) exit;
use Composer\Package\BasePackage;
use Composer\Package\PackageInterface;
use Composer\Repository\LockArrayRepository;
use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\Constraint\MatchAllConstraint;
class Request
{
 const UPDATE_ONLY_LISTED = 0;
 const UPDATE_LISTED_WITH_TRANSITIVE_DEPS_NO_ROOT_REQUIRE = 1;
 const UPDATE_LISTED_WITH_TRANSITIVE_DEPS = 2;
 protected $lockedRepository;
 protected $requires = array();
 protected $fixedPackages = array();
 protected $lockedPackages = array();
 protected $fixedLockedPackages = array();
 protected $updateAllowList = array();
 protected $updateAllowTransitiveDependencies = false;
 public function __construct(LockArrayRepository $lockedRepository = null)
 {
 $this->lockedRepository = $lockedRepository;
 }
 public function requireName($packageName, ConstraintInterface $constraint = null)
 {
 $packageName = strtolower($packageName);
 if ($constraint === null) {
 $constraint = new MatchAllConstraint();
 }
 if (isset($this->requires[$packageName])) {
 throw new \LogicException('Overwriting requires seems like a bug ('.$packageName.' '.$this->requires[$packageName]->getPrettyString().' => '.$constraint->getPrettyString().', check why it is happening, might be a root alias');
 }
 $this->requires[$packageName] = $constraint;
 }
 public function fixPackage(BasePackage $package)
 {
 $this->fixedPackages[spl_object_hash($package)] = $package;
 }
 public function lockPackage(BasePackage $package)
 {
 $this->lockedPackages[spl_object_hash($package)] = $package;
 }
 public function fixLockedPackage(BasePackage $package)
 {
 $this->fixedPackages[spl_object_hash($package)] = $package;
 $this->fixedLockedPackages[spl_object_hash($package)] = $package;
 }
 public function unlockPackage(BasePackage $package)
 {
 unset($this->lockedPackages[spl_object_hash($package)]);
 }
 public function setUpdateAllowList($updateAllowList, $updateAllowTransitiveDependencies)
 {
 $this->updateAllowList = $updateAllowList;
 $this->updateAllowTransitiveDependencies = $updateAllowTransitiveDependencies;
 }
 public function getUpdateAllowList()
 {
 return $this->updateAllowList;
 }
 public function getUpdateAllowTransitiveDependencies()
 {
 return $this->updateAllowTransitiveDependencies !== self::UPDATE_ONLY_LISTED;
 }
 public function getUpdateAllowTransitiveRootDependencies()
 {
 return $this->updateAllowTransitiveDependencies === self::UPDATE_LISTED_WITH_TRANSITIVE_DEPS;
 }
 public function getRequires()
 {
 return $this->requires;
 }
 public function getFixedPackages()
 {
 return $this->fixedPackages;
 }
 public function isFixedPackage(BasePackage $package)
 {
 return isset($this->fixedPackages[spl_object_hash($package)]);
 }
 public function getLockedPackages()
 {
 return $this->lockedPackages;
 }
 public function isLockedPackage(PackageInterface $package)
 {
 return isset($this->lockedPackages[spl_object_hash($package)]) || isset($this->fixedLockedPackages[spl_object_hash($package)]);
 }
 public function getFixedOrLockedPackages()
 {
 return array_merge($this->fixedPackages, $this->lockedPackages);
 }
 public function getPresentMap($packageIds = false)
 {
 $presentMap = array();
 if ($this->lockedRepository) {
 foreach ($this->lockedRepository->getPackages() as $package) {
 $presentMap[$packageIds ? $package->getId() : spl_object_hash($package)] = $package;
 }
 }
 foreach ($this->fixedPackages as $package) {
 $presentMap[$packageIds ? $package->getId() : spl_object_hash($package)] = $package;
 }
 return $presentMap;
 }
 public function getFixedPackagesMap()
 {
 $fixedPackagesMap = array();
 foreach ($this->fixedPackages as $package) {
 $fixedPackagesMap[$package->getId()] = $package;
 }
 return $fixedPackagesMap;
 }
 public function getLockedRepository()
 {
 return $this->lockedRepository;
 }
}
