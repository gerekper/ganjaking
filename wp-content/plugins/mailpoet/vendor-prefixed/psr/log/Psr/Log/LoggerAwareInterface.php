<?php
 namespace MailPoetVendor\Psr\Log; if (!defined('ABSPATH')) exit; interface LoggerAwareInterface { public function setLogger(\MailPoetVendor\Psr\Log\LoggerInterface $logger); } 