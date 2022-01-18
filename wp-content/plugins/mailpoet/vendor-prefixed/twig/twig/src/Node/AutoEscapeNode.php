<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class AutoEscapeNode extends Node
{
 public function __construct($value, Node $body, int $lineno, string $tag = 'autoescape')
 {
 parent::__construct(['body' => $body], ['value' => $value], $lineno, $tag);
 }
 public function compile(Compiler $compiler)
 {
 $compiler->subcompile($this->getNode('body'));
 }
}
\class_alias('MailPoetVendor\\Twig\\Node\\AutoEscapeNode', 'MailPoetVendor\\Twig_Node_AutoEscape');
