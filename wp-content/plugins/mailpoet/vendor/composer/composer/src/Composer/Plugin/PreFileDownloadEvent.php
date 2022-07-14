<?php
namespace Composer\Plugin;
if (!defined('ABSPATH')) exit;
use Composer\EventDispatcher\Event;
use Composer\Util\HttpDownloader;
class PreFileDownloadEvent extends Event
{
 private $httpDownloader;
 private $processedUrl;
 private $customCacheKey;
 private $type;
 private $context;
 private $transportOptions = array();
 public function __construct($name, HttpDownloader $httpDownloader, $processedUrl, $type, $context = null)
 {
 parent::__construct($name);
 $this->httpDownloader = $httpDownloader;
 $this->processedUrl = $processedUrl;
 $this->type = $type;
 $this->context = $context;
 }
 public function getHttpDownloader()
 {
 return $this->httpDownloader;
 }
 public function getProcessedUrl()
 {
 return $this->processedUrl;
 }
 public function setProcessedUrl($processedUrl)
 {
 $this->processedUrl = $processedUrl;
 }
 public function getCustomCacheKey()
 {
 return $this->customCacheKey;
 }
 public function setCustomCacheKey($customCacheKey)
 {
 $this->customCacheKey = $customCacheKey;
 }
 public function getType()
 {
 return $this->type;
 }
 public function getContext()
 {
 return $this->context;
 }
 public function getTransportOptions()
 {
 return $this->transportOptions;
 }
 public function setTransportOptions(array $options)
 {
 $this->transportOptions = $options;
 }
}
