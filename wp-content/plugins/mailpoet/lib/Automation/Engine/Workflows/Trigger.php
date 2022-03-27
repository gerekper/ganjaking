<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Workflows;

if (!defined('ABSPATH')) exit;


interface Trigger {
  public function getKey(): string;

  public function getName(): string;

  public function registerHooks(): void;
}
