<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;

class WooCommerceSubscriptionPayload implements Payload {

  /** @var \WC_Subscription */
  private $subscription;

  public function __construct(
    \WC_Subscription $subscription
  ) {
    $this->subscription = $subscription;
  }

  public function getId(): int {
    return $this->subscription->get_id();
  }

  public function getSubscription(): \WC_Subscription {
    return $this->subscription;
  }
}
