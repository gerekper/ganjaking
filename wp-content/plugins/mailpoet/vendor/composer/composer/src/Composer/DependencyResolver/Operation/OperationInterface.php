<?php
namespace Composer\DependencyResolver\Operation;
if (!defined('ABSPATH')) exit;
interface OperationInterface
{
 public function getOperationType();
 public function show($lock);
 public function __toString();
}
