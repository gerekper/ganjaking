<?php
 namespace MailPoetVendor\Monolog\Processor; if (!defined('ABSPATH')) exit; interface ProcessorInterface { public function __invoke(array $records); } 