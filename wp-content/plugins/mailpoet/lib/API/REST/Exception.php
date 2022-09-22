<?php declare(strict_types = 1);

namespace MailPoet\API\REST;

if (!defined('ABSPATH')) exit;


interface Exception {
  public function getStatusCode();

  public function getErrorCode(): string;

  public function getErrors(): array;
}
