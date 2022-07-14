<?php
namespace Composer\Util\Http;
if (!defined('ABSPATH')) exit;
use Composer\Downloader\TransportException;
use Composer\Util\NoProxyPattern;
use Composer\Util\Url;
class ProxyManager
{
 private $error = null;
 private $fullProxy;
 private $safeProxy;
 private $streams;
 private $hasProxy;
 private $info = null;
 private $noProxyHandler = null;
 private static $instance = null;
 private function __construct()
 {
 $this->fullProxy = $this->safeProxy = array(
 'http' => null,
 'https' => null,
 );
 $this->streams['http'] = $this->streams['https'] = array(
 'options' => null,
 );
 $this->hasProxy = false;
 $this->initProxyData();
 }
 public static function getInstance()
 {
 if (!self::$instance) {
 self::$instance = new self();
 }
 return self::$instance;
 }
 public static function reset()
 {
 self::$instance = null;
 }
 public function getProxyForRequest($requestUrl)
 {
 if ($this->error) {
 throw new TransportException('Unable to use a proxy: '.$this->error);
 }
 $scheme = parse_url($requestUrl, PHP_URL_SCHEME) ?: 'http';
 $proxyUrl = '';
 $options = array();
 $formattedProxyUrl = '';
 if ($this->hasProxy && in_array($scheme, array('http', 'https'), true) && $this->fullProxy[$scheme]) {
 if ($this->noProxy($requestUrl)) {
 $formattedProxyUrl = 'excluded by no_proxy';
 } else {
 $proxyUrl = $this->fullProxy[$scheme];
 $options = $this->streams[$scheme]['options'];
 ProxyHelper::setRequestFullUri($requestUrl, $options);
 $formattedProxyUrl = $this->safeProxy[$scheme];
 }
 }
 return new RequestProxy($proxyUrl, $options, $formattedProxyUrl);
 }
 public function isProxying()
 {
 return $this->hasProxy;
 }
 public function getFormattedProxy()
 {
 return $this->hasProxy ? $this->info : $this->error;
 }
 private function initProxyData()
 {
 try {
 list($httpProxy, $httpsProxy, $noProxy) = ProxyHelper::getProxyData();
 } catch (\RuntimeException $e) {
 $this->error = $e->getMessage();
 return;
 }
 $info = array();
 if ($httpProxy) {
 $info[] = $this->setData($httpProxy, 'http');
 }
 if ($httpsProxy) {
 $info[] = $this->setData($httpsProxy, 'https');
 }
 if ($this->hasProxy) {
 $this->info = implode(', ', $info);
 if ($noProxy) {
 $this->noProxyHandler = new NoProxyPattern($noProxy);
 }
 }
 }
 private function setData($url, $scheme)
 {
 $safeProxy = Url::sanitize($url);
 $this->fullProxy[$scheme] = $url;
 $this->safeProxy[$scheme] = $safeProxy;
 $this->streams[$scheme]['options'] = ProxyHelper::getContextOptions($url);
 $this->hasProxy = true;
 return sprintf('%s=%s', $scheme, $safeProxy);
 }
 private function noProxy($requestUrl)
 {
 return $this->noProxyHandler && $this->noProxyHandler->test($requestUrl);
 }
}
