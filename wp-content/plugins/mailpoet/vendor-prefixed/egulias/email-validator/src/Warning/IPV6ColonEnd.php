<?php
namespace MailPoetVendor\Egulias\EmailValidator\Warning;
if (!defined('ABSPATH')) exit;
class IPV6ColonEnd extends Warning
{
 const CODE = 77;
 public function __construct()
 {
 $this->message = ':: found at the end of the domain literal';
 $this->rfcNumber = 5322;
 }
}
