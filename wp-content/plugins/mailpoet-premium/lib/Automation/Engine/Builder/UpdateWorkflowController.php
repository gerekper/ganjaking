<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Builder\UpdateWorkflowController as FreePluginUpdateWorkflowController;
use MailPoet\Automation\Engine\Data\Workflow;
use MailPoet\Automation\Engine\Integration\Step;

class UpdateWorkflowController extends FreePluginUpdateWorkflowController {
  /** @param Step[] $steps */
  protected function validateWorkflowSteps(Workflow $workflow, array $steps): void {
    // allow full workflow eiditing
  }
}
