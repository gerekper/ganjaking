<?php
namespace Composer\Repository\Vcs;
if (!defined('ABSPATH')) exit;
use Composer\Cache;
use Composer\Config;
use Composer\Json\JsonFile;
use Composer\Pcre\Preg;
use Composer\Util\ProcessExecutor;
use Composer\Util\Filesystem;
use Composer\Util\Url;
use Composer\Util\Svn as SvnUtil;
use Composer\IO\IOInterface;
use Composer\Downloader\TransportException;
class SvnDriver extends VcsDriver
{
 protected $baseUrl;
 protected $tags;
 protected $branches;
 protected $rootIdentifier;
 protected $trunkPath = 'trunk';
 protected $branchesPath = 'branches';
 protected $tagsPath = 'tags';
 protected $packagePath = '';
 protected $cacheCredentials = true;
 private $util;
 public function initialize()
 {
 $this->url = $this->baseUrl = rtrim(self::normalizeUrl($this->url), '/');
 SvnUtil::cleanEnv();
 if (isset($this->repoConfig['trunk-path'])) {
 $this->trunkPath = $this->repoConfig['trunk-path'];
 }
 if (isset($this->repoConfig['branches-path'])) {
 $this->branchesPath = $this->repoConfig['branches-path'];
 }
 if (isset($this->repoConfig['tags-path'])) {
 $this->tagsPath = $this->repoConfig['tags-path'];
 }
 if (array_key_exists('svn-cache-credentials', $this->repoConfig)) {
 $this->cacheCredentials = (bool) $this->repoConfig['svn-cache-credentials'];
 }
 if (isset($this->repoConfig['package-path'])) {
 $this->packagePath = '/' . trim($this->repoConfig['package-path'], '/');
 }
 if (false !== ($pos = strrpos($this->url, '/' . $this->trunkPath))) {
 $this->baseUrl = substr($this->url, 0, $pos);
 }
 $this->cache = new Cache($this->io, $this->config->get('cache-repo-dir').'/'.Preg::replace('{[^a-z0-9.]}i', '-', Url::sanitize($this->baseUrl)));
 $this->cache->setReadOnly($this->config->get('cache-read-only'));
 $this->getBranches();
 $this->getTags();
 }
 public function getRootIdentifier()
 {
 return $this->rootIdentifier ?: $this->trunkPath;
 }
 public function getUrl()
 {
 return $this->url;
 }
 public function getSource($identifier)
 {
 return array('type' => 'svn', 'url' => $this->baseUrl, 'reference' => $identifier);
 }
 public function getDist($identifier)
 {
 return null;
 }
 protected function shouldCache($identifier)
 {
 return $this->cache && Preg::isMatch('{@\d+$}', $identifier);
 }
 public function getComposerInformation($identifier)
 {
 if (!isset($this->infoCache[$identifier])) {
 if ($this->shouldCache($identifier) && $res = $this->cache->read($identifier.'.json')) {
 return $this->infoCache[$identifier] = JsonFile::parseJson($res);
 }
 try {
 $composer = $this->getBaseComposerInformation($identifier);
 } catch (TransportException $e) {
 $message = $e->getMessage();
 if (stripos($message, 'path not found') === false && stripos($message, 'svn: warning: W160013') === false) {
 throw $e;
 }
 // remember a not-existent composer.json
 $composer = '';
 }
 if ($this->shouldCache($identifier)) {
 $this->cache->write($identifier.'.json', json_encode($composer));
 }
 $this->infoCache[$identifier] = $composer;
 }
 return $this->infoCache[$identifier];
 }
 public function getFileContent($file, $identifier)
 {
 $identifier = '/' . trim($identifier, '/') . '/';
 Preg::match('{^(.+?)(@\d+)?/$}', $identifier, $match);
 if (!empty($match[2])) {
 $path = $match[1];
 $rev = $match[2];
 } else {
 $path = $identifier;
 $rev = '';
 }
 try {
 $resource = $path.$file;
 $output = $this->execute('svn cat', $this->baseUrl . $resource . $rev);
 if (!trim($output)) {
 return null;
 }
 } catch (\RuntimeException $e) {
 throw new TransportException($e->getMessage());
 }
 return $output;
 }
 public function getChangeDate($identifier)
 {
 $identifier = '/' . trim($identifier, '/') . '/';
 Preg::match('{^(.+?)(@\d+)?/$}', $identifier, $match);
 if (!empty($match[2])) {
 $path = $match[1];
 $rev = $match[2];
 } else {
 $path = $identifier;
 $rev = '';
 }
 $output = $this->execute('svn info', $this->baseUrl . $path . $rev);
 foreach ($this->process->splitLines($output) as $line) {
 if ($line && Preg::isMatch('{^Last Changed Date: ([^(]+)}', $line, $match)) {
 return new \DateTime($match[1], new \DateTimeZone('UTC'));
 }
 }
 return null;
 }
 public function getTags()
 {
 if (null === $this->tags) {
 $tags = array();
 if ($this->tagsPath !== false) {
 $output = $this->execute('svn ls --verbose', $this->baseUrl . '/' . $this->tagsPath);
 if ($output) {
 foreach ($this->process->splitLines($output) as $line) {
 $line = trim($line);
 if ($line && Preg::isMatch('{^\s*(\S+).*?(\S+)\s*$}', $line, $match)) {
 if (isset($match[1], $match[2]) && $match[2] !== './') {
 $tags[rtrim($match[2], '/')] = $this->buildIdentifier(
 '/' . $this->tagsPath . '/' . $match[2],
 $match[1]
 );
 }
 }
 }
 }
 }
 $this->tags = $tags;
 }
 return $this->tags;
 }
 public function getBranches()
 {
 if (null === $this->branches) {
 $branches = array();
 if (false === $this->trunkPath) {
 $trunkParent = $this->baseUrl . '/';
 } else {
 $trunkParent = $this->baseUrl . '/' . $this->trunkPath;
 }
 $output = $this->execute('svn ls --verbose', $trunkParent);
 if ($output) {
 foreach ($this->process->splitLines($output) as $line) {
 $line = trim($line);
 if ($line && Preg::isMatch('{^\s*(\S+).*?(\S+)\s*$}', $line, $match)) {
 if (isset($match[1], $match[2]) && $match[2] === './') {
 $branches['trunk'] = $this->buildIdentifier(
 '/' . $this->trunkPath,
 $match[1]
 );
 $this->rootIdentifier = $branches['trunk'];
 break;
 }
 }
 }
 }
 unset($output);
 if ($this->branchesPath !== false) {
 $output = $this->execute('svn ls --verbose', $this->baseUrl . '/' . $this->branchesPath);
 if ($output) {
 foreach ($this->process->splitLines(trim($output)) as $line) {
 $line = trim($line);
 if ($line && Preg::isMatch('{^\s*(\S+).*?(\S+)\s*$}', $line, $match)) {
 if (isset($match[1], $match[2]) && $match[2] !== './') {
 $branches[rtrim($match[2], '/')] = $this->buildIdentifier(
 '/' . $this->branchesPath . '/' . $match[2],
 $match[1]
 );
 }
 }
 }
 }
 }
 $this->branches = $branches;
 }
 return $this->branches;
 }
 public static function supports(IOInterface $io, Config $config, $url, $deep = false)
 {
 $url = self::normalizeUrl($url);
 if (Preg::isMatch('#(^svn://|^svn\+ssh://|svn\.)#i', $url)) {
 return true;
 }
 // proceed with deep check for local urls since they are fast to process
 if (!$deep && !Filesystem::isLocalPath($url)) {
 return false;
 }
 $process = new ProcessExecutor($io);
 $exit = $process->execute(
 "svn info --non-interactive -- ".ProcessExecutor::escape($url),
 $ignoredOutput
 );
 if ($exit === 0) {
 // This is definitely a Subversion repository.
 return true;
 }
 // Subversion client 1.7 and older
 if (false !== stripos($process->getErrorOutput(), 'authorization failed:')) {
 // This is likely a remote Subversion repository that requires
 // authentication. We will handle actual authentication later.
 return true;
 }
 // Subversion client 1.8 and newer
 if (false !== stripos($process->getErrorOutput(), 'Authentication failed')) {
 // This is likely a remote Subversion or newer repository that requires
 // authentication. We will handle actual authentication later.
 return true;
 }
 return false;
 }
 protected static function normalizeUrl($url)
 {
 $fs = new Filesystem();
 if ($fs->isAbsolutePath($url)) {
 return 'file://' . strtr($url, '\\', '/');
 }
 return $url;
 }
 protected function execute($command, $url)
 {
 if (null === $this->util) {
 $this->util = new SvnUtil($this->baseUrl, $this->io, $this->config, $this->process);
 $this->util->setCacheCredentials($this->cacheCredentials);
 }
 try {
 return $this->util->execute($command, $url);
 } catch (\RuntimeException $e) {
 if (null === $this->util->binaryVersion()) {
 throw new \RuntimeException('Failed to load '.$this->url.', svn was not found, check that it is installed and in your PATH env.' . "\n\n" . $this->process->getErrorOutput());
 }
 throw new \RuntimeException(
 'Repository '.$this->url.' could not be processed, '.$e->getMessage()
 );
 }
 }
 protected function buildIdentifier($baseDir, $revision)
 {
 return rtrim($baseDir, '/') . $this->packagePath . '/@' . $revision;
 }
}
