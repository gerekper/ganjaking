<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Egulias\EmailValidator\Result\InvalidEmail;
use MailPoetVendor\Egulias\EmailValidator\Result\Reason\SpoofEmail as ReasonSpoofEmail;
class SpoofEmail extends InvalidEmail
{
 public function __construct()
 {
 $this->reason = new ReasonSpoofEmail();
 parent::__construct($this->reason, '');
 }
}
