<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Workflows;

if (!defined('ABSPATH')) exit;


interface Trigger extends Step {
  public function registerHooks(): void;
}
