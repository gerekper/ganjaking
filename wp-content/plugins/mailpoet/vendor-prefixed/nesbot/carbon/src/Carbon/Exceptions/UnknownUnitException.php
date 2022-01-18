<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use Exception;
class UnknownUnitException extends UnitException
{
 public function __construct($unit, $code = 0, Exception $previous = null)
 {
 parent::__construct("Unknown unit '{$unit}'.", $code, $previous);
 }
}
