<?php
namespace MailPoetVendor\Egulias\EmailValidator\Result\Reason;
if (!defined('ABSPATH')) exit;
abstract class DetailedReason implements Reason
{
 protected $detailedDescription;
 public function __construct(string $details)
 {
 $this->detailedDescription = $details;
 }
}
