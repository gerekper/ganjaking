<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class UnableToGetDNSRecord extends NoDNSRecord
{
 public function code() : int
 {
 return 3;
 }
 public function description() : string
 {
 return 'Unable to get DNS records for the host';
 }
}
