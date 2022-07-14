<?php
namespace Composer\Installer;
if (!defined('ABSPATH')) exit;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Package\PackageInterface;
class NoopInstaller implements InstallerInterface
{
 public function supports($packageType)
 {
 return true;
 }
 public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package)
 {
 return $repo->hasPackage($package);
 }
 public function download(PackageInterface $package, PackageInterface $prevPackage = null)
 {
 return \React\Promise\resolve();
 }
 public function prepare($type, PackageInterface $package, PackageInterface $prevPackage = null)
 {
 return \React\Promise\resolve();
 }
 public function cleanup($type, PackageInterface $package, PackageInterface $prevPackage = null)
 {
 return \React\Promise\resolve();
 }
 public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
 {
 if (!$repo->hasPackage($package)) {
 $repo->addPackage(clone $package);
 }
 return \React\Promise\resolve();
 }
 public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
 {
 if (!$repo->hasPackage($initial)) {
 throw new \InvalidArgumentException('Package is not installed: '.$initial);
 }
 $repo->removePackage($initial);
 if (!$repo->hasPackage($target)) {
 $repo->addPackage(clone $target);
 }
 return \React\Promise\resolve();
 }
 public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
 {
 if (!$repo->hasPackage($package)) {
 throw new \InvalidArgumentException('Package is not installed: '.$package);
 }
 $repo->removePackage($package);
 return \React\Promise\resolve();
 }
 public function getInstallPath(PackageInterface $package)
 {
 $targetDir = $package->getTargetDir();
 return $package->getPrettyName() . ($targetDir ? '/'.$targetDir : '');
 }
}
