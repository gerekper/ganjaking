<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Endpoints\Workflows;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\API\Endpoint;
use MailPoet\Automation\Engine\Mappers\WorkflowMapper;
use MailPoet\Automation\Engine\Validation\WorkflowSchema;
use MailPoet\Premium\Automation\Engine\Builder\CreateWorkflowController;
use MailPoet\Validator\Builder;

class WorkflowsPostEndpoint extends Endpoint {
  /** @var CreateWorkflowController */
  private $createController;

  /** @var WorkflowMapper */
  private $workflowMapper;

  public function __construct(
    CreateWorkflowController $createController,
    WorkflowMapper $workflowMapper
  ) {
    $this->createController = $createController;
    $this->workflowMapper = $workflowMapper;
  }

  public function handle(Request $request): Response {
    $data = $request->getParams();
    $workflow = $this->createController->createWorkflow($data);
    return new Response($this->workflowMapper->buildWorkflow($workflow));
  }

  public static function getRequestSchema(): array {
    return [
      'name' => Builder::string()->required(),
      'steps' => WorkflowSchema::getStepsSchema(),
    ];
  }
}
