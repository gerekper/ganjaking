<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use Exception;
use InvalidArgumentException as BaseInvalidArgumentException;
class UnknownGetterException extends BaseInvalidArgumentException implements InvalidArgumentException
{
 public function __construct($name, $code = 0, Exception $previous = null)
 {
 parent::__construct("Unknown getter '{$name}'", $code, $previous);
 }
}
