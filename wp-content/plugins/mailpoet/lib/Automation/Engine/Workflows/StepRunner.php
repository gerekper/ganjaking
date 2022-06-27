<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Workflows;

if (!defined('ABSPATH')) exit;


interface StepRunner {
  public function run(Step $step, Workflow $workflow, WorkflowRun $workflowRun): void;
}
