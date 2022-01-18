<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
class UnOpenedComment implements Reason
{
 public function code() : int
 {
 return 152;
 }
 public function description() : string
 {
 return 'Missing openning comment parentheses - https://tools.ietf.org/html/rfc5322#section-3.2.2';
 }
}
