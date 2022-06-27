<?php

namespace MailPoet\Premium\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\API\API;
use MailPoet\Automation\Engine\Control\StepRunner;
use MailPoet\Automation\Engine\Hooks as AutomationHooks;
use MailPoet\Features\FeaturesController;
use MailPoet\Premium\Automation\Engine\Control\Steps\ConditionalStepRunner;
use MailPoet\Premium\Automation\Engine\Endpoints\Workflows\WorkflowsPostEndpoint;
use MailPoet\Premium\Automation\Engine\Workflows\ConditionalStep;
use MailPoet\Premium\Automation\Integrations\MailPoetPremium\MailPoetPremiumIntegration;
use MailPoet\WP\Functions as WPFunctions;

class Automation {
  /** @var MailPoetPremiumIntegration */
  private $mailpoetPremiumIntegration;

  /** @var ConditionalStepRunner */
  private $conditionalStepRunner;

  /** @var FeaturesController */
  private $featuresController;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    MailPoetPremiumIntegration $mailpoetPremiumIntegration,
    ConditionalStepRunner $conditionalStepRunner,
    FeaturesController $featuresController,
    WPFunctions $wp
  ) {
    $this->mailpoetPremiumIntegration = $mailpoetPremiumIntegration;
    $this->conditionalStepRunner = $conditionalStepRunner;
    $this->featuresController = $featuresController;
    $this->wp = $wp;
  }

  public function init() {
    if ($this->featuresController->isSupported(FeaturesController::AUTOMATION) || defined('MAILPOET_PREMIUM_TESTS_AUTOMATION')) {
      $this->wp->addAction(AutomationHooks::API_INITIALIZE, [
        $this,
        'registerPremiumAutomationAPIRoutes',
      ]);

      $this->wp->addAction(AutomationHooks::STEP_RUNNER_INITIALIZE, [
        $this,
        'registerPremiumStepRunners',
      ]);

      $this->wp->addAction(AutomationHooks::INITIALIZE, [
        $this->mailpoetPremiumIntegration,
        'register',
      ]);
    }
  }

  public function registerPremiumAutomationAPIRoutes(API $api) {
    $api->registerPostRoute('workflows', WorkflowsPostEndpoint::class);
  }

  public function registerPremiumStepRunners(StepRunner $stepRunner) {
    $stepRunner->addStepRunner(ConditionalStep::TYPE_CONDITIONAL, $this->conditionalStepRunner);
  }
}
