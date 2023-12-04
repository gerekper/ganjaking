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
use MailPoet\WooCommerce\WooCommerceSubscriptions\Helper as WCS;

class SubscriptionCreatedTrigger implements Trigger {

  public const KEY = 'woocommerce-subscriptions:subscription-created';

  /** @var WordPress */
  private $wp;

  /** @var WCS */
  private $wcs;

  public function __construct(
    WordPress $wp,
    WCS $wcs
  ) {
    $this->wp = $wp;
    $this->wcs = $wcs;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Woo Subscription started', 'mailpoet-premium');
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
        'handleNewCreated',
      ]
    );

    $this->wp->addAction(
      'woocommerce_subscription_trial_ended',
      [
        $this,
        'handleTrialEnded',
      ]
    );
  }

  /**
   * @param \WC_Subscription $subscription
   * @return void
   */
  public function handleNewCreated($subscription): void {
    if (!$subscription instanceof \WC_Subscription || $this->isSubscriptionWithTrial($subscription)) {
      return;
    }
    $this->fireEvent($subscription);
  }

  /**
   * @param int $subscriptionId
   * @return void
   */
  public function handleTrialEnded($subscriptionId): void {
    $subscription = $this->wcs->wcsGetSubscription($subscriptionId);
    if (!$subscription instanceof \WC_Subscription) {
      return;
    }
    $this->fireEvent($subscription);
  }

  private function fireEvent(\WC_Subscription $subscription): void {
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

    // The decision to trigger or not is already made in the hook handling methods
    return true;
  }
}
