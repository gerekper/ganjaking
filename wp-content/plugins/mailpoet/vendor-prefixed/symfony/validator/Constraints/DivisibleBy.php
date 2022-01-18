<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
class DivisibleBy extends AbstractComparison
{
 public const NOT_DIVISIBLE_BY = '6d99d6c3-1464-4ccf-bdc7-14d083cf455c';
 protected static $errorNames = [self::NOT_DIVISIBLE_BY => 'NOT_DIVISIBLE_BY'];
 public $message = 'This value should be a multiple of {{ compared_value }}.';
}
