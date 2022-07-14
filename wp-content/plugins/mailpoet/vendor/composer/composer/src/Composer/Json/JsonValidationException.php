<?php
namespace Composer\Json;
if (!defined('ABSPATH')) exit;
use Exception;
class JsonValidationException extends Exception
{
 protected $errors;
 public function __construct($message, $errors = array(), Exception $previous = null)
 {
 $this->errors = $errors;
 parent::__construct((string) $message, 0, $previous);
 }
 public function getErrors()
 {
 return $this->errors;
 }
}
