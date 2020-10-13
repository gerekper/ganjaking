<?php
 namespace MailPoetVendor; if (!defined('ABSPATH')) exit; interface Swift_ReplacementFilterFactory { public function createFilter($search, $replace); } 