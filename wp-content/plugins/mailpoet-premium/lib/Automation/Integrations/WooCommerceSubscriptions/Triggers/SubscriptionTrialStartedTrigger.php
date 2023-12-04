<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\CustomerSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderSubject;
use MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Subjects\WooCommerceSubscriptionSubject;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

class SubscriptionTrialStartedTrigger implements Trigger {


  const KEY = 'woocommerce-subscriptions:trial-started';

  /** @var WordPress */
  private $wp;

  public function __construct(
    WordPress $wp
  ) {
    $this->wp = $wp;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Woo Subscription trial started', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object();
  }

  public function getSubjectKeys(): array {
    return [
      WooCommerceSubscriptionSubject::KEY,
      OrderSubject::KEY,
      CustomerSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
  }

  public function registerHooks(): void {
    $this->wp->addAction(
      'woocommerce_checkout_create_subscription',
      [
        $this,
        'handle',
      ]
    );
  }

  /**
   * @param \WC_Subscription $subscription
   * @return void
   */
  public function handle($subscription) {
    if (!$subscription instanceof \WC_Subscription || !$this->isSubscriptionWithTrial($subscription)) {
      return;
    }
    $orderId = $subscription->get_last_order() ?? 0;
    $this->wp->doAction(Hooks::TRIGGER, $this, [
      new Subject(WooCommerceSubscriptionSubject::KEY, ['subscription_id' => $subscription->get_id()]),
      new Subject(OrderSubject::KEY, ['order_id' => $orderId]),
      new Subject(CustomerSubject::KEY, ['customer_id' => $subscription->get_customer_id()]),
    ]);
  }

  private function isSubscriptionWithTrial(\WC_Subscription $subscription): bool {
    $date = $subscription->get_date('trial_end');
    return $date !== 0;
  }

  public function isTriggeredBy(StepRunArgs $args): bool {
    return true;
  }
}
