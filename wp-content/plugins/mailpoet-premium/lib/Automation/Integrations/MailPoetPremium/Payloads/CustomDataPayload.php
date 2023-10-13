<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;

class CustomDataPayload implements Payload {

  /** @var string */
  private $hook;

  /** @var scalar[] */
  private $data;

  /**
   * @param scalar[] $data
   */
  public function __construct(
    string $hook,
    array $data
  ) {
    $this->hook = $hook;
    $this->data = $data;
  }

  /**
   * @return scalar[]
   */
  public function getData(): array {
    return $this->data;
  }

  public function getHook(): string {
    return $this->hook;
  }
}
