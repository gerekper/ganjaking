<?php
namespace Composer\Downloader;
if (!defined('ABSPATH')) exit;
class TransportException extends \RuntimeException
{
 protected $headers;
 protected $response;
 protected $statusCode;
 protected $responseInfo = array();
 public function setHeaders($headers)
 {
 $this->headers = $headers;
 }
 public function getHeaders()
 {
 return $this->headers;
 }
 public function setResponse($response)
 {
 $this->response = $response;
 }
 public function getResponse()
 {
 return $this->response;
 }
 public function setStatusCode($statusCode)
 {
 $this->statusCode = $statusCode;
 }
 public function getStatusCode()
 {
 return $this->statusCode;
 }
 public function getResponseInfo()
 {
 return $this->responseInfo;
 }
 public function setResponseInfo(array $responseInfo)
 {
 $this->responseInfo = $responseInfo;
 }
}
