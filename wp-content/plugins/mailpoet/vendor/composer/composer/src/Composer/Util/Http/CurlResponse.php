<?php
namespace Composer\Util\Http;
if (!defined('ABSPATH')) exit;
class CurlResponse extends Response
{
 private $curlInfo;
 public function __construct(array $request, $code, array $headers, $body, array $curlInfo)
 {
 parent::__construct($request, $code, $headers, $body);
 $this->curlInfo = $curlInfo;
 }
 public function getCurlInfo()
 {
 return $this->curlInfo;
 }
}
