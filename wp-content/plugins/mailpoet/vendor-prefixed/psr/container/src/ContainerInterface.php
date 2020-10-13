<?php
 namespace MailPoetVendor\Psr\Container; if (!defined('ABSPATH')) exit; interface ContainerInterface { public function get($id); public function has($id); } 