<?php
namespace MailPoetVendor\Twig\Node\Expression\Binary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class ModBinary extends AbstractBinary
{
 public function operator(Compiler $compiler)
 {
 return $compiler->raw('%');
 }
}
\class_alias('MailPoetVendor\\Twig\\Node\\Expression\\Binary\\ModBinary', 'MailPoetVendor\\Twig_Node_Expression_Binary_Mod');
