<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Workflows;

if (!defined('ABSPATH')) exit;


interface Subject {
  public function getKey(): string;

  /** array<SubjectField> */
  public function getFields(): array;

  public function load(array $args): void;

  public function pack(): array;
}
