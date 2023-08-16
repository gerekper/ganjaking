<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\API;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Endpoints\OrderEndpoint;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\Analytics\Endpoints\SubscriberEndpoint;

class Analytics {
  /** @var WordPress */
  private $wordPress;

  public function __construct(
    WordPress $wordPress
  ) {
    $this->wordPress = $wordPress;
  }

  public function register(): void {
    $this->wordPress->addAction(Hooks::API_INITIALIZE, function (API $api) {
      $api->registerGetRoute('automation/analytics/orders', OrderEndpoint::class);
      $api->registerGetRoute('automation/analytics/subscribers', SubscriberEndpoint::class);
    });
  }
}
