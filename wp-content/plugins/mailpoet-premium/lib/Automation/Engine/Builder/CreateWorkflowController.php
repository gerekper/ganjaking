<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Data\Workflow;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Storage\WorkflowStorage;
use MailPoet\Automation\Engine\Validation\WorkflowValidator;

class CreateWorkflowController {
  /** @var WorkflowStorage */
  private $storage;

  /** @var WorkflowValidator */
  private $workflowValidator;

  public function __construct(
    WorkflowStorage $storage,
    WorkflowValidator $workflowValidator
  ) {
    $this->storage = $storage;
    $this->workflowValidator = $workflowValidator;
  }

  public function createWorkflow(array $data): Workflow {
    $steps = [];
    foreach ($data['steps'] as $index => $step) {
      $steps[(string)$index] = Step::fromArray($step);
    }

    $workflow = new Workflow($data['name'], $steps, wp_get_current_user());
    $this->workflowValidator->validate($workflow);
    $workflowId = $this->storage->createWorkflow($workflow);
    $workflow = $this->storage->getWorkflow($workflowId);
    if (!$workflow) {
      throw new InvalidStateException("Could not find workflow $workflowId");
    }
    return $workflow;
  }
}
