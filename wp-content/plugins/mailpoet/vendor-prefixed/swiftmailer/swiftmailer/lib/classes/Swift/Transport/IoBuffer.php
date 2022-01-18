<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
interface Swift_Transport_IoBuffer extends Swift_InputByteStream, Swift_OutputByteStream
{
 const TYPE_SOCKET = 0x1;
 const TYPE_PROCESS = 0x10;
 public function initialize(array $params);
 public function setParam($param, $value);
 public function terminate();
 public function setWriteTranslations(array $replacements);
 public function readLine($sequence);
}
