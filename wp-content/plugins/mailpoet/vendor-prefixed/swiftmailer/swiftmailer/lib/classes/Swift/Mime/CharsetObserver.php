<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
interface Swift_Mime_CharsetObserver
{
 public function charsetChanged($charset);
}
