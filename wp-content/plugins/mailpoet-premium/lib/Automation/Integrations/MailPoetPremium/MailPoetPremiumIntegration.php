<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\UnsubscribeAction;

class MailPoetPremiumIntegration implements Integration {

  /** @var UnsubscribeAction */
  private $unsubscribeAction;

  public function __construct(
    UnsubscribeAction $unsubscribeAction
  ) {
    $this->unsubscribeAction = $unsubscribeAction;
  }

  public function register(Registry $registry): void {
    $registry->addAction($this->unsubscribeAction);
  }
}
