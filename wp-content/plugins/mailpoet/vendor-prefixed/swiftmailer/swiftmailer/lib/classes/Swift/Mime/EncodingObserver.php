<?php
 namespace MailPoetVendor; if (!defined('ABSPATH')) exit; interface Swift_Mime_EncodingObserver { public function encoderChanged(\MailPoetVendor\Swift_Mime_ContentEncoder $encoder); } 