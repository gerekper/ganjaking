<?php
 namespace MailPoetVendor; if (!defined('ABSPATH')) exit; interface Swift_Plugins_Logger { public function add($entry); public function clear(); public function dump(); } 