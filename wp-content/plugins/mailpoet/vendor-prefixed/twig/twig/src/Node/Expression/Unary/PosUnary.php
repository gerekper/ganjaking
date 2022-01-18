<?php
namespace MailPoetVendor\Twig\Node\Expression\Unary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class PosUnary extends AbstractUnary
{
 public function operator(Compiler $compiler)
 {
 $compiler->raw('+');
 }
}
\class_alias('MailPoetVendor\\Twig\\Node\\Expression\\Unary\\PosUnary', 'MailPoetVendor\\Twig_Node_Expression_Unary_Pos');
