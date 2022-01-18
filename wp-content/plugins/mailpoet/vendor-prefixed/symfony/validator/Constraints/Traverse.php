<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\ConstraintDefinitionException;
class Traverse extends Constraint
{
 public $traverse = \true;
 public function __construct($options = null)
 {
 if (\is_array($options) && \array_key_exists('groups', $options)) {
 throw new ConstraintDefinitionException(\sprintf('The option "groups" is not supported by the constraint "%s".', __CLASS__));
 }
 parent::__construct($options);
 }
 public function getDefaultOption()
 {
 return 'traverse';
 }
 public function getTargets()
 {
 return self::CLASS_CONSTRAINT;
 }
}
