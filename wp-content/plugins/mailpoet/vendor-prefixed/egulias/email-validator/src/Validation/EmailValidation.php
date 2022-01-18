<?php
namespace MailPoetVendor\Egulias\EmailValidator\Validation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\EmailLexer;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Warning\Warning;
interface EmailValidation
{
 public function isValid(string $email, EmailLexer $emailLexer) : bool;
 public function getError() : ?InvalidEmail;
 public function getWarnings() : array;
}
