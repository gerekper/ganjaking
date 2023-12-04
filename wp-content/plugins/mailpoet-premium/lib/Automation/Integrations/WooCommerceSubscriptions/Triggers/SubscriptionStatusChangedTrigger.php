<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\CustomerSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderSubject;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Payloads\WooCommerceSubscriptionStatusChangePayload;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Subjects\WooCommerceSubscriptionStatusChangeSubject;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Subjects\WooCommerceSubscriptionSubject;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use MailPoet\WP\Functions;

class SubscriptionStatusChangedTrigger implements Trigger {

  public const KEY = 'woocommerce-subscriptions:subscription-status-changed';

  /** @var Functions */
  private $wp;

  public function __construct(
    Functions $wp
  ) {
    $this->wp = $wp;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Woo Subscription status changed', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'from' => Builder::string()->required()->default('any'),
      'to' => Builder::string()->required()->default('any'),
    ]);
  }

  public function getSubjectKeys(): array {
    return [
      WooCommerceSubscriptionSubject::KEY,
      OrderSubject::KEY,
      WooCommerceSubscriptionStatusChangeSubject::KEY,
      CustomerSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
  }

  public function registerHooks(): void {
    $this->wp->addAction(
      'woocommerce_subscription_status_updated',
      [
        $this,
        'handle',
      ],
      10,
      3
    );
  }

  /**
   * @param \WC_Subscription $subscription
   * @param string $newStatus
   * @param string $oldStatus
   * @return void
   */
  public function handle($subscription, string $newStatus, string $oldStatus): void {
    if (!$subscription instanceof \WC_Subscription) {
      return;
    }
    $orderId = $subscription->get_last_order() ?? 0;
    $this->wp->doAction(Hooks::TRIGGER, $this, [
      new Subject(WooCommerceSubscriptionStatusChangeSubject::KEY, ['from' => $oldStatus, 'to' => $newStatus]),
      new Subject(WooCommerceSubscriptionSubject::KEY, ['subscription_id' => $subscription->get_id()]),
      new Subject(OrderSubject::KEY, ['order_id' => $orderId]),
      new Subject(CustomerSubject::KEY, ['customer_id' => $subscription->get_customer_id()]),
    ]);
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    $subscriptionChangePayload = $args->getSinglePayloadByClass(WooCommerceSubscriptionStatusChangePayload::class);
    $triggerArgs = $args->getStep()->getArgs();
    $configuredFrom = $triggerArgs['from'] ? str_replace('wc-', '', $triggerArgs['from']) : null;
    $configuredTo = $triggerArgs['to'] ? str_replace('wc-', '', $triggerArgs['to']) : null;
    if ($configuredFrom !== 'any' && $subscriptionChangePayload->getFrom() !== $configuredFrom) {
      return false;
    }
    if ($configuredTo !== 'any' && $subscriptionChangePayload->getTo() !== $configuredTo) {
      return false;
    }
    return true;
  }
}
