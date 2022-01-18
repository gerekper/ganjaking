<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result;
if (!defined('ABSPATH')) exit;
interface Result
{
 public function isValid() : bool;
 public function isInvalid() : bool;
 public function description() : string;
 public function code() : int;
}
