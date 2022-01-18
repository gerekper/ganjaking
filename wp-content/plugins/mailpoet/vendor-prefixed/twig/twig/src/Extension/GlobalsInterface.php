<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
interface GlobalsInterface
{
 public function getGlobals();
}
\class_alias('MailPoetVendor\\Twig\\Extension\\GlobalsInterface', 'MailPoetVendor\\Twig_Extension_GlobalsInterface');
