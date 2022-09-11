<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Data\Workflow;
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
      $steps[] = new Step(
        $step['id'],
        $step['type'],
        $step['key'],
        $step['next_step_id'] ?? null,
        $step['args'] ?? []
      );
    }
    $workflow = new Workflow($data['name'], $steps, wp_get_current_user());

    $this->storage->createWorkflow($workflow);
    return $workflow;
  }
}
