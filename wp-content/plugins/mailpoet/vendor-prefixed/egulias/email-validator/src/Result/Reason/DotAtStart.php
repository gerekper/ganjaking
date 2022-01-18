<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class DotAtStart implements Reason
{
 public function code() : int
 {
 return 141;
 }
 public function description() : string
 {
 return "Starts with a DOT";
 }
}
