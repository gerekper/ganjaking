<?php
namespace MailPoetVendor\Carbon\Exceptions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\CarbonInterface;
use Exception;
use InvalidArgumentException as BaseInvalidArgumentException;
class NotACarbonClassException extends BaseInvalidArgumentException implements InvalidArgumentException
{
 public function __construct($className, $code = 0, Exception $previous = null)
 {
 parent::__construct(\sprintf('Given class does not implement %s: %s', CarbonInterface::class, $className), $code, $previous);
 }
}
