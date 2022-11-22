<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\API\API;
use MailPoet\Automation\Engine\Hooks as AutomationHooks;
use MailPoet\Premium\Automation\Engine\Endpoints\Automations\AutomationsPostEndpoint;
use MailPoet\Premium\Automation\Engine\Endpoints\Automations\AutomationsPutEndpoint;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\MailPoetPremiumIntegration;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\PremiumAutomationTemplates;
use MailPoet\WP\Functions as WPFunctions;

class Engine {
  /** @var MailPoetPremiumIntegration */
  private $mailpoetPremiumIntegration;

  /** @var PremiumAutomationTemplates  */
  private $templateStorage;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    MailPoetPremiumIntegration $mailpoetPremiumIntegration,
    PremiumAutomationTemplates $templateStorage,
    WPFunctions $wp
  ) {
    $this->mailpoetPremiumIntegration = $mailpoetPremiumIntegration;
    $this->templateStorage = $templateStorage;
    $this->wp = $wp;
  }

  public function initialize(): void {
    $this->wp->addAction(
      AutomationHooks::API_INITIALIZE,
      [$this, 'registerPremiumAutomationAPIRoutes'],
      5 // register premium routes before the free ones to replace the same ones
    );

    $this->wp->addAction(AutomationHooks::INITIALIZE, [
      $this->mailpoetPremiumIntegration,
      'register',
    ]);

    $this->wp->addAction(AutomationHooks::AUTOMATION_TEMPLATES, [
      $this->templateStorage,
      'integrate',
    ]);
  }

  public function registerPremiumAutomationAPIRoutes(API $api): void {
    $api->registerPostRoute('automations', AutomationsPostEndpoint::class);
    $api->registerPutRoute('automations/(?P<id>\d+)', AutomationsPutEndpoint::class);
  }
}
