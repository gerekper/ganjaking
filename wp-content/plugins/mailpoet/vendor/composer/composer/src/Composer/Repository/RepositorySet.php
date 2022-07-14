<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
use Composer\DependencyResolver\PoolOptimizer;
use Composer\DependencyResolver\PolicyInterface;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\PoolBuilder;
use Composer\DependencyResolver\Request;
use Composer\EventDispatcher\EventDispatcher;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Package\BasePackage;
use Composer\Package\AliasPackage;
use Composer\Package\CompleteAliasPackage;
use Composer\Package\CompletePackage;
use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Package\Version\StabilityFilter;
class RepositorySet
{
 const ALLOW_UNACCEPTABLE_STABILITIES = 1;
 const ALLOW_SHADOWED_REPOSITORIES = 2;
 private $rootAliases;
 private $rootReferences;
 private $repositories = array();
 private $acceptableStabilities;
 private $stabilityFlags;
 private $rootRequires;
 private $locked = false;
 private $allowInstalledRepositories = false;
 public function __construct($minimumStability = 'stable', array $stabilityFlags = array(), array $rootAliases = array(), array $rootReferences = array(), array $rootRequires = array())
 {
 $this->rootAliases = self::getRootAliasesPerPackage($rootAliases);
 $this->rootReferences = $rootReferences;
 $this->acceptableStabilities = array();
 foreach (BasePackage::$stabilities as $stability => $value) {
 if ($value <= BasePackage::$stabilities[$minimumStability]) {
 $this->acceptableStabilities[$stability] = $value;
 }
 }
 $this->stabilityFlags = $stabilityFlags;
 $this->rootRequires = $rootRequires;
 foreach ($rootRequires as $name => $constraint) {
 if (PlatformRepository::isPlatformPackage($name)) {
 unset($this->rootRequires[$name]);
 }
 }
 }
 public function allowInstalledRepositories($allow = true)
 {
 $this->allowInstalledRepositories = $allow;
 }
 public function getRootRequires()
 {
 return $this->rootRequires;
 }
 public function addRepository(RepositoryInterface $repo)
 {
 if ($this->locked) {
 throw new \RuntimeException("Pool has already been created from this repository set, it cannot be modified anymore.");
 }
 if ($repo instanceof CompositeRepository) {
 $repos = $repo->getRepositories();
 } else {
 $repos = array($repo);
 }
 foreach ($repos as $repo) {
 $this->repositories[] = $repo;
 }
 }
 public function findPackages($name, ConstraintInterface $constraint = null, $flags = 0)
 {
 $ignoreStability = ($flags & self::ALLOW_UNACCEPTABLE_STABILITIES) !== 0;
 $loadFromAllRepos = ($flags & self::ALLOW_SHADOWED_REPOSITORIES) !== 0;
 $packages = array();
 if ($loadFromAllRepos) {
 foreach ($this->repositories as $repository) {
 $packages[] = $repository->findPackages($name, $constraint) ?: array();
 }
 } else {
 foreach ($this->repositories as $repository) {
 $result = $repository->loadPackages(array($name => $constraint), $ignoreStability ? BasePackage::$stabilities : $this->acceptableStabilities, $ignoreStability ? array() : $this->stabilityFlags);
 $packages[] = $result['packages'];
 foreach ($result['namesFound'] as $nameFound) {
 // avoid loading the same package again from other repositories once it has been found
 if ($name === $nameFound) {
 break 2;
 }
 }
 }
 }
 $candidates = $packages ? call_user_func_array('array_merge', $packages) : array();
 // when using loadPackages above (!$loadFromAllRepos) the repos already filter for stability so no need to do it again
 if ($ignoreStability || !$loadFromAllRepos) {
 return $candidates;
 }
 $result = array();
 foreach ($candidates as $candidate) {
 if ($this->isPackageAcceptable($candidate->getNames(), $candidate->getStability())) {
 $result[] = $candidate;
 }
 }
 return $result;
 }
 public function getProviders($packageName)
 {
 $providers = array();
 foreach ($this->repositories as $repository) {
 if ($repoProviders = $repository->getProviders($packageName)) {
 $providers = array_merge($providers, $repoProviders);
 }
 }
 return $providers;
 }
 public function isPackageAcceptable($names, $stability)
 {
 return StabilityFilter::isPackageAcceptable($this->acceptableStabilities, $this->stabilityFlags, $names, $stability);
 }
 public function createPool(Request $request, IOInterface $io, EventDispatcher $eventDispatcher = null, PoolOptimizer $poolOptimizer = null)
 {
 $poolBuilder = new PoolBuilder($this->acceptableStabilities, $this->stabilityFlags, $this->rootAliases, $this->rootReferences, $io, $eventDispatcher, $poolOptimizer);
 foreach ($this->repositories as $repo) {
 if (($repo instanceof InstalledRepositoryInterface || $repo instanceof InstalledRepository) && !$this->allowInstalledRepositories) {
 throw new \LogicException('The pool can not accept packages from an installed repository');
 }
 }
 $this->locked = true;
 return $poolBuilder->buildPool($this->repositories, $request);
 }
 public function createPoolWithAllPackages()
 {
 foreach ($this->repositories as $repo) {
 if (($repo instanceof InstalledRepositoryInterface || $repo instanceof InstalledRepository) && !$this->allowInstalledRepositories) {
 throw new \LogicException('The pool can not accept packages from an installed repository');
 }
 }
 $this->locked = true;
 $packages = array();
 foreach ($this->repositories as $repository) {
 foreach ($repository->getPackages() as $package) {
 $packages[] = $package;
 if (isset($this->rootAliases[$package->getName()][$package->getVersion()])) {
 $alias = $this->rootAliases[$package->getName()][$package->getVersion()];
 while ($package instanceof AliasPackage) {
 $package = $package->getAliasOf();
 }
 if ($package instanceof CompletePackage) {
 $aliasPackage = new CompleteAliasPackage($package, $alias['alias_normalized'], $alias['alias']);
 } else {
 $aliasPackage = new AliasPackage($package, $alias['alias_normalized'], $alias['alias']);
 }
 $aliasPackage->setRootPackageAlias(true);
 $packages[] = $aliasPackage;
 }
 }
 }
 return new Pool($packages);
 }
 public function createPoolForPackage($packageName, LockArrayRepository $lockedRepo = null)
 {
 // TODO unify this with above in some simpler version without "request"?
 return $this->createPoolForPackages(array($packageName), $lockedRepo);
 }
 public function createPoolForPackages($packageNames, LockArrayRepository $lockedRepo = null)
 {
 $request = new Request($lockedRepo);
 foreach ($packageNames as $packageName) {
 if (PlatformRepository::isPlatformPackage($packageName)) {
 throw new \LogicException('createPoolForPackage(s) can not be used for platform packages, as they are never loaded by the PoolBuilder which expects them to be fixed. Use createPoolWithAllPackages or pass in a proper request with the platform packages you need fixed in it.');
 }
 $request->requireName($packageName);
 }
 return $this->createPool($request, new NullIO());
 }
 private static function getRootAliasesPerPackage(array $aliases)
 {
 $normalizedAliases = array();
 foreach ($aliases as $alias) {
 $normalizedAliases[$alias['package']][$alias['version']] = array(
 'alias' => $alias['alias'],
 'alias_normalized' => $alias['alias_normalized'],
 );
 }
 return $normalizedAliases;
 }
}
