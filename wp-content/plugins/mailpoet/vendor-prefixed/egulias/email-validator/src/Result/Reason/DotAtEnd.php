<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class DotAtEnd implements Reason
{
 public function code() : int
 {
 return 142;
 }
 public function description() : string
 {
 return 'Dot at the end';
 }
}
