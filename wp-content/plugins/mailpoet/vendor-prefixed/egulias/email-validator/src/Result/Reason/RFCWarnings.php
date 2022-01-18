<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class RFCWarnings implements Reason
{
 public function code() : int
 {
 return 997;
 }
 public function description() : string
 {
 return 'Warnings found after validating';
 }
}
