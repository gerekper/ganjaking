<?php
 namespace MailPoetVendor; if (!defined('ABSPATH')) exit; interface Swift_Mime_EncodingObserver { public function encoderChanged(Swift_Mime_ContentEncoder $encoder); } 