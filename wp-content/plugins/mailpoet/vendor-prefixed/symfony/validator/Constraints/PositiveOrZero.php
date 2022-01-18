<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class PositiveOrZero extends GreaterThanOrEqual
{
 use NumberConstraintTrait;
 public $message = 'This value should be either positive or zero.';
 public function __construct($options = null)
 {
 parent::__construct($this->configureNumberConstraintOptions($options));
 }
 public function validatedBy() : string
 {
 return GreaterThanOrEqualValidator::class;
 }
}
