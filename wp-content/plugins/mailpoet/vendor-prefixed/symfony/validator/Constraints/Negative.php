<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class Negative extends LessThan
{
 use NumberConstraintTrait;
 public $message = 'This value should be negative.';
 public function __construct($options = null)
 {
 parent::__construct($this->configureNumberConstraintOptions($options));
 }
 public function validatedBy() : string
 {
 return LessThanValidator::class;
 }
}
