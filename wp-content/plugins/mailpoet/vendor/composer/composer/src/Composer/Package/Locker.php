<?php
namespace Composer\Package;
if (!defined('ABSPATH')) exit;
use Composer\Json\JsonFile;
use Composer\Installer\InstallationManager;
use Composer\Pcre\Preg;
use Composer\Repository\LockArrayRepository;
use Composer\Util\ProcessExecutor;
use Composer\Package\Dumper\ArrayDumper;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\Version\VersionParser;
use Composer\Plugin\PluginInterface;
use Composer\Util\Git as GitUtil;
use Composer\IO\IOInterface;
use Seld\JsonLint\ParsingException;
class Locker
{
 private $lockFile;
 private $installationManager;
 private $hash;
 private $contentHash;
 private $loader;
 private $dumper;
 private $process;
 private $lockDataCache = null;
 private $virtualFileWritten = false;
 public function __construct(IOInterface $io, JsonFile $lockFile, InstallationManager $installationManager, $composerFileContents, ProcessExecutor $process = null)
 {
 $this->lockFile = $lockFile;
 $this->installationManager = $installationManager;
 $this->hash = md5($composerFileContents);
 $this->contentHash = self::getContentHash($composerFileContents);
 $this->loader = new ArrayLoader(null, true);
 $this->dumper = new ArrayDumper();
 $this->process = $process ?: new ProcessExecutor($io);
 }
 public static function getContentHash($composerFileContents)
 {
 $content = json_decode($composerFileContents, true);
 $relevantKeys = array(
 'name',
 'version',
 'require',
 'require-dev',
 'conflict',
 'replace',
 'provide',
 'minimum-stability',
 'prefer-stable',
 'repositories',
 'extra',
 );
 $relevantContent = array();
 foreach (array_intersect($relevantKeys, array_keys($content)) as $key) {
 $relevantContent[$key] = $content[$key];
 }
 if (isset($content['config']['platform'])) {
 $relevantContent['config']['platform'] = $content['config']['platform'];
 }
 ksort($relevantContent);
 return md5(json_encode($relevantContent));
 }
 public function isLocked()
 {
 if (!$this->virtualFileWritten && !$this->lockFile->exists()) {
 return false;
 }
 $data = $this->getLockData();
 return isset($data['packages']);
 }
 public function isFresh()
 {
 $lock = $this->lockFile->read();
 if (!empty($lock['content-hash'])) {
 // There is a content hash key, use that instead of the file hash
 return $this->contentHash === $lock['content-hash'];
 }
 // BC support for old lock files without content-hash
 if (!empty($lock['hash'])) {
 return $this->hash === $lock['hash'];
 }
 // should not be reached unless the lock file is corrupted, so assume it's out of date
 return false;
 }
 public function getLockedRepository($withDevReqs = false)
 {
 $lockData = $this->getLockData();
 $packages = new LockArrayRepository();
 $lockedPackages = $lockData['packages'];
 if ($withDevReqs) {
 if (isset($lockData['packages-dev'])) {
 $lockedPackages = array_merge($lockedPackages, $lockData['packages-dev']);
 } else {
 throw new \RuntimeException('The lock file does not contain require-dev information, run install with the --no-dev option or delete it and run composer update to generate a new lock file.');
 }
 }
 if (empty($lockedPackages)) {
 return $packages;
 }
 if (isset($lockedPackages[0]['name'])) {
 $packageByName = array();
 foreach ($lockedPackages as $info) {
 $package = $this->loader->load($info);
 $packages->addPackage($package);
 $packageByName[$package->getName()] = $package;
 if ($package instanceof AliasPackage) {
 $packageByName[$package->getAliasOf()->getName()] = $package->getAliasOf();
 }
 }
 if (isset($lockData['aliases'])) {
 foreach ($lockData['aliases'] as $alias) {
 if (isset($packageByName[$alias['package']])) {
 $aliasPkg = new CompleteAliasPackage($packageByName[$alias['package']], $alias['alias_normalized'], $alias['alias']);
 $aliasPkg->setRootPackageAlias(true);
 $packages->addPackage($aliasPkg);
 }
 }
 }
 return $packages;
 }
 throw new \RuntimeException('Your composer.lock is invalid. Run "composer update" to generate a new one.');
 }
 public function getDevPackageNames()
 {
 $names = array();
 $lockData = $this->getLockData();
 if (isset($lockData['packages-dev'])) {
 foreach ($lockData['packages-dev'] as $package) {
 $names[] = strtolower($package['name']);
 }
 }
 return $names;
 }
 public function getPlatformRequirements($withDevReqs = false)
 {
 $lockData = $this->getLockData();
 $requirements = array();
 if (!empty($lockData['platform'])) {
 $requirements = $this->loader->parseLinks(
 '__root__',
 '1.0.0',
 Link::TYPE_REQUIRE,
 isset($lockData['platform']) ? $lockData['platform'] : array()
 );
 }
 if ($withDevReqs && !empty($lockData['platform-dev'])) {
 $devRequirements = $this->loader->parseLinks(
 '__root__',
 '1.0.0',
 Link::TYPE_REQUIRE,
 isset($lockData['platform-dev']) ? $lockData['platform-dev'] : array()
 );
 $requirements = array_merge($requirements, $devRequirements);
 }
 return $requirements;
 }
 public function getMinimumStability()
 {
 $lockData = $this->getLockData();
 return isset($lockData['minimum-stability']) ? $lockData['minimum-stability'] : 'stable';
 }
 public function getStabilityFlags()
 {
 $lockData = $this->getLockData();
 return isset($lockData['stability-flags']) ? $lockData['stability-flags'] : array();
 }
 public function getPreferStable()
 {
 $lockData = $this->getLockData();
 // return null if not set to allow caller logic to choose the
 // right behavior since old lock files have no prefer-stable
 return isset($lockData['prefer-stable']) ? $lockData['prefer-stable'] : null;
 }
 public function getPreferLowest()
 {
 $lockData = $this->getLockData();
 // return null if not set to allow caller logic to choose the
 // right behavior since old lock files have no prefer-lowest
 return isset($lockData['prefer-lowest']) ? $lockData['prefer-lowest'] : null;
 }
 public function getPlatformOverrides()
 {
 $lockData = $this->getLockData();
 return isset($lockData['platform-overrides']) ? $lockData['platform-overrides'] : array();
 }
 public function getAliases()
 {
 $lockData = $this->getLockData();
 return isset($lockData['aliases']) ? $lockData['aliases'] : array();
 }
 public function getLockData()
 {
 if (null !== $this->lockDataCache) {
 return $this->lockDataCache;
 }
 if (!$this->lockFile->exists()) {
 throw new \LogicException('No lockfile found. Unable to read locked packages');
 }
 return $this->lockDataCache = $this->lockFile->read();
 }
 public function setLockData(array $packages, $devPackages, array $platformReqs, $platformDevReqs, array $aliases, $minimumStability, array $stabilityFlags, $preferStable, $preferLowest, array $platformOverrides, $write = true)
 {
 // keep old default branch names normalized to DEFAULT_BRANCH_ALIAS for BC as that is how Composer 1 outputs the lock file
 // when loading the lock file the version is anyway ignored in Composer 2, so it has no adverse effect
 $aliases = array_map(function ($alias) {
 if (in_array($alias['version'], array('dev-master', 'dev-trunk', 'dev-default'), true)) {
 $alias['version'] = VersionParser::DEFAULT_BRANCH_ALIAS;
 }
 return $alias;
 }, $aliases);
 $lock = array(
 '_readme' => array('This file locks the dependencies of your project to a known state',
 'Read more about it at https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies',
 'This file is @gener'.'ated automatically', ),
 'content-hash' => $this->contentHash,
 'packages' => null,
 'packages-dev' => null,
 'aliases' => $aliases,
 'minimum-stability' => $minimumStability,
 'stability-flags' => $stabilityFlags,
 'prefer-stable' => $preferStable,
 'prefer-lowest' => $preferLowest,
 );
 $lock['packages'] = $this->lockPackages($packages);
 if (null !== $devPackages) {
 $lock['packages-dev'] = $this->lockPackages($devPackages);
 }
 $lock['platform'] = $platformReqs;
 $lock['platform-dev'] = $platformDevReqs;
 if ($platformOverrides) {
 $lock['platform-overrides'] = $platformOverrides;
 }
 $lock['plugin-api-version'] = PluginInterface::PLUGIN_API_VERSION;
 try {
 $isLocked = $this->isLocked();
 } catch (ParsingException $e) {
 $isLocked = false;
 }
 if (!$isLocked || $lock !== $this->getLockData()) {
 if ($write) {
 $this->lockFile->write($lock);
 $this->lockDataCache = null;
 $this->virtualFileWritten = false;
 } else {
 $this->virtualFileWritten = true;
 $this->lockDataCache = JsonFile::parseJson(JsonFile::encode($lock, 448 & JsonFile::JSON_PRETTY_PRINT));
 }
 return true;
 }
 return false;
 }
 private function lockPackages(array $packages)
 {
 $locked = array();
 foreach ($packages as $package) {
 if ($package instanceof AliasPackage) {
 continue;
 }
 $name = $package->getPrettyName();
 $version = $package->getPrettyVersion();
 if (!$name || !$version) {
 throw new \LogicException(sprintf(
 'Package "%s" has no version or name and can not be locked',
 $package
 ));
 }
 $spec = $this->dumper->dump($package);
 unset($spec['version_normalized']);
 // always move time to the end of the package definition
 $time = isset($spec['time']) ? $spec['time'] : null;
 unset($spec['time']);
 if ($package->isDev() && $package->getInstallationSource() === 'source') {
 // use the exact commit time of the current reference if it's a dev package
 $time = $this->getPackageTime($package) ?: $time;
 }
 if (null !== $time) {
 $spec['time'] = $time;
 }
 unset($spec['installation-source']);
 $locked[] = $spec;
 }
 usort($locked, function ($a, $b) {
 $comparison = strcmp($a['name'], $b['name']);
 if (0 !== $comparison) {
 return $comparison;
 }
 // If it is the same package, compare the versions to make the order deterministic
 return strcmp($a['version'], $b['version']);
 });
 return $locked;
 }
 private function getPackageTime(PackageInterface $package)
 {
 if (!function_exists('proc_open')) {
 return null;
 }
 $path = realpath($this->installationManager->getInstallPath($package));
 $sourceType = $package->getSourceType();
 $datetime = null;
 if ($path && in_array($sourceType, array('git', 'hg'))) {
 $sourceRef = $package->getSourceReference() ?: $package->getDistReference();
 switch ($sourceType) {
 case 'git':
 GitUtil::cleanEnv();
 if (0 === $this->process->execute('git log -n1 --pretty=%ct '.ProcessExecutor::escape($sourceRef).GitUtil::getNoShowSignatureFlag($this->process), $output, $path) && Preg::isMatch('{^\s*\d+\s*$}', $output)) {
 $datetime = new \DateTime('@'.trim($output), new \DateTimeZone('UTC'));
 }
 break;
 case 'hg':
 if (0 === $this->process->execute('hg log --template "{date|hgdate}" -r '.ProcessExecutor::escape($sourceRef), $output, $path) && Preg::isMatch('{^\s*(\d+)\s*}', $output, $match)) {
 $datetime = new \DateTime('@'.$match[1], new \DateTimeZone('UTC'));
 }
 break;
 }
 }
 return $datetime ? $datetime->format(DATE_RFC3339) : null;
 }
}
