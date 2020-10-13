<?php
 namespace MailPoetVendor; if (!defined('ABSPATH')) exit; interface Swift_AddressEncoder { public function encodeString(string $address) : string; } 