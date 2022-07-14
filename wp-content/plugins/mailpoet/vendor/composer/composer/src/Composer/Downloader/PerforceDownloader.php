<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
use Composer\Package\PackageInterface;
use Composer\Repository\VcsRepository;
use Composer\Util\Perforce;
class PerforceDownloader extends VcsDownloader
{
 protected $perforce;
 protected function doDownload(PackageInterface $package, $path, $url, PackageInterface $prevPackage = null)
 {
 return \React\Promise\resolve();
 }
 public function doInstall(PackageInterface $package, $path, $url)
 {
 $ref = $package->getSourceReference();
 $label = $this->getLabelFromSourceReference((string) $ref);
 $this->io->writeError('Cloning ' . $ref);
 $this->initPerforce($package, $path, $url);
 $this->perforce->setStream($ref);
 $this->perforce->p4Login();
 $this->perforce->writeP4ClientSpec();
 $this->perforce->connectClient();
 $this->perforce->syncCodeBase($label);
 $this->perforce->cleanupClientSpec();
 return \React\Promise\resolve();
 }
 private function getLabelFromSourceReference($ref)
 {
 $pos = strpos($ref, '@');
 if (false !== $pos) {
 return substr($ref, $pos + 1);
 }
 return null;
 }
 public function initPerforce(PackageInterface $package, $path, $url)
 {
 if (!empty($this->perforce)) {
 $this->perforce->initializePath($path);
 return;
 }
 $repository = $package->getRepository();
 $repoConfig = null;
 if ($repository instanceof VcsRepository) {
 $repoConfig = $this->getRepoConfig($repository);
 }
 $this->perforce = Perforce::create($repoConfig, $url, $path, $this->process, $this->io);
 }
 private function getRepoConfig(VcsRepository $repository)
 {
 return $repository->getRepoConfig();
 }
 protected function doUpdate(PackageInterface $initial, PackageInterface $target, $path, $url)
 {
 return $this->doInstall($target, $path, $url);
 }
 public function getLocalChanges(PackageInterface $package, $path)
 {
 $this->io->writeError('Perforce driver does not check for local changes before overriding');
 return null;
 }
 protected function getCommitLogs($fromReference, $toReference, $path)
 {
 return $this->perforce->getCommitLogs($fromReference, $toReference);
 }
 public function setPerforce(Perforce $perforce)
 {
 $this->perforce = $perforce;
 }
 protected function hasMetadataRepository($path)
 {
 return true;
 }
}
