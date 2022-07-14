<?php
namespace Composer\Installer;
if (!defined('ABSPATH')) exit;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Pcre\Preg;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;
use Composer\Util\Silencer;
use Composer\Util\Platform;
use React\Promise\PromiseInterface;
use Composer\Downloader\DownloadManager;
class LibraryInstaller implements InstallerInterface, BinaryPresenceInterface
{
 protected $composer;
 protected $vendorDir;
 protected $downloadManager;
 protected $io;
 protected $type;
 protected $filesystem;
 protected $binaryInstaller;
 public function __construct(IOInterface $io, Composer $composer, $type = 'library', Filesystem $filesystem = null, BinaryInstaller $binaryInstaller = null)
 {
 $this->composer = $composer;
 $this->downloadManager = $composer->getDownloadManager();
 $this->io = $io;
 $this->type = $type;
 $this->filesystem = $filesystem ?: new Filesystem();
 $this->vendorDir = rtrim($composer->getConfig()->get('vendor-dir'), '/');
 $this->binaryInstaller = $binaryInstaller ?: new BinaryInstaller($this->io, rtrim($composer->getConfig()->get('bin-dir'), '/'), $composer->getConfig()->get('bin-compat'), $this->filesystem, $this->vendorDir);
 }
 public function supports($packageType)
 {
 return $packageType === $this->type || null === $this->type;
 }
 public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
 {
 if (!$repo->hasPackage($package)) {
 return false;
 }
 $installPath = $this->getInstallPath($package);
 if (Filesystem::isReadable($installPath)) {
 return true;
 }
 if (Platform::isWindows() && $this->filesystem->isJunction($installPath)) {
 return true;
 }
 if (is_link($installPath)) {
 if (realpath($installPath) === false) {
 return false;
 }
 return true;
 }
 return false;
 }
 public function download(PackageInterface $package, PackageInterface $prevPackage = null)
 {
 $this->initializeVendorDir();
 $downloadPath = $this->getInstallPath($package);
 return $this->downloadManager->download($package, $downloadPath, $prevPackage);
 }
 public function prepare($type, PackageInterface $package, PackageInterface $prevPackage = null)
 {
 $this->initializeVendorDir();
 $downloadPath = $this->getInstallPath($package);
 return $this->downloadManager->prepare($type, $package, $downloadPath, $prevPackage);
 }
 public function cleanup($type, PackageInterface $package, PackageInterface $prevPackage = null)
 {
 $this->initializeVendorDir();
 $downloadPath = $this->getInstallPath($package);
 return $this->downloadManager->cleanup($type, $package, $downloadPath, $prevPackage);
 }
 public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
 {
 $this->initializeVendorDir();
 $downloadPath = $this->getInstallPath($package);
 // remove the binaries if it appears the package files are missing
 if (!Filesystem::isReadable($downloadPath) && $repo->hasPackage($package)) {
 $this->binaryInstaller->removeBinaries($package);
 }
 $promise = $this->installCode($package);
 if (!$promise instanceof PromiseInterface) {
 $promise = \React\Promise\resolve();
 }
 $binaryInstaller = $this->binaryInstaller;
 $installPath = $this->getInstallPath($package);
 return $promise->then(function () use ($binaryInstaller, $installPath, $package, $repo) {
 $binaryInstaller->installBinaries($package, $installPath);
 if (!$repo->hasPackage($package)) {
 $repo->addPackage(clone $package);
 }
 });
 }
 public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
 {
 if (!$repo->hasPackage($initial)) {
 throw new \InvalidArgumentException('Package is not installed: '.$initial);
 }
 $this->initializeVendorDir();
 $this->binaryInstaller->removeBinaries($initial);
 $promise = $this->updateCode($initial, $target);
 if (!$promise instanceof PromiseInterface) {
 $promise = \React\Promise\resolve();
 }
 $binaryInstaller = $this->binaryInstaller;
 $installPath = $this->getInstallPath($target);
 return $promise->then(function () use ($binaryInstaller, $installPath, $target, $initial, $repo) {
 $binaryInstaller->installBinaries($target, $installPath);
 $repo->removePackage($initial);
 if (!$repo->hasPackage($target)) {
 $repo->addPackage(clone $target);
 }
 });
 }
 public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
 {
 if (!$repo->hasPackage($package)) {
 throw new \InvalidArgumentException('Package is not installed: '.$package);
 }
 $promise = $this->removeCode($package);
 if (!$promise instanceof PromiseInterface) {
 $promise = \React\Promise\resolve();
 }
 $binaryInstaller = $this->binaryInstaller;
 $downloadPath = $this->getPackageBasePath($package);
 $filesystem = $this->filesystem;
 return $promise->then(function () use ($binaryInstaller, $filesystem, $downloadPath, $package, $repo) {
 $binaryInstaller->removeBinaries($package);
 $repo->removePackage($package);
 if (strpos($package->getName(), '/')) {
 $packageVendorDir = dirname($downloadPath);
 if (is_dir($packageVendorDir) && $filesystem->isDirEmpty($packageVendorDir)) {
 Silencer::call('rmdir', $packageVendorDir);
 }
 }
 });
 }
 public function getInstallPath(PackageInterface $package)
 {
 $this->initializeVendorDir();
 $basePath = ($this->vendorDir ? $this->vendorDir.'/' : '') . $package->getPrettyName();
 $targetDir = $package->getTargetDir();
 return $basePath . ($targetDir ? '/'.$targetDir : '');
 }
 public function ensureBinariesPresence(PackageInterface $package)
 {
 $this->binaryInstaller->installBinaries($package, $this->getInstallPath($package), false);
 }
 protected function getPackageBasePath(PackageInterface $package)
 {
 $installPath = $this->getInstallPath($package);
 $targetDir = $package->getTargetDir();
 if ($targetDir) {
 return Preg::replace('{/*'.str_replace('/', '/+', preg_quote($targetDir)).'/?$}', '', $installPath);
 }
 return $installPath;
 }
 protected function installCode(PackageInterface $package)
 {
 $downloadPath = $this->getInstallPath($package);
 return $this->downloadManager->install($package, $downloadPath);
 }
 protected function updateCode(PackageInterface $initial, PackageInterface $target)
 {
 $initialDownloadPath = $this->getInstallPath($initial);
 $targetDownloadPath = $this->getInstallPath($target);
 if ($targetDownloadPath !== $initialDownloadPath) {
 // if the target and initial dirs intersect, we force a remove + install
 // to avoid the rename wiping the target dir as part of the initial dir cleanup
 if (strpos($initialDownloadPath, $targetDownloadPath) === 0
 || strpos($targetDownloadPath, $initialDownloadPath) === 0
 ) {
 $promise = $this->removeCode($initial);
 if (!$promise instanceof PromiseInterface) {
 $promise = \React\Promise\resolve();
 }
 $self = $this;
 return $promise->then(function () use ($self, $target) {
 $reflMethod = new \ReflectionMethod($self, 'installCode');
 $reflMethod->setAccessible(true);
 // equivalent of $this->installCode($target) with php 5.3 support
 // TODO remove this once 5.3 support is dropped
 return $reflMethod->invoke($self, $target);
 });
 }
 $this->filesystem->rename($initialDownloadPath, $targetDownloadPath);
 }
 return $this->downloadManager->update($initial, $target, $targetDownloadPath);
 }
 protected function removeCode(PackageInterface $package)
 {
 $downloadPath = $this->getPackageBasePath($package);
 return $this->downloadManager->remove($package, $downloadPath);
 }
 protected function initializeVendorDir()
 {
 $this->filesystem->ensureDirectoryExists($this->vendorDir);
 $this->vendorDir = realpath($this->vendorDir);
 }
}
