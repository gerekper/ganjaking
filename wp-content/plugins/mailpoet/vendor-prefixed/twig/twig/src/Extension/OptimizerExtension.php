<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\NodeVisitor\OptimizerNodeVisitor;
final class OptimizerExtension extends AbstractExtension
{
 private $optimizers;
 public function __construct($optimizers = -1)
 {
 $this->optimizers = $optimizers;
 }
 public function getNodeVisitors()
 {
 return [new OptimizerNodeVisitor($this->optimizers)];
 }
}
\class_alias('MailPoetVendor\\Twig\\Extension\\OptimizerExtension', 'MailPoetVendor\\Twig_Extension_Optimizer');
