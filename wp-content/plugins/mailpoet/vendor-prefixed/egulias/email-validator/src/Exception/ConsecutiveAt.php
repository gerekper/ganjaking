<?php
 namespace MailPoetVendor\Egulias\EmailValidator\Exception; if (!defined('ABSPATH')) exit; class ConsecutiveAt extends InvalidEmail { const CODE = 128; const REASON = "Consecutive AT"; } 