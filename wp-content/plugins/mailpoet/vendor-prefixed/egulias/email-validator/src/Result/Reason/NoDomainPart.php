<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class NoDomainPart implements Reason
{
 public function code() : int
 {
 return 131;
 }
 public function description() : string
 {
 return 'No domain part found';
 }
}
