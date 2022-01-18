<?php
namespace MailPoetVendor\Twig\Util;
if (!defined('ABSPATH')) exit;
class TemplateDirIterator extends \IteratorIterator
{
 #[\ReturnTypeWillChange]
 public function current()
 {
 return \file_get_contents(parent::current());
 }
 #[\ReturnTypeWillChange]
 public function key()
 {
 return (string) parent::key();
 }
}
\class_alias('MailPoetVendor\\Twig\\Util\\TemplateDirIterator', 'MailPoetVendor\\Twig_Util_TemplateDirIterator');
