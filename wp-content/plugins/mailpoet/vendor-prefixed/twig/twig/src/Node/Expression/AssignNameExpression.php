<?php
namespace MailPoetVendor\Twig\Node\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class AssignNameExpression extends NameExpression
{
 public function compile(Compiler $compiler)
 {
 $compiler->raw('$context[')->string($this->getAttribute('name'))->raw(']');
 }
}
\class_alias('MailPoetVendor\\Twig\\Node\\Expression\\AssignNameExpression', 'MailPoetVendor\\Twig_Node_Expression_AssignName');
