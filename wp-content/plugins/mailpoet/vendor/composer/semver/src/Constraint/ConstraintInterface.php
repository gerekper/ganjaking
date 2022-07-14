<?php
namespace Composer\Semver\Constraint;
if (!defined('ABSPATH')) exit;
interface ConstraintInterface
{
 public function matches(ConstraintInterface $provider);
 public function compile($otherOperator);
 public function getUpperBound();
 public function getLowerBound();
 public function getPrettyString();
 public function setPrettyString($prettyString);
 public function __toString();
}
