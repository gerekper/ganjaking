<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Endpoints\Workflows;

if (!defined('ABSPATH')) exit;


use MailPoet\API\REST\Request;
use MailPoet\API\REST\Response;
use MailPoet\Automation\Engine\Endpoints\Workflows\WorkflowsPutEndpoint as FreePluginWorkflowsPutEndpoint;
use MailPoet\Automation\Engine\Mappers\WorkflowMapper;
use MailPoet\Premium\Automation\Engine\Builder\UpdateWorkflowController;

class WorkflowsPutEndpoint extends FreePluginWorkflowsPutEndpoint {
  /** @var UpdateWorkflowController */
  private $updateController;

  /** @var WorkflowMapper */
  private $workflowMapper;

  public function __construct(
    UpdateWorkflowController $updateController,
    WorkflowMapper $workflowMapper
  ) {
    $this->updateController = $updateController;
    $this->workflowMapper = $workflowMapper;
  }

  public function handle(Request $request): Response {
    $data = $request->getParams();
    $workflow = $this->updateController->updateWorkflow(intval($request->getParam('id')), $data);
    return new Response($this->workflowMapper->buildWorkflow($workflow));
  }
}
