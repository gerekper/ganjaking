<?php
 namespace MailPoetVendor; if (!defined('ABSPATH')) exit; if (!\interface_exists('JsonSerializable')) { interface JsonSerializable { public function jsonSerialize(); } } 