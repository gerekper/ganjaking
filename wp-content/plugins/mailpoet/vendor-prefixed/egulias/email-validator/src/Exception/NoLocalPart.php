<?php
 namespace MailPoetVendor\Egulias\EmailValidator\Exception; if (!defined('ABSPATH')) exit; class NoLocalPart extends InvalidEmail { const CODE = 130; const REASON = "No local part"; } 