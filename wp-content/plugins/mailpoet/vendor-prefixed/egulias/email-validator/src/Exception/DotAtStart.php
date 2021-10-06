<?php
 namespace MailPoetVendor\Egulias\EmailValidator\Exception; if (!defined('ABSPATH')) exit; class DotAtStart extends InvalidEmail { const CODE = 141; const REASON = "Found DOT at start"; } 