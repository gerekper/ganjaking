<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Intl\Languages;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
class Language extends Constraint
{
 public const NO_SUCH_LANGUAGE_ERROR = 'ee65fec4-9a20-4202-9f39-ca558cd7bdf7';
 protected static $errorNames = [self::NO_SUCH_LANGUAGE_ERROR => 'NO_SUCH_LANGUAGE_ERROR'];
 public $message = 'This value is not a valid language.';
 public function __construct($options = null)
 {
 if (!\class_exists(Languages::class)) {
 // throw new LogicException('The Intl component is required to use the Language constraint. Try running "composer require symfony/intl".');
 @\trigger_error(\sprintf('Using the "%s" constraint without the "symfony/intl" component installed is deprecated since Symfony 4.2.', __CLASS__), \E_USER_DEPRECATED);
 }
 parent::__construct($options);
 }
}
