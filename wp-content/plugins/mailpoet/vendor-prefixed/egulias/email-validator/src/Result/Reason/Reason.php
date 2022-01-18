<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
interface Reason
{
 public function code() : int;
 public function description() : string;
}
