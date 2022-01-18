<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
interface Swift_InputByteStream
{
 public function write($bytes);
 public function commit();
 public function bind(self $is);
 public function unbind(self $is);
 public function flushBuffers();
}
