<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
interface Swift_StreamFilter
{
 public function shouldBuffer($buffer);
 public function filter($buffer);
}
