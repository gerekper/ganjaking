<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Workflows\Trigger;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use MailPoet\WP\Functions as WPFunctions;

class UserRegistrationTrigger implements Trigger {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function getKey(): string {
    return 'mailpoet:user:registration';
  }

  public function getName(): string {
    return __('WP user registration', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object();
  }

  public function registerHooks(): void {
    $this->wp->addAction('mailpoet_user_registered', [$this, 'handleSubscription'], 10, 2);
  }

  public function handleSubscription(SubscriberEntity $subscriber): void {
    $this->wp->doAction(Hooks::TRIGGER, $this, [
      [
        'key' => SubscriberSubject::KEY,
        'args' => [
          'subscriber_id' => $subscriber->getId(),
        ],
      ],
    ]);
  }
}
