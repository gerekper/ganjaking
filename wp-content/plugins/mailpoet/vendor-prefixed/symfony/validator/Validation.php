<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
final class Validation
{
 public static function createValidator() : ValidatorInterface
 {
 return self::createValidatorBuilder()->getValidator();
 }
 public static function createValidatorBuilder() : ValidatorBuilder
 {
 return new ValidatorBuilder();
 }
 private function __construct()
 {
 }
}
