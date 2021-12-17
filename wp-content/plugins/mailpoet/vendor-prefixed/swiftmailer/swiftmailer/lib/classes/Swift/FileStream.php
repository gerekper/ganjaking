<?php
 namespace MailPoetVendor; if (!defined('ABSPATH')) exit; interface Swift_FileStream extends Swift_OutputByteStream { public function getPath(); } 