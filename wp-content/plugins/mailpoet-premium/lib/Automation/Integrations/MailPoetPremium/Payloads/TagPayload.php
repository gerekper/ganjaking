<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Entities\TagEntity;
use MailPoet\InvalidStateException;

class TagPayload implements Payload {
  /** @var TagEntity */
  private $tag;

  public function __construct(
    TagEntity $tag
  ) {
    $this->tag = $tag;
  }

  public function getId(): int {
    $id = $this->tag->getId();
    if (!$id) {
      throw new InvalidStateException();
    }
    return $id;
  }

  public function getName(): string {
    return $this->tag->getName();
  }
}
