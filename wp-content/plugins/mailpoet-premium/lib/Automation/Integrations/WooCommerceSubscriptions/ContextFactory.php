<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerceSubscriptions;

if (!defined('ABSPATH')) exit;


use MailPoet\WooCommerce\WooCommerceSubscriptions\Helper as WCS;

class ContextFactory {


  /** @var WCS */
  private $wcs;

  public function __construct(
    WCS $wcs
  ) {
    $this->wcs = $wcs;
  }

  /**
   * @return mixed[]
   */
  public function getContextData(): array {
    if (!$this->wcs->isWooCommerceSubscriptionsActive()) {
      return [];
    }
    return [
      'subscription_statuses' => $this->getSubscriptionStatuses(),
    ];
  }

  /**
   * @return array<int, array<string,string>>
   */
  private function getSubscriptionStatuses(): array {
    $statuses = $this->wcs->wcsGetSubscriptionStatuses();
    return array_map(
      function($label, $value) {
        return [
          'label' => $label,
          'value' => $value,
        ];
      }, $statuses, array_keys($statuses),
    );
  }
}
