<?php
namespace Composer;
if (!defined('ABSPATH')) exit;
use Composer\Package\RootPackageInterface;
use Composer\Package\Locker;
use Composer\Pcre\Preg;
use Composer\Util\Loop;
use Composer\Repository\RepositoryManager;
use Composer\Installer\InstallationManager;
use Composer\Plugin\PluginManager;
use Composer\Downloader\DownloadManager;
use Composer\EventDispatcher\EventDispatcher;
use Composer\Autoload\AutoloadGenerator;
use Composer\Package\Archiver\ArchiveManager;
class Composer
{
 const VERSION = '2.2.14';
 const BRANCH_ALIAS_VERSION = '';
 const RELEASE_DATE = '2022-06-06 16:32:50';
 const SOURCE_VERSION = '';
 const RUNTIME_API_VERSION = '2.2.2';
 public static function getVersion()
 {
 // no replacement done, this must be a source checkout
 if (self::VERSION === '@package_version'.'@') {
 return self::SOURCE_VERSION;
 }
 // we have a branch alias and version is a commit id, this must be a snapshot build
 if (self::BRANCH_ALIAS_VERSION !== '' && Preg::isMatch('{^[a-f0-9]{40}$}', self::VERSION)) {
 return self::BRANCH_ALIAS_VERSION.'+'.self::VERSION;
 }
 return self::VERSION;
 }
 private $package;
 private $locker = null;
 private $loop;
 private $repositoryManager;
 private $downloadManager;
 private $installationManager;
 private $pluginManager;
 private $config;
 private $eventDispatcher;
 private $autoloadGenerator;
 private $archiveManager;
 public function setPackage(RootPackageInterface $package)
 {
 $this->package = $package;
 }
 public function getPackage()
 {
 return $this->package;
 }
 public function setConfig(Config $config)
 {
 $this->config = $config;
 }
 public function getConfig()
 {
 return $this->config;
 }
 public function setLocker(Locker $locker)
 {
 $this->locker = $locker;
 }
 public function getLocker()
 {
 return $this->locker;
 }
 public function setLoop(Loop $loop)
 {
 $this->loop = $loop;
 }
 public function getLoop()
 {
 return $this->loop;
 }
 public function setRepositoryManager(RepositoryManager $manager)
 {
 $this->repositoryManager = $manager;
 }
 public function getRepositoryManager()
 {
 return $this->repositoryManager;
 }
 public function setDownloadManager(DownloadManager $manager)
 {
 $this->downloadManager = $manager;
 }
 public function getDownloadManager()
 {
 return $this->downloadManager;
 }
 public function setArchiveManager(ArchiveManager $manager)
 {
 $this->archiveManager = $manager;
 }
 public function getArchiveManager()
 {
 return $this->archiveManager;
 }
 public function setInstallationManager(InstallationManager $manager)
 {
 $this->installationManager = $manager;
 }
 public function getInstallationManager()
 {
 return $this->installationManager;
 }
 public function setPluginManager(PluginManager $manager)
 {
 $this->pluginManager = $manager;
 }
 public function getPluginManager()
 {
 return $this->pluginManager;
 }
 public function setEventDispatcher(EventDispatcher $eventDispatcher)
 {
 $this->eventDispatcher = $eventDispatcher;
 }
 public function getEventDispatcher()
 {
 return $this->eventDispatcher;
 }
 public function setAutoloadGenerator(AutoloadGenerator $autoloadGenerator)
 {
 $this->autoloadGenerator = $autoloadGenerator;
 }
 public function getAutoloadGenerator()
 {
 return $this->autoloadGenerator;
 }
}
