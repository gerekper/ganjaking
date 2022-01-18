<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
class Type extends Constraint
{
 public const INVALID_TYPE_ERROR = 'ba785a8c-82cb-4283-967c-3cf342181b40';
 protected static $errorNames = [self::INVALID_TYPE_ERROR => 'INVALID_TYPE_ERROR'];
 public $message = 'This value should be of type {{ type }}.';
 public $type;
 public function getDefaultOption()
 {
 return 'type';
 }
 public function getRequiredOptions()
 {
 return ['type'];
 }
}
