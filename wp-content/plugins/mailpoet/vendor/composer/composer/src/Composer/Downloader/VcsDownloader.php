<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
use Composer\Config;
use Composer\Package\Dumper\ArrayDumper;
use Composer\Package\PackageInterface;
use Composer\Package\Version\VersionGuesser;
use Composer\Package\Version\VersionParser;
use Composer\Util\ProcessExecutor;
use Composer\IO\IOInterface;
use Composer\Util\Filesystem;
use React\Promise\PromiseInterface;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
abstract class VcsDownloader implements DownloaderInterface, ChangeReportInterface, VcsCapableDownloaderInterface
{
 protected $io;
 protected $config;
 protected $process;
 protected $filesystem;
 protected $hasCleanedChanges = array();
 public function __construct(IOInterface $io, Config $config, ProcessExecutor $process = null, Filesystem $fs = null)
 {
 $this->io = $io;
 $this->config = $config;
 $this->process = $process ?: new ProcessExecutor($io);
 $this->filesystem = $fs ?: new Filesystem($this->process);
 }
 public function getInstallationSource()
 {
 return 'source';
 }
 public function download(PackageInterface $package, $path, PackageInterface $prevPackage = null)
 {
 if (!$package->getSourceReference()) {
 throw new \InvalidArgumentException('Package '.$package->getPrettyName().' is missing reference information');
 }
 $urls = $this->prepareUrls($package->getSourceUrls());
 while ($url = array_shift($urls)) {
 try {
 return $this->doDownload($package, $path, $url, $prevPackage);
 } catch (\Exception $e) {
 // rethrow phpunit exceptions to avoid hard to debug bug failures
 if ($e instanceof \PHPUnit\Framework\Exception) {
 throw $e;
 }
 if ($this->io->isDebug()) {
 $this->io->writeError('Failed: ['.get_class($e).'] '.$e->getMessage());
 } elseif (count($urls)) {
 $this->io->writeError(' Failed, trying the next URL');
 }
 if (!count($urls)) {
 throw $e;
 }
 }
 }
 return \React\Promise\resolve();
 }
 public function prepare($type, PackageInterface $package, $path, PackageInterface $prevPackage = null)
 {
 if ($type === 'update') {
 $this->cleanChanges($prevPackage, $path, true);
 $this->hasCleanedChanges[$prevPackage->getUniqueName()] = true;
 } elseif ($type === 'install') {
 $this->filesystem->emptyDirectory($path);
 } elseif ($type === 'uninstall') {
 $this->cleanChanges($package, $path, false);
 }
 return \React\Promise\resolve();
 }
 public function cleanup($type, PackageInterface $package, $path, PackageInterface $prevPackage = null)
 {
 if ($type === 'update' && isset($this->hasCleanedChanges[$prevPackage->getUniqueName()])) {
 $this->reapplyChanges($path);
 unset($this->hasCleanedChanges[$prevPackage->getUniqueName()]);
 }
 return \React\Promise\resolve();
 }
 public function install(PackageInterface $package, $path)
 {
 if (!$package->getSourceReference()) {
 throw new \InvalidArgumentException('Package '.$package->getPrettyName().' is missing reference information');
 }
 $this->io->writeError(" - " . InstallOperation::format($package).': ', false);
 $urls = $this->prepareUrls($package->getSourceUrls());
 while ($url = array_shift($urls)) {
 try {
 $this->doInstall($package, $path, $url);
 break;
 } catch (\Exception $e) {
 // rethrow phpunit exceptions to avoid hard to debug bug failures
 if ($e instanceof \PHPUnit\Framework\Exception) {
 throw $e;
 }
 if ($this->io->isDebug()) {
 $this->io->writeError('Failed: ['.get_class($e).'] '.$e->getMessage());
 } elseif (count($urls)) {
 $this->io->writeError(' Failed, trying the next URL');
 }
 if (!count($urls)) {
 throw $e;
 }
 }
 }
 return \React\Promise\resolve();
 }
 public function update(PackageInterface $initial, PackageInterface $target, $path)
 {
 if (!$target->getSourceReference()) {
 throw new \InvalidArgumentException('Package '.$target->getPrettyName().' is missing reference information');
 }
 $this->io->writeError(" - " . UpdateOperation::format($initial, $target).': ', false);
 $urls = $this->prepareUrls($target->getSourceUrls());
 $exception = null;
 while ($url = array_shift($urls)) {
 try {
 $this->doUpdate($initial, $target, $path, $url);
 $exception = null;
 break;
 } catch (\Exception $exception) {
 // rethrow phpunit exceptions to avoid hard to debug bug failures
 if ($exception instanceof \PHPUnit\Framework\Exception) {
 throw $exception;
 }
 if ($this->io->isDebug()) {
 $this->io->writeError('Failed: ['.get_class($exception).'] '.$exception->getMessage());
 } elseif (count($urls)) {
 $this->io->writeError(' Failed, trying the next URL');
 }
 }
 }
 // print the commit logs if in verbose mode and VCS metadata is present
 // because in case of missing metadata code would trigger another exception
 if (!$exception && $this->io->isVerbose() && $this->hasMetadataRepository($path)) {
 $message = 'Pulling in changes:';
 $logs = $this->getCommitLogs($initial->getSourceReference(), $target->getSourceReference(), $path);
 if (!trim($logs)) {
 $message = 'Rolling back changes:';
 $logs = $this->getCommitLogs($target->getSourceReference(), $initial->getSourceReference(), $path);
 }
 if (trim($logs)) {
 $logs = implode("\n", array_map(function ($line) {
 return ' ' . $line;
 }, explode("\n", $logs)));
 // escape angle brackets for proper output in the console
 $logs = str_replace('<', '\<', $logs);
 $this->io->writeError(' '.$message);
 $this->io->writeError($logs);
 }
 }
 if (!$urls && $exception) {
 throw $exception;
 }
 return \React\Promise\resolve();
 }
 public function remove(PackageInterface $package, $path)
 {
 $this->io->writeError(" - " . UninstallOperation::format($package));
 $promise = $this->filesystem->removeDirectoryAsync($path);
 return $promise->then(function ($result) use ($path) {
 if (!$result) {
 throw new \RuntimeException('Could not completely delete '.$path.', aborting.');
 }
 });
 }
 public function getVcsReference(PackageInterface $package, $path)
 {
 $parser = new VersionParser;
 $guesser = new VersionGuesser($this->config, $this->process, $parser);
 $dumper = new ArrayDumper;
 $packageConfig = $dumper->dump($package);
 if ($packageVersion = $guesser->guessVersion($packageConfig, $path)) {
 return $packageVersion['commit'];
 }
 return null;
 }
 protected function cleanChanges(PackageInterface $package, $path, $update)
 {
 // the default implementation just fails if there are any changes, override in child classes to provide stash-ability
 if (null !== $this->getLocalChanges($package, $path)) {
 throw new \RuntimeException('Source directory ' . $path . ' has uncommitted changes.');
 }
 return \React\Promise\resolve();
 }
 protected function reapplyChanges($path)
 {
 }
 abstract protected function doDownload(PackageInterface $package, $path, $url, PackageInterface $prevPackage = null);
 abstract protected function doInstall(PackageInterface $package, $path, $url);
 abstract protected function doUpdate(PackageInterface $initial, PackageInterface $target, $path, $url);
 abstract protected function getCommitLogs($fromReference, $toReference, $path);
 abstract protected function hasMetadataRepository($path);
 private function prepareUrls(array $urls)
 {
 foreach ($urls as $index => $url) {
 if (Filesystem::isLocalPath($url)) {
 // realpath() below will not understand
 // url that starts with "file://"
 $fileProtocol = 'file://';
 $isFileProtocol = false;
 if (0 === strpos($url, $fileProtocol)) {
 $url = substr($url, strlen($fileProtocol));
 $isFileProtocol = true;
 }
 // realpath() below will not understand %20 spaces etc.
 if (false !== strpos($url, '%')) {
 $url = rawurldecode($url);
 }
 $urls[$index] = realpath($url);
 if ($isFileProtocol) {
 $urls[$index] = $fileProtocol . $urls[$index];
 }
 }
 }
 return $urls;
 }
}
