<?php
namespace Composer\Package;
if (!defined('ABSPATH')) exit;
use Composer\Package\Version\VersionParser;
use Composer\Pcre\Preg;
use Composer\Util\ComposerMirror;
class Package extends BasePackage
{
 protected $type;
 protected $targetDir;
 protected $installationSource;
 protected $sourceType;
 protected $sourceUrl;
 protected $sourceReference;
 protected $sourceMirrors;
 protected $distType;
 protected $distUrl;
 protected $distReference;
 protected $distSha1Checksum;
 protected $distMirrors;
 protected $version;
 protected $prettyVersion;
 protected $releaseDate;
 protected $extra = array();
 protected $binaries = array();
 protected $dev;
 protected $stability;
 protected $notificationUrl;
 protected $requires = array();
 protected $conflicts = array();
 protected $provides = array();
 protected $replaces = array();
 protected $devRequires = array();
 protected $suggests = array();
 protected $autoload = array();
 protected $devAutoload = array();
 protected $includePaths = array();
 protected $isDefaultBranch = false;
 protected $transportOptions = array();
 public function __construct($name, $version, $prettyVersion)
 {
 parent::__construct($name);
 $this->version = $version;
 $this->prettyVersion = $prettyVersion;
 $this->stability = VersionParser::parseStability($version);
 $this->dev = $this->stability === 'dev';
 }
 public function isDev()
 {
 return $this->dev;
 }
 public function setType($type)
 {
 $this->type = $type;
 }
 public function getType()
 {
 return $this->type ?: 'library';
 }
 public function getStability()
 {
 return $this->stability;
 }
 public function setTargetDir($targetDir)
 {
 $this->targetDir = $targetDir;
 }
 public function getTargetDir()
 {
 if (null === $this->targetDir) {
 return null;
 }
 return ltrim(Preg::replace('{ (?:^|[\\\\/]+) \.\.? (?:[\\\\/]+|$) (?:\.\.? (?:[\\\\/]+|$) )*}x', '/', $this->targetDir), '/');
 }
 public function setExtra(array $extra)
 {
 $this->extra = $extra;
 }
 public function getExtra()
 {
 return $this->extra;
 }
 public function setBinaries(array $binaries)
 {
 $this->binaries = $binaries;
 }
 public function getBinaries()
 {
 return $this->binaries;
 }
 public function setInstallationSource($type)
 {
 $this->installationSource = $type;
 }
 public function getInstallationSource()
 {
 return $this->installationSource;
 }
 public function setSourceType($type)
 {
 $this->sourceType = $type;
 }
 public function getSourceType()
 {
 return $this->sourceType;
 }
 public function setSourceUrl($url)
 {
 $this->sourceUrl = $url;
 }
 public function getSourceUrl()
 {
 return $this->sourceUrl;
 }
 public function setSourceReference($reference)
 {
 $this->sourceReference = $reference;
 }
 public function getSourceReference()
 {
 return $this->sourceReference;
 }
 public function setSourceMirrors($mirrors)
 {
 $this->sourceMirrors = $mirrors;
 }
 public function getSourceMirrors()
 {
 return $this->sourceMirrors;
 }
 public function getSourceUrls()
 {
 return $this->getUrls($this->sourceUrl, $this->sourceMirrors, $this->sourceReference, $this->sourceType, 'source');
 }
 public function setDistType($type)
 {
 $this->distType = $type;
 }
 public function getDistType()
 {
 return $this->distType;
 }
 public function setDistUrl($url)
 {
 $this->distUrl = $url;
 }
 public function getDistUrl()
 {
 return $this->distUrl;
 }
 public function setDistReference($reference)
 {
 $this->distReference = $reference;
 }
 public function getDistReference()
 {
 return $this->distReference;
 }
 public function setDistSha1Checksum($sha1checksum)
 {
 $this->distSha1Checksum = $sha1checksum;
 }
 public function getDistSha1Checksum()
 {
 return $this->distSha1Checksum;
 }
 public function setDistMirrors($mirrors)
 {
 $this->distMirrors = $mirrors;
 }
 public function getDistMirrors()
 {
 return $this->distMirrors;
 }
 public function getDistUrls()
 {
 return $this->getUrls($this->distUrl, $this->distMirrors, $this->distReference, $this->distType, 'dist');
 }
 public function getTransportOptions()
 {
 return $this->transportOptions;
 }
 public function setTransportOptions(array $options)
 {
 $this->transportOptions = $options;
 }
 public function getVersion()
 {
 return $this->version;
 }
 public function getPrettyVersion()
 {
 return $this->prettyVersion;
 }
 public function setReleaseDate(\DateTime $releaseDate)
 {
 $this->releaseDate = $releaseDate;
 }
 public function getReleaseDate()
 {
 return $this->releaseDate;
 }
 public function setRequires(array $requires)
 {
 if (isset($requires[0])) { // @phpstan-ignore-line
 $requires = $this->convertLinksToMap($requires, 'setRequires');
 }
 $this->requires = $requires;
 }
 public function getRequires()
 {
 return $this->requires;
 }
 public function setConflicts(array $conflicts)
 {
 if (isset($conflicts[0])) { // @phpstan-ignore-line
 $conflicts = $this->convertLinksToMap($conflicts, 'setConflicts');
 }
 $this->conflicts = $conflicts;
 }
 public function getConflicts()
 {
 return $this->conflicts;
 }
 public function setProvides(array $provides)
 {
 if (isset($provides[0])) { // @phpstan-ignore-line
 $provides = $this->convertLinksToMap($provides, 'setProvides');
 }
 $this->provides = $provides;
 }
 public function getProvides()
 {
 return $this->provides;
 }
 public function setReplaces(array $replaces)
 {
 if (isset($replaces[0])) { // @phpstan-ignore-line
 $replaces = $this->convertLinksToMap($replaces, 'setReplaces');
 }
 $this->replaces = $replaces;
 }
 public function getReplaces()
 {
 return $this->replaces;
 }
 public function setDevRequires(array $devRequires)
 {
 if (isset($devRequires[0])) { // @phpstan-ignore-line
 $devRequires = $this->convertLinksToMap($devRequires, 'setDevRequires');
 }
 $this->devRequires = $devRequires;
 }
 public function getDevRequires()
 {
 return $this->devRequires;
 }
 public function setSuggests(array $suggests)
 {
 $this->suggests = $suggests;
 }
 public function getSuggests()
 {
 return $this->suggests;
 }
 public function setAutoload(array $autoload)
 {
 $this->autoload = $autoload;
 }
 public function getAutoload()
 {
 return $this->autoload;
 }
 public function setDevAutoload(array $devAutoload)
 {
 $this->devAutoload = $devAutoload;
 }
 public function getDevAutoload()
 {
 return $this->devAutoload;
 }
 public function setIncludePaths(array $includePaths)
 {
 $this->includePaths = $includePaths;
 }
 public function getIncludePaths()
 {
 return $this->includePaths;
 }
 public function setNotificationUrl($notificationUrl)
 {
 $this->notificationUrl = $notificationUrl;
 }
 public function getNotificationUrl()
 {
 return $this->notificationUrl;
 }
 public function setIsDefaultBranch($defaultBranch)
 {
 $this->isDefaultBranch = $defaultBranch;
 }
 public function isDefaultBranch()
 {
 return $this->isDefaultBranch;
 }
 public function setSourceDistReferences($reference)
 {
 $this->setSourceReference($reference);
 // only bitbucket, github and gitlab have auto generated dist URLs that easily allow replacing the reference in the dist URL
 // TODO generalize this a bit for self-managed/on-prem versions? Some kind of replace token in dist urls which allow this?
 if (
 $this->getDistUrl() !== null
 && Preg::isMatch('{^https?://(?:(?:www\.)?bitbucket\.org|(api\.)?github\.com|(?:www\.)?gitlab\.com)/}i', $this->getDistUrl())
 ) {
 $this->setDistReference($reference);
 $this->setDistUrl(Preg::replace('{(?<=/|sha=)[a-f0-9]{40}(?=/|$)}i', $reference, $this->getDistUrl()));
 } elseif ($this->getDistReference()) { // update the dist reference if there was one, but if none was provided ignore it
 $this->setDistReference($reference);
 }
 }
 public function replaceVersion($version, $prettyVersion)
 {
 $this->version = $version;
 $this->prettyVersion = $prettyVersion;
 $this->stability = VersionParser::parseStability($version);
 $this->dev = $this->stability === 'dev';
 }
 protected function getUrls($url, $mirrors, $ref, $type, $urlType)
 {
 if (!$url) {
 return array();
 }
 if ($urlType === 'dist' && false !== strpos($url, '%')) {
 $url = ComposerMirror::processUrl($url, $this->name, $this->version, $ref, $type, $this->prettyVersion);
 }
 $urls = array($url);
 if ($mirrors) {
 foreach ($mirrors as $mirror) {
 if ($urlType === 'dist') {
 $mirrorUrl = ComposerMirror::processUrl($mirror['url'], $this->name, $this->version, $ref, $type, $this->prettyVersion);
 } elseif ($urlType === 'source' && $type === 'git') {
 $mirrorUrl = ComposerMirror::processGitUrl($mirror['url'], $this->name, $url, $type);
 } elseif ($urlType === 'source' && $type === 'hg') {
 $mirrorUrl = ComposerMirror::processHgUrl($mirror['url'], $this->name, $url, $type);
 } else {
 continue;
 }
 if (!\in_array($mirrorUrl, $urls)) {
 $func = $mirror['preferred'] ? 'array_unshift' : 'array_push';
 $func($urls, $mirrorUrl);
 }
 }
 }
 return $urls;
 }
 private function convertLinksToMap(array $links, $source)
 {
 trigger_error('Package::'.$source.' must be called with a map of lowercased package name => Link object, got a indexed array, this is deprecated and you should fix your usage.');
 $newLinks = array();
 foreach ($links as $link) {
 $newLinks[$link->getTarget()] = $link;
 }
 return $newLinks;
 }
}
