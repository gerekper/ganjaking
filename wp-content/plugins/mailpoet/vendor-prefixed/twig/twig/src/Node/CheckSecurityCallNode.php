<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
class CheckSecurityCallNode extends Node
{
 public function compile(Compiler $compiler)
 {
 $compiler->write("\$this->sandbox = \$this->env->getExtension('\\Twig\\Extension\\SandboxExtension');\n")->write("\$this->checkSecurity();\n");
 }
}
