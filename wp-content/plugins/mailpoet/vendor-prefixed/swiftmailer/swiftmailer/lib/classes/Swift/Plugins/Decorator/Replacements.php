<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
interface Swift_Plugins_Decorator_Replacements
{
 public function getReplacementsFor($address);
}
