<?php
namespace MailPoetVendor\Egulias\EmailValidator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Validation\EmailValidation;
class EmailValidator
{
 private $lexer;
 private $warnings = [];
 private $error;
 public function __construct()
 {
 $this->lexer = new EmailLexer();
 }
 public function isValid(string $email, EmailValidation $emailValidation)
 {
 $isValid = $emailValidation->isValid($email, $this->lexer);
 $this->warnings = $emailValidation->getWarnings();
 $this->error = $emailValidation->getError();
 return $isValid;
 }
 public function hasWarnings()
 {
 return !empty($this->warnings);
 }
 public function getWarnings()
 {
 return $this->warnings;
 }
 public function getError()
 {
 return $this->error;
 }
}
