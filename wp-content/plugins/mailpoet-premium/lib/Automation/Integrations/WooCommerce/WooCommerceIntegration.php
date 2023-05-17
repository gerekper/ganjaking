<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Premium\Automation\Integrations\WooCommerce\Triggers\AbandonedCart\AbandonedCartTrigger;

class WooCommerceIntegration implements Integration {

  /** @var AbandonedCartTrigger */
  private $abandonedCartTrigger;

  public function __construct(
    AbandonedCartTrigger $abandonedCartTrigger
  ) {
    $this->abandonedCartTrigger = $abandonedCartTrigger;
  }

  public function register(Registry $registry): void {
    $registry->addTrigger($this->abandonedCartTrigger);
  }
}
