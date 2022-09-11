<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Workflows;

if (!defined('ABSPATH')) exit;


use MailPoet\Validator\Schema\ObjectSchema;

interface Step {
  public function getKey(): string;

  public function getName(): string;

  public function getArgsSchema(): ObjectSchema;
}
