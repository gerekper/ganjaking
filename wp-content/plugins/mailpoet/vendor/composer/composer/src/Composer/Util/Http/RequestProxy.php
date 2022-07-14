<?php
namespace Composer\Util\Http;
if (!defined('ABSPATH')) exit;
use Composer\Util\Url;
class RequestProxy
{
 private $contextOptions;
 private $isSecure;
 private $formattedUrl;
 private $url;
 public function __construct($url, array $contextOptions, $formattedUrl)
 {
 $this->url = $url;
 $this->contextOptions = $contextOptions;
 $this->formattedUrl = $formattedUrl;
 $this->isSecure = 0 === strpos($url, 'https://');
 }
 public function getContextOptions()
 {
 return $this->contextOptions;
 }
 public function getFormattedUrl($format = '')
 {
 $result = '';
 if ($this->formattedUrl) {
 $format = $format ?: '%s';
 $result = sprintf($format, $this->formattedUrl);
 }
 return $result;
 }
 public function getUrl()
 {
 return $this->url;
 }
 public function isSecure()
 {
 return $this->isSecure;
 }
}
