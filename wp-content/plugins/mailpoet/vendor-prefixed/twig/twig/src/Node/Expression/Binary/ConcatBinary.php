<?php
namespace MailPoetVendor\Twig\Node\Expression\Binary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class ConcatBinary extends AbstractBinary
{
 public function operator(Compiler $compiler)
 {
 return $compiler->raw('.');
 }
}
\class_alias('MailPoetVendor\\Twig\\Node\\Expression\\Binary\\ConcatBinary', 'MailPoetVendor\\Twig_Node_Expression_Binary_Concat');
