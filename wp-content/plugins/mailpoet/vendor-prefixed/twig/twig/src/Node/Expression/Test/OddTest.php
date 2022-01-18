<?php
namespace MailPoetVendor\Twig\Node\Expression\Test;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Node\Expression\TestExpression;
class OddTest extends TestExpression
{
 public function compile(Compiler $compiler)
 {
 $compiler->raw('(')->subcompile($this->getNode('node'))->raw(' % 2 != 0')->raw(')');
 }
}
\class_alias('MailPoetVendor\\Twig\\Node\\Expression\\Test\\OddTest', 'MailPoetVendor\\Twig_Node_Expression_Test_Odd');
