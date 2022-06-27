<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Workflows\Subject;
use MailPoet\Automation\Engine\Workflows\Trigger;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\WP\Functions as WPFunctions;

class UserRegistrationTrigger implements Trigger {
  /** @var SubscriberSubject */
  private $subscriberSubject;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SubscriberSubject $subscriberSubject,
    WPFunctions $wp
  ) {
    $this->subscriberSubject = $subscriberSubject;
    $this->wp = $wp;
  }

  public function getKey(): string {
    return 'mailpoet:user:registration';
  }

  public function getName(): string {
    return __('WP user registration');
  }

  /**
   * @return Subject[]
   */
  public function getSubjects(): array {
    return [
      $this->subscriberSubject,
    ];
  }

  public function registerHooks(): void {
    $this->wp->addAction('mailpoet_user_registered', [$this, 'handleSubscription'], 10, 2);
  }

  public function handleSubscription(SubscriberEntity $subscriber): void {
    $this->subscriberSubject->load(['subscriber_id' => $subscriber->getId()]);

    $this->wp->doAction(Hooks::TRIGGER, $this, [
      $this->subscriberSubject,
    ]);
  }
}
