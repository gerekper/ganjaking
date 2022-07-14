<?php
namespace Composer\Repository;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
use Composer\Installer\InstallationManager;
interface WritableRepositoryInterface extends RepositoryInterface
{
 public function write($devMode, InstallationManager $installationManager);
 public function addPackage(PackageInterface $package);
 public function removePackage(PackageInterface $package);
 public function getCanonicalPackages();
 public function reload();
 public function setDevPackageNames(array $devPackageNames);
 public function getDevPackageNames();
}
