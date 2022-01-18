<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class ExceptionFound implements Reason
{
 private $exception;
 public function __construct(\Exception $exception)
 {
 $this->exception = $exception;
 }
 public function code() : int
 {
 return 999;
 }
 public function description() : string
 {
 return $this->exception->getMessage();
 }
}
