<?php
namespace MailPoetVendor\Egulias\EmailValidator\Validation\Exception;
if (!defined('ABSPATH')) exit;
use Exception;
class EmptyValidationList extends \InvalidArgumentException
{
 public function __construct($code = 0, Exception $previous = null)
 {
 parent::__construct("Empty validation list is not allowed", $code, $previous);
 }
}
