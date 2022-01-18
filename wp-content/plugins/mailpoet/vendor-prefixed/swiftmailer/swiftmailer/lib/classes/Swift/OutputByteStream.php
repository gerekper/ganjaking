<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
interface Swift_OutputByteStream
{
 public function read($length);
 public function setReadPointer($byteOffset);
}
