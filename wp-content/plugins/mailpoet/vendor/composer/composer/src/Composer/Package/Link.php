<?php
namespace Composer\Package;
if (!defined('ABSPATH')) exit;
use Composer\Semver\Constraint\ConstraintInterface;
class Link
{
 const TYPE_REQUIRE = 'requires';
 const TYPE_DEV_REQUIRE = 'devRequires';
 const TYPE_PROVIDE = 'provides';
 const TYPE_CONFLICT = 'conflicts';
 const TYPE_REPLACE = 'replaces';
 const TYPE_DOES_NOT_REQUIRE = 'does not require';
 const TYPE_UNKNOWN = 'relates to';
 public static $TYPES = array(
 self::TYPE_REQUIRE,
 self::TYPE_DEV_REQUIRE,
 self::TYPE_PROVIDE,
 self::TYPE_CONFLICT,
 self::TYPE_REPLACE,
 );
 protected $source;
 protected $target;
 protected $constraint;
 protected $description;
 protected $prettyConstraint;
 public function __construct(
 $source,
 $target,
 ConstraintInterface $constraint,
 $description = self::TYPE_UNKNOWN,
 $prettyConstraint = null
 ) {
 $this->source = strtolower($source);
 $this->target = strtolower($target);
 $this->constraint = $constraint;
 $this->description = self::TYPE_DEV_REQUIRE === $description ? 'requires (for development)' : $description;
 $this->prettyConstraint = $prettyConstraint;
 }
 public function getDescription()
 {
 return $this->description;
 }
 public function getSource()
 {
 return $this->source;
 }
 public function getTarget()
 {
 return $this->target;
 }
 public function getConstraint()
 {
 return $this->constraint;
 }
 public function getPrettyConstraint()
 {
 if (null === $this->prettyConstraint) {
 throw new \UnexpectedValueException(sprintf('Link %s has been misconfigured and had no prettyConstraint given.', $this));
 }
 return $this->prettyConstraint;
 }
 public function __toString()
 {
 return $this->source.' '.$this->description.' '.$this->target.' ('.$this->constraint.')';
 }
 public function getPrettyString(PackageInterface $sourcePackage)
 {
 return $sourcePackage->getPrettyString().' '.$this->description.' '.$this->target.' '.$this->constraint->getPrettyString();
 }
}
