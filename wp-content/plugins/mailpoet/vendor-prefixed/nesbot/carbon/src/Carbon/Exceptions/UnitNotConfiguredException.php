<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use Exception;
class UnitNotConfiguredException extends UnitException
{
 public function __construct($unit, $code = 0, Exception $previous = null)
 {
 parent::__construct("Unit {$unit} have no configuration to get total from other units.", $code, $previous);
 }
}
