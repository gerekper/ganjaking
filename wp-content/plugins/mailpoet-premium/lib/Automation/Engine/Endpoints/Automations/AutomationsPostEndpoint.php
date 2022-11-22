<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Endpoints\Automations;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Mappers\AutomationMapper;
use MailPoet\Automation\Engine\Validation\AutomationSchema;
use MailPoet\Premium\Automation\Engine\Builder\CreateAutomationController;
use MailPoet\Validator\Builder;

class AutomationsPostEndpoint extends Endpoint {
  /** @var CreateAutomationController */
  private $createController;

  /** @var AutomationMapper */
  private $automationMapper;

  public function __construct(
    CreateAutomationController $createController,
    AutomationMapper $automationMapper
  ) {
    $this->createController = $createController;
    $this->automationMapper = $automationMapper;
  }

  public function handle(Request $request): Response {
    $data = $request->getParams();
    $automation = $this->createController->createAutomation($data);
    return new Response($this->automationMapper->buildAutomation($automation));
  }

  public static function getRequestSchema(): array {
    return [
      'name' => Builder::string()->required(),
      'steps' => AutomationSchema::getStepsSchema(),
    ];
  }
}
