<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
class Json extends Constraint
{
 public const INVALID_JSON_ERROR = '0789c8ad-2d2b-49a4-8356-e2ce63998504';
 protected static $errorNames = [self::INVALID_JSON_ERROR => 'INVALID_JSON_ERROR'];
 public $message = 'This value should be valid JSON.';
}
