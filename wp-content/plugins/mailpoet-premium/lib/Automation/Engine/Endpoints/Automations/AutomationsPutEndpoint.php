<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Endpoints\Automations;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\Endpoints\Automations\AutomationsPutEndpoint as FreePluginAutomationsPutEndpoint;
use MailPoet\Automation\Engine\Mappers\AutomationMapper;
use MailPoet\Premium\Automation\Engine\Builder\UpdateAutomationController;

class AutomationsPutEndpoint extends FreePluginAutomationsPutEndpoint {
  /** @var UpdateAutomationController */
  private $updateController;

  /** @var AutomationMapper */
  private $automationMapper;

  public function __construct(
    UpdateAutomationController $updateController,
    AutomationMapper $automationMapper
  ) {
    $this->updateController = $updateController;
    $this->automationMapper = $automationMapper;
  }

  public function handle(Request $request): Response {
    $data = $request->getParams();
    $automation = $this->updateController->updateAutomation(intval($request->getParam('id')), $data);
    return new Response($this->automationMapper->buildAutomation($automation));
  }
}
