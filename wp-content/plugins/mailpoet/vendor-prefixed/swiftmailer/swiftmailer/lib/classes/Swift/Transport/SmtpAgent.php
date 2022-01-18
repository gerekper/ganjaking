<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
interface Swift_Transport_SmtpAgent
{
 public function getBuffer();
 public function executeCommand($command, $codes = [], &$failures = null);
}
