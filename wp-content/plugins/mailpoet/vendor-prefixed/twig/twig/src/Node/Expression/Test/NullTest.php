<?php
namespace MailPoetVendor\Twig\Node\Expression\Test;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\TestExpression;
class NullTest extends TestExpression
{
 public function compile(Compiler $compiler)
 {
 $compiler->raw('(null === ')->subcompile($this->getNode('node'))->raw(')');
 }
}
\class_alias('MailPoetVendor\\Twig\\Node\\Expression\\Test\\NullTest', 'MailPoetVendor\\Twig_Node_Expression_Test_Null');
