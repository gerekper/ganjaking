<?php
namespace Composer\DependencyResolver\Operation;
if (!defined('ABSPATH')) exit;
abstract class SolverOperation implements OperationInterface
{
 const TYPE = null;
 public function getOperationType()
 {
 return static::TYPE;
 }
 public function __toString()
 {
 return $this->show(false);
 }
}
