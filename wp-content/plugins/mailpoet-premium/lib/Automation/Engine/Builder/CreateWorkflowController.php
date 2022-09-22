<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Data\Workflow;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Storage\WorkflowStorage;

class CreateWorkflowController {
  /** @var WorkflowStorage */
  private $storage;

  public function __construct(
    WorkflowStorage $storage
  ) {
    $this->storage = $storage;
  }

  public function createWorkflow(array $data): Workflow {
    // TODO: data & workflow validation (trigger existence, graph consistency, etc.)
    $steps = [];
    foreach ($data['steps'] as $step) {
      $steps[] = Step::fromArray($step);
    }
    $workflow = new Workflow($data['name'], $steps, wp_get_current_user());
    $workflowId = $this->storage->createWorkflow($workflow);
    $workflow = $this->storage->getWorkflow($workflowId);
    if (!$workflow) {
      throw new InvalidStateException("Could not find workflow $workflowId");
    }
    return $workflow;
  }
}
