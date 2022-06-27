<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions\UnsubscribeAction;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers\UserRegistrationTrigger;

class MailPoetPremiumIntegration implements Integration {
  /** @var UserRegistrationTrigger */
  private $userRegistrationTrigger;

  /** @var UnsubscribeAction */
  private $unsubscribeAction;

  public function __construct(
    UserRegistrationTrigger $userRegistrationTrigger,
    UnsubscribeAction $unsubscribeAction
  ) {
    $this->userRegistrationTrigger = $userRegistrationTrigger;
    $this->unsubscribeAction = $unsubscribeAction;
  }

  public function register(Registry $registry): void {
    $registry->addTrigger($this->userRegistrationTrigger);
    $registry->addAction($this->unsubscribeAction);
  }
}
