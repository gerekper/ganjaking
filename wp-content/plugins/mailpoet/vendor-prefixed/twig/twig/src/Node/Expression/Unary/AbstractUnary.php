<?php
namespace MailPoetVendor\Twig\Node\Expression\Unary;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\AbstractExpression;
use MailPoetVendor\Twig\Node\Node;
abstract class AbstractUnary extends AbstractExpression
{
 public function __construct(Node $node, int $lineno)
 {
 parent::__construct(['node' => $node], [], $lineno);
 }
 public function compile(Compiler $compiler)
 {
 $compiler->raw(' ');
 $this->operator($compiler);
 $compiler->subcompile($this->getNode('node'));
 }
 public abstract function operator(Compiler $compiler);
}
\class_alias('MailPoetVendor\\Twig\\Node\\Expression\\Unary\\AbstractUnary', 'MailPoetVendor\\Twig_Node_Expression_Unary');
