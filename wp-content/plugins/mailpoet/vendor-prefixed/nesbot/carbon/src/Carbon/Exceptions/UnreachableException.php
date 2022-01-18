<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use Exception;
use RuntimeException as BaseRuntimeException;
class UnreachableException extends BaseRuntimeException implements RuntimeException
{
 public function __construct($message, $code = 0, Exception $previous = null)
 {
 parent::__construct($message, $code, $previous);
 }
}
