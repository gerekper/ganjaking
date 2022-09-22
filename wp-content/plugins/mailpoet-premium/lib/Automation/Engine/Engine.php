<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\API\API;
use MailPoet\Automation\Engine\Hooks as AutomationHooks;
use MailPoet\Features\FeaturesController;
use MailPoet\Premium\Automation\Engine\Endpoints\Workflows\WorkflowsPostEndpoint;
use MailPoet\Premium\Automation\Engine\Endpoints\Workflows\WorkflowsPutEndpoint;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\MailPoetPremiumIntegration;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\PremiumWorkflowTemplates;
use MailPoet\WP\Functions as WPFunctions;

class Engine {
  /** @var MailPoetPremiumIntegration */
  private $mailpoetPremiumIntegration;

  /** @var FeaturesController */
  private $featuresController;

  /** @var PremiumWorkflowTemplates  */
  private $templateStorage;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    MailPoetPremiumIntegration $mailpoetPremiumIntegration,
    FeaturesController $featuresController,
    PremiumWorkflowTemplates $templateStorage,
    WPFunctions $wp
  ) {
    $this->mailpoetPremiumIntegration = $mailpoetPremiumIntegration;
    $this->featuresController = $featuresController;
    $this->templateStorage = $templateStorage;
    $this->wp = $wp;
  }

  public function initialize(): void {
    if (!$this->featuresController->isSupported(FeaturesController::AUTOMATION) && !defined('MAILPOET_PREMIUM_TESTS_AUTOMATION')) {
      return;
    }

    $this->wp->addAction(
      AutomationHooks::API_INITIALIZE,
      [$this, 'registerPremiumAutomationAPIRoutes'],
      5 // register premium routes before the free ones to replace the same ones
    );

    $this->wp->addAction(AutomationHooks::INITIALIZE, [
      $this->mailpoetPremiumIntegration,
      'register',
    ]);

    $this->wp->addAction(AutomationHooks::WORKFLOW_TEMPLATES, [
      $this->templateStorage,
      'integrate',
    ]);
  }

  public function registerPremiumAutomationAPIRoutes(API $api): void {
    $api->registerPostRoute('workflows', WorkflowsPostEndpoint::class);
    $api->registerPutRoute('workflows/(?P<id>\d+)', WorkflowsPutEndpoint::class);
  }
}
