<?php
namespace MailPoetVendor\Egulias\EmailValidator\Warning;
if (!defined('ABSPATH')) exit;
class DomainLiteral extends Warning
{
 const CODE = 70;
 public function __construct()
 {
 $this->message = 'Domain Literal';
 $this->rfcNumber = 5322;
 }
}
