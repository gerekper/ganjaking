<?php
namespace Composer\Semver\Constraint;
if (!defined('ABSPATH')) exit;
class Bound
{
 private $version;
 private $isInclusive;
 public function __construct($version, $isInclusive)
 {
 $this->version = $version;
 $this->isInclusive = $isInclusive;
 }
 public function getVersion()
 {
 return $this->version;
 }
 public function isInclusive()
 {
 return $this->isInclusive;
 }
 public function isZero()
 {
 return $this->getVersion() === '0.0.0.0-dev' && $this->isInclusive();
 }
 public function isPositiveInfinity()
 {
 return $this->getVersion() === PHP_INT_MAX.'.0.0.0' && !$this->isInclusive();
 }
 public function compareTo(Bound $other, $operator)
 {
 if (!\in_array($operator, array('<', '>'), true)) {
 throw new \InvalidArgumentException('Does not support any other operator other than > or <.');
 }
 // If they are the same it doesn't matter
 if ($this == $other) {
 return false;
 }
 $compareResult = version_compare($this->getVersion(), $other->getVersion());
 // Not the same version means we don't need to check if the bounds are inclusive or not
 if (0 !== $compareResult) {
 return (('>' === $operator) ? 1 : -1) === $compareResult;
 }
 // Question we're answering here is "am I higher than $other?"
 return '>' === $operator ? $other->isInclusive() : !$other->isInclusive();
 }
 public function __toString()
 {
 return sprintf(
 '%s [%s]',
 $this->getVersion(),
 $this->isInclusive() ? 'inclusive' : 'exclusive'
 );
 }
 public static function zero()
 {
 return new Bound('0.0.0.0-dev', true);
 }
 public static function positiveInfinity()
 {
 return new Bound(PHP_INT_MAX.'.0.0.0', false);
 }
}
