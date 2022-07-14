<?php
namespace Composer\SelfUpdate;
if (!defined('ABSPATH')) exit;
use Composer\IO\IOInterface;
use Composer\Pcre\Preg;
use Composer\Util\HttpDownloader;
use Composer\Config;
class Versions
{
 public static $channels = array('stable', 'preview', 'snapshot', '1', '2', '2.2');
 private $httpDownloader;
 private $config;
 private $channel;
 private $versionsData = null;
 public function __construct(Config $config, HttpDownloader $httpDownloader)
 {
 $this->httpDownloader = $httpDownloader;
 $this->config = $config;
 }
 public function getChannel()
 {
 if ($this->channel) {
 return $this->channel;
 }
 $channelFile = $this->config->get('home').'/update-channel';
 if (file_exists($channelFile)) {
 $channel = trim(file_get_contents($channelFile));
 if (in_array($channel, array('stable', 'preview', 'snapshot', '2.2'), true)) {
 return $this->channel = $channel;
 }
 }
 return $this->channel = 'stable';
 }
 public function setChannel($channel, IOInterface $io = null)
 {
 if (!in_array($channel, self::$channels, true)) {
 throw new \InvalidArgumentException('Invalid channel '.$channel.', must be one of: ' . implode(', ', self::$channels));
 }
 $channelFile = $this->config->get('home').'/update-channel';
 $this->channel = $channel;
 $storedChannel = Preg::isMatch('{^\d+$}D', $channel) ? 'stable' : $channel;
 $previouslyStored = file_exists($channelFile) ? trim((string) file_get_contents($channelFile)) : null;
 // rewrite '2' and '1' channels to stable for future self-updates, but LTS ones like '2.2' remain pinned
 file_put_contents($channelFile, $storedChannel.PHP_EOL);
 if ($io !== null && $previouslyStored !== $storedChannel) {
 $io->writeError('Storing "<info>'.$storedChannel.'</info>" as default update channel for the next self-update run.');
 }
 }
 public function getLatest($channel = null)
 {
 $versions = $this->getVersionsData();
 foreach ($versions[$channel ?: $this->getChannel()] as $version) {
 if ($version['min-php'] <= PHP_VERSION_ID) {
 return $version;
 }
 }
 throw new \UnexpectedValueException('There is no version of Composer available for your PHP version ('.PHP_VERSION.')');
 }
 private function getVersionsData()
 {
 if (null === $this->versionsData) {
 if ($this->config->get('disable-tls') === true) {
 $protocol = 'http';
 } else {
 $protocol = 'https';
 }
 $this->versionsData = $this->httpDownloader->get($protocol . '://getcomposer.org/versions')->decodeJson();
 }
 return $this->versionsData;
 }
}
