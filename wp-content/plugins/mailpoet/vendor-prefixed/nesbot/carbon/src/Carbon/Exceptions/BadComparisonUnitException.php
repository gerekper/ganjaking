<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use Exception;
class BadComparisonUnitException extends UnitException
{
 public function __construct($unit, $code = 0, Exception $previous = null)
 {
 parent::__construct("Bad comparison unit: '{$unit}'", $code, $previous);
 }
}
